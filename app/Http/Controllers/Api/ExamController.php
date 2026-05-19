<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamQuestion;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Daftar ujian untuk santri yang login
     */
    public function index(Request $request): JsonResponse
    {
        $student = $request->user()->student;

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.'], 404);
        }

        $exams = $student->exams()
            ->withPivot('status', 'score', 'total_points', 'started_at', 'finished_at', 'access_token')
            ->withCount('questions')
            ->orderBy('exam_date', 'desc')
            ->get()
            ->map(function ($exam) {
                $pivotStatus = $exam->pivot->status;
                $label = match (true) {
                    $pivotStatus === 'completed' => 'selesai',
                    $pivotStatus === 'missed' => 'terlewat',
                    $exam->status !== 'active' => 'terkunci',
                    $exam->isPast() => 'terlewat',
                    $exam->isOpen() && $pivotStatus === 'not_started' => 'tersedia',
                    $pivotStatus === 'in_progress' => 'tersedia',
                    default => 'terkunci',
                };

                return [
                    'id' => $exam->id,
                    'title' => $exam->title,
                    'subject' => $exam->subject,
                    'teacher_name' => $exam->teacher_name,
                    'exam_date' => $exam->exam_date->format('Y-m-d'),
                    'start_time' => $exam->start_time,
                    'end_time' => $exam->end_time,
                    'duration_minutes' => $exam->duration_minutes,
                    'questions_count' => $exam->questions_count,
                    'description' => $exam->description,
                    'status' => $label,
                    'score' => $pivotStatus === 'completed' ? $exam->pivot->score : null,
                    'total_points' => $pivotStatus === 'completed' ? $exam->pivot->total_points : null,
                    'started_at' => $exam->pivot->started_at,
                    'finished_at' => $exam->pivot->finished_at,
                    // Unique exam URL per student (untuk deep-link Flutter → WebView)
                    'exam_url' => $label === 'tersedia' ? url('/exams/join/' . $exam->pivot->access_token) : null,
                    'access_token' => $exam->pivot->access_token,
                ];
            });

        return response()->json(['success' => true, 'data' => $exams]);
    }

    /**
     * Mulai ujian — ambil soal (tanpa kunci jawaban!)
     */
    public function start(Request $request): JsonResponse
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
        ]);

        $student = $request->user()->student;
        $exam = Exam::with('questions')->findOrFail($request->exam_id);
        $pivot = $exam->students()->where('student_id', $student->id)->first();

        if (!$pivot) {
            return response()->json(['success' => false, 'message' => 'Anda tidak terdaftar.'], 403);
        }

        if ($exam->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'Ujian tidak aktif.'], 403);
        }

        if ($pivot->pivot->status === 'completed') {
            return response()->json(['success' => false, 'message' => 'Ujian sudah selesai.'], 403);
        }

        if ($exam->isPast()) {
            return response()->json(['success' => false, 'message' => 'Waktu ujian sudah berakhir.'], 403);
        }

        // Start if not started
        if ($pivot->pivot->status === 'not_started') {
            $exam->students()->updateExistingPivot($student->id, [
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        }

        $pivot = $exam->students()->where('student_id', $student->id)->first();
        $startedAt = Carbon::parse($pivot->pivot->started_at);
        $deadline = $startedAt->copy()->addMinutes($exam->duration_minutes);

        if (now()->isAfter($deadline)) {
            $this->autoGrade($exam, $student);
            return response()->json(['success' => false, 'message' => 'Waktu habis.'], 403);
        }

        $remainingSeconds = now()->diffInSeconds($deadline, false);

        $questions = $exam->questions;
        if ($exam->shuffle_questions) {
            $questions = $questions->shuffle();
        }

        // Load existing answers
        $existingAnswers = ExamAnswer::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->pluck('answer_text', 'exam_question_id');

        // SECURITY: Remove correct_answer from questions!
        $questionsData = $questions->map(fn($q) => [
            'id' => $q->id,
            'question_text' => $q->question_text,
            'question_type' => $q->question_type,
            'options' => $q->options, // only option text, no correct answer
            'points' => $q->points,
            'your_answer' => $existingAnswers[$q->id] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'exam' => [
                    'id' => $exam->id,
                    'title' => $exam->title,
                    'subject' => $exam->subject,
                    'duration_minutes' => $exam->duration_minutes,
                ],
                'remaining_seconds' => (int) $remainingSeconds,
                'access_token' => $pivot->pivot->access_token,
                'questions' => $questionsData,
            ],
        ]);
    }

    /**
     * Simpan jawaban (per soal)
     */
    public function saveAnswer(Request $request): JsonResponse
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'question_id' => 'required|exists:exam_questions,id',
            'answer_text' => 'nullable|string|max:5000',
            'access_token' => 'required|string',
        ]);

        $student = $request->user()->student;
        $exam = Exam::findOrFail($request->exam_id);
        $pivot = $exam->students()->where('student_id', $student->id)->first();

        if (!$pivot || $pivot->pivot->status !== 'in_progress') {
            return response()->json(['success' => false, 'message' => 'Ujian tidak berlangsung.'], 403);
        }

        if ($request->access_token !== $pivot->pivot->access_token) {
            return response()->json(['success' => false, 'message' => 'Token tidak valid.'], 403);
        }

        // Time check
        $startedAt = Carbon::parse($pivot->pivot->started_at);
        $deadline = $startedAt->copy()->addMinutes($exam->duration_minutes);
        if (now()->isAfter($deadline)) {
            $this->autoGrade($exam, $student);
            return response()->json(['success' => false, 'message' => 'Waktu habis.', 'timeout' => true], 403);
        }

        // Verify question belongs to exam
        ExamQuestion::where('id', $request->question_id)->where('exam_id', $exam->id)->firstOrFail();

        ExamAnswer::updateOrCreate(
            [
                'exam_id' => $exam->id,
                'student_id' => $student->id,
                'exam_question_id' => $request->question_id,
            ],
            ['answer_text' => $request->answer_text]
        );

        return response()->json(['success' => true, 'message' => 'Jawaban tersimpan.']);
    }

    /**
     * Submit / kumpulkan ujian
     */
    public function submit(Request $request): JsonResponse
    {
        $request->validate(['exam_id' => 'required|exists:exams,id']);

        $student = $request->user()->student;
        $exam = Exam::with('questions')->findOrFail($request->exam_id);
        $pivot = $exam->students()->where('student_id', $student->id)->first();

        if (!$pivot || $pivot->pivot->status !== 'in_progress') {
            return response()->json(['success' => false, 'message' => 'Ujian tidak berlangsung.'], 403);
        }

        $score = $this->autoGrade($exam, $student);

        return response()->json([
            'success' => true,
            'message' => 'Ujian berhasil dikumpulkan.',
            'data' => ['score' => $score],
        ]);
    }

    /**
     * Lihat hasil
     */
    public function result(Request $request): JsonResponse
    {
        $request->validate(['exam_id' => 'required|exists:exams,id']);

        $student = $request->user()->student;
        $exam = Exam::with('questions')->findOrFail($request->exam_id);
        $pivot = $exam->students()->where('student_id', $student->id)->first();

        if (!$pivot || $pivot->pivot->status !== 'completed') {
            return response()->json(['success' => false, 'message' => 'Hasil belum tersedia.'], 403);
        }

        $result = [
            'exam' => [
                'title' => $exam->title,
                'subject' => $exam->subject,
                'teacher_name' => $exam->teacher_name,
            ],
            'score' => $pivot->pivot->score,
            'total_points' => $pivot->pivot->total_points,
            'max_points' => $exam->questions->sum('points'),
            'started_at' => $pivot->pivot->started_at,
            'finished_at' => $pivot->pivot->finished_at,
        ];

        if ($exam->show_result) {
            $answers = ExamAnswer::where('exam_id', $exam->id)
                ->where('student_id', $student->id)
                ->with('question')
                ->get();

            $result['answers'] = $answers->map(fn($a) => [
                'question' => $a->question->question_text,
                'question_type' => $a->question->question_type,
                'your_answer' => $a->answer_text,
                'correct_answer' => $a->question->correct_answer,
                'is_correct' => $a->is_correct,
                'points_earned' => $a->points_earned,
                'max_points' => $a->question->points,
            ]);
        }

        return response()->json(['success' => true, 'data' => $result]);
    }

    // ===== PRIVATE =====

    private function autoGrade($exam, $student): float
    {
        $answers = ExamAnswer::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->get();

        $totalEarned = 0;
        $totalPoints = $exam->questions()->sum('points');

        foreach ($exam->questions as $question) {
            $answer = $answers->firstWhere('exam_question_id', $question->id);
            if (!$answer) continue;

            if ($question->question_type === 'multiple_choice') {
                $isCorrect = strtoupper(trim($answer->answer_text)) === strtoupper(trim($question->correct_answer));
                $pointsEarned = $isCorrect ? $question->points : 0;
                $answer->update([
                    'is_correct' => $isCorrect,
                    'points_earned' => $pointsEarned,
                ]);
                $totalEarned += $pointsEarned;
            }
        }

        $score = $totalPoints > 0 ? round(($totalEarned / $totalPoints) * 100, 2) : 0;

        $exam->students()->updateExistingPivot($student->id, [
            'status' => 'completed',
            'finished_at' => now(),
            'score' => $score,
            'total_points' => $totalEarned,
        ]);

        return $score;
    }
}
