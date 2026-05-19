<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamQuestion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Daftar ujian untuk wali santri / santri
     */
    public function index(Request $request)
    {
        $student = $request->user()->student;
        if (!$student) {
            return view('exams.index', ['exams' => collect()]);
        }

        $exams = $student->exams()
            ->withPivot('status', 'score', 'total_points', 'started_at', 'finished_at', 'access_token')
            ->withCount('questions')
            ->orderBy('exam_date', 'desc')
            ->get()
            ->map(function ($exam) {
                // Determine display status label
                $pivotStatus = $exam->pivot->status;
                $exam->display_label = match (true) {
                    $pivotStatus === 'completed' => 'selesai',
                    $pivotStatus === 'missed' => 'terlewat',
                    $exam->status !== 'active' => 'terkunci',
                    $exam->isPast() => 'terlewat',
                    $pivotStatus === 'in_progress' => $this->isTimedOut($exam) ? 'terlewat' : 'tersedia',
                    $exam->isOpen() && $pivotStatus === 'not_started' => 'tersedia',
                    default => 'terkunci',
                };
                return $exam;
            });

        return view('exams.index', compact('exams', 'student'));
    }

    /**
     * Join exam via unique access token (untuk Flutter / link external)
     * URL: /exams/join/{access_token}
     */
    public function joinByToken(Request $request, $accessToken)
    {
        // Cari pivot berdasarkan token
        $pivot = \DB::table('exam_student')
            ->where('access_token', $accessToken)
            ->first();

        if (!$pivot) {
            abort(403, 'Link ujian tidak valid atau sudah kadaluarsa.');
        }

        $exam = Exam::findOrFail($pivot->exam_id);
        $student = \App\Models\Student::findOrFail($pivot->student_id);

        // Validasi: ujian harus aktif
        if ($exam->status !== 'active') {
            return redirect('/exams')->with('error', 'Ujian belum/sudah tidak aktif.');
        }

        // Validasi: ujian belum lewat waktu
        if ($exam->isPast()) {
            return redirect('/exams')->with('error', 'Waktu ujian sudah berakhir.');
        }

        // Jika user belum login, auto-login sebagai wali santri
        if (!$student->user_id) {
            abort(403, 'Santri belum terdaftar. Silakan registrasi terlebih dahulu.');
        }

        $user = $student->user;
        if (!\Illuminate\Support\Facades\Auth::check()) {
            \Illuminate\Support\Facades\Auth::login($user);
        } else {
            // Pastikan user yang login = pemilik token
            $loggedInStudent = $request->user()->student;
            if (!$loggedInStudent || $loggedInStudent->id !== $student->id) {
                abort(403, 'Token ini bukan milik akun Anda.');
            }
        }

        // Cek status peserta
        if ($pivot->status === 'completed') {
            return redirect('/exams/' . $exam->id . '/result')->with('info', 'Anda sudah menyelesaikan ujian ini.');
        }
        if ($pivot->status === 'missed') {
            return redirect('/exams')->with('error', 'Waktu ujian sudah terlewat.');
        }

        // Jika sudah in_progress, langsung ke halaman ujian
        if ($pivot->status === 'in_progress') {
            return redirect('/exams/' . $exam->id . '/take');
        }

        // Belum mulai, arahkan ke halaman konfirmasi
        return redirect('/exams/' . $exam->id . '/start');
    }

    /**
     * Halaman mulai ujian (konfirmasi sebelum start)
     */
    public function start(Request $request, $examId)
    {
        $student = $request->user()->student;
        $exam = Exam::with('questions')->findOrFail($examId);
        $pivot = $exam->students()->where('student_id', $student->id)->first();

        if (!$pivot) abort(403, 'Anda tidak terdaftar sebagai peserta ujian ini.');
        if ($exam->status !== 'active') abort(403, 'Ujian belum/sudah tidak aktif.');
        if ($pivot->pivot->status === 'completed') abort(403, 'Anda sudah menyelesaikan ujian ini.');
        if ($pivot->pivot->status === 'missed') abort(403, 'Waktu ujian sudah terlewat.');
        if ($exam->isPast()) abort(403, 'Waktu ujian sudah berakhir.');

        return view('exams.start', compact('exam', 'pivot'));
    }

    /**
     * Mulai mengerjakan ujian - set started_at dan buka form
     */
    public function begin(Request $request, $examId)
    {
        $student = $request->user()->student;
        $exam = Exam::with('questions')->findOrFail($examId);
        $pivot = $exam->students()->where('student_id', $student->id)->first();

        if (!$pivot) abort(403);
        if ($exam->status !== 'active') abort(403, 'Ujian tidak aktif.');
        if ($pivot->pivot->status === 'completed') return redirect('/exams')->with('error', 'Ujian sudah selesai.');
        if ($exam->isPast()) abort(403, 'Waktu ujian sudah berakhir.');

        // Mulai ujian jika belum
        if ($pivot->pivot->status === 'not_started') {
            $exam->students()->updateExistingPivot($student->id, [
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        }

        // Reload pivot
        $pivot = $exam->students()->where('student_id', $student->id)->first();

        // Cek apakah waktu sudah habis
        $startedAt = Carbon::parse($pivot->pivot->started_at);
        $deadline = $startedAt->copy()->addMinutes($exam->duration_minutes);

        if (now()->isAfter($deadline)) {
            $this->autoSubmit($exam, $student);
            return redirect('/exams')->with('error', 'Waktu ujian sudah habis.');
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

        $accessToken = $pivot->pivot->access_token;

        return view('exams.take', compact('exam', 'questions', 'remainingSeconds', 'existingAnswers', 'accessToken'));
    }

    /**
     * Simpan jawaban (auto-save per soal via AJAX)
     */
    public function saveAnswer(Request $request, $examId)
    {
        $student = $request->user()->student;
        $exam = Exam::findOrFail($examId);
        $pivot = $exam->students()->where('student_id', $student->id)->first();

        if (!$pivot || $pivot->pivot->status !== 'in_progress') {
            return response()->json(['error' => 'Ujian tidak sedang berlangsung.'], 403);
        }

        // Verify access token
        if ($request->header('X-Exam-Token') !== $pivot->pivot->access_token) {
            return response()->json(['error' => 'Token tidak valid.'], 403);
        }

        // Server-side time check
        $startedAt = Carbon::parse($pivot->pivot->started_at);
        $deadline = $startedAt->copy()->addMinutes($exam->duration_minutes);
        if (now()->isAfter($deadline)) {
            $this->autoSubmit($exam, $student);
            return response()->json(['error' => 'Waktu habis.', 'timeout' => true], 403);
        }

        $request->validate([
            'question_id' => 'required|exists:exam_questions,id',
            'answer_text' => 'nullable|string|max:5000',
        ]);

        // Verify question belongs to this exam
        $question = ExamQuestion::where('id', $request->question_id)
            ->where('exam_id', $examId)
            ->firstOrFail();

        ExamAnswer::updateOrCreate(
            [
                'exam_id' => $examId,
                'student_id' => $student->id,
                'exam_question_id' => $request->question_id,
            ],
            [
                'answer_text' => $request->answer_text,
            ]
        );

        return response()->json(['success' => true, 'saved' => true]);
    }

    /**
     * Submit ujian (selesai)
     */
    public function submit(Request $request, $examId)
    {
        $student = $request->user()->student;
        $exam = Exam::with('questions')->findOrFail($examId);
        $pivot = $exam->students()->where('student_id', $student->id)->first();

        if (!$pivot || $pivot->pivot->status !== 'in_progress') {
            return redirect('/exams')->with('error', 'Ujian tidak sedang berlangsung.');
        }

        $this->gradeAndFinish($exam, $student);

        return redirect('/exams')->with('success', 'Ujian berhasil diselesaikan!');
    }

    /**
     * Lihat hasil ujian
     */
    public function result(Request $request, $examId)
    {
        $student = $request->user()->student;
        $exam = Exam::with('questions')->findOrFail($examId);
        $pivot = $exam->students()->where('student_id', $student->id)->first();

        if (!$pivot || $pivot->pivot->status !== 'completed') {
            return redirect('/exams')->with('error', 'Hasil ujian belum tersedia.');
        }

        if (!$exam->show_result) {
            return view('exams.result', [
                'exam' => $exam,
                'pivot' => $pivot,
                'answers' => collect(),
                'showDetail' => false,
            ]);
        }

        $answers = ExamAnswer::where('exam_id', $examId)
            ->where('student_id', $student->id)
            ->with('question')
            ->get();

        return view('exams.result', [
            'exam' => $exam,
            'pivot' => $pivot,
            'answers' => $answers,
            'showDetail' => true,
        ]);
    }

    // ===== PRIVATE HELPERS =====

    private function isTimedOut($exam): bool
    {
        if (!$exam->pivot->started_at) return false;
        $startedAt = Carbon::parse($exam->pivot->started_at);
        $deadline = $startedAt->copy()->addMinutes($exam->duration_minutes);
        return now()->isAfter($deadline);
    }

    private function autoSubmit($exam, $student)
    {
        $pivot = $exam->students()->where('student_id', $student->id)->first();
        if ($pivot && $pivot->pivot->status === 'in_progress') {
            $this->gradeAndFinish($exam, $student);
        }
    }

    private function gradeAndFinish($exam, $student)
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
            // Essay: is_correct stays null, needs manual grading
        }

        $score = $totalPoints > 0 ? round(($totalEarned / $totalPoints) * 100, 2) : 0;

        $exam->students()->updateExistingPivot($student->id, [
            'status' => 'completed',
            'finished_at' => now(),
            'score' => $score,
            'total_points' => $totalEarned,
        ]);
    }
}
