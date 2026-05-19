<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamQuestion;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExamAdminController extends Controller
{
    public function index()
    {
        $exams = Exam::withCount(['students', 'questions'])
            ->orderBy('exam_date', 'desc')
            ->paginate(20);
        return view('admin.exams.index', compact('exams'));
    }

    public function create()
    {
        $students = Student::where('status', 'active')->orderBy('name')->get();
        return view('admin.exams.create', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:100',
            'teacher_name' => 'nullable|string|max:255',
            'exam_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'duration_minutes' => 'required|integer|min:5|max:300',
            'shuffle_questions' => 'nullable|boolean',
            'show_result' => 'nullable|boolean',
            'description' => 'nullable|string|max:1000',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $exam = Exam::create([
            'title' => $request->title,
            'description' => $request->description,
            'subject' => $request->subject,
            'teacher_name' => $request->teacher_name,
            'exam_date' => $request->exam_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration_minutes' => $request->duration_minutes,
            'shuffle_questions' => $request->boolean('shuffle_questions'),
            'show_result' => $request->boolean('show_result', true),
            'status' => 'draft',
        ]);

        // Attach students with unique access tokens
        $studentIds = $request->student_ids ?: Student::where('status', 'active')->pluck('id')->toArray();
        foreach ($studentIds as $studentId) {
            $exam->students()->attach($studentId, [
                'access_token' => Str::random(48),
                'status' => 'not_started',
            ]);
        }

        return redirect("/admin/exams/{$exam->id}/questions")->with('success', 'Ujian berhasil dibuat. Sekarang tambahkan soal-soalnya.');
    }

    public function show($id)
    {
        $exam = Exam::with(['questions', 'students'])->withCount('questions')->findOrFail($id);
        return view('admin.exams.show', compact('exam'));
    }

    public function edit($id)
    {
        $exam = Exam::findOrFail($id);
        $students = Student::where('status', 'active')->orderBy('name')->get();
        $selectedStudentIds = $exam->students()->pluck('students.id')->toArray();
        return view('admin.exams.edit', compact('exam', 'students', 'selectedStudentIds'));
    }

    public function update(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:100',
            'teacher_name' => 'nullable|string|max:255',
            'exam_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'duration_minutes' => 'required|integer|min:5|max:300',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:draft,active,completed,archived',
        ]);

        $exam->update([
            'title' => $request->title,
            'description' => $request->description,
            'subject' => $request->subject,
            'teacher_name' => $request->teacher_name,
            'exam_date' => $request->exam_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration_minutes' => $request->duration_minutes,
            'shuffle_questions' => $request->boolean('shuffle_questions'),
            'show_result' => $request->boolean('show_result', true),
            'status' => $request->status,
        ]);

        return redirect("/admin/exams/{$exam->id}")->with('success', 'Ujian berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $exam = Exam::findOrFail($id);
        $exam->delete();
        return redirect('/admin/exams')->with('success', 'Ujian berhasil dihapus.');
    }

    // ===== QUESTION MANAGEMENT =====

    public function questions($examId)
    {
        $exam = Exam::with('questions')->findOrFail($examId);
        return view('admin.exams.questions', compact('exam'));
    }

    public function storeQuestion(Request $request, $examId)
    {
        $exam = Exam::findOrFail($examId);

        $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,essay',
            'points' => 'required|integer|min:1|max:100',
            'options' => 'nullable|array',
            'options.*.key' => 'nullable|string',
            'options.*.text' => 'nullable|string',
            'correct_answer' => 'nullable|string',
        ]);

        $maxOrder = $exam->questions()->max('sort_order') ?? 0;

        $options = null;
        if ($request->question_type === 'multiple_choice' && $request->options) {
            $options = array_values(array_filter($request->options, fn($o) => !empty($o['text'])));
        }

        ExamQuestion::create([
            'exam_id' => $exam->id,
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'options' => $options,
            'correct_answer' => $request->correct_answer,
            'points' => $request->points,
            'sort_order' => $maxOrder + 1,
        ]);

        return back()->with('success', 'Soal berhasil ditambahkan.');
    }

    public function updateQuestion(Request $request, $examId, $questionId)
    {
        $question = ExamQuestion::where('exam_id', $examId)->findOrFail($questionId);

        $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,essay',
            'points' => 'required|integer|min:1|max:100',
            'correct_answer' => 'nullable|string',
        ]);

        $options = null;
        if ($request->question_type === 'multiple_choice' && $request->options) {
            $options = array_values(array_filter($request->options, fn($o) => !empty($o['text'])));
        }

        $question->update([
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'options' => $options,
            'correct_answer' => $request->correct_answer,
            'points' => $request->points,
        ]);

        return back()->with('success', 'Soal berhasil diperbarui.');
    }

    public function deleteQuestion($examId, $questionId)
    {
        $question = ExamQuestion::where('exam_id', $examId)->findOrFail($questionId);
        $question->delete();
        return back()->with('success', 'Soal berhasil dihapus.');
    }

    // ===== RESULTS & GRADING =====

    public function results($examId)
    {
        $exam = Exam::with(['students', 'questions'])->findOrFail($examId);
        $participants = $exam->students()
            ->withPivot('status', 'score', 'total_points', 'started_at', 'finished_at')
            ->orderBy('name')
            ->get();
        return view('admin.exams.results', compact('exam', 'participants'));
    }

    public function gradeStudent($examId, $studentId)
    {
        $exam = Exam::with('questions')->findOrFail($examId);
        $student = Student::findOrFail($studentId);
        $answers = ExamAnswer::where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->with('question')
            ->get()
            ->keyBy('exam_question_id');

        return view('admin.exams.grade', compact('exam', 'student', 'answers'));
    }

    public function saveGrade(Request $request, $examId, $studentId)
    {
        $exam = Exam::with('questions')->findOrFail($examId);

        $request->validate([
            'grades' => 'required|array',
            'grades.*.answer_id' => 'required|exists:exam_answers,id',
            'grades.*.points_earned' => 'required|integer|min:0',
            'grades.*.is_correct' => 'required|boolean',
        ]);

        $totalEarned = 0;
        $totalPoints = $exam->questions()->sum('points');

        foreach ($request->grades as $grade) {
            $answer = ExamAnswer::findOrFail($grade['answer_id']);
            $answer->update([
                'points_earned' => $grade['points_earned'],
                'is_correct' => $grade['is_correct'],
            ]);
            $totalEarned += $grade['points_earned'];
        }

        $score = $totalPoints > 0 ? round(($totalEarned / $totalPoints) * 100, 2) : 0;

        // Update pivot
        $exam->students()->updateExistingPivot($studentId, [
            'score' => $score,
            'total_points' => $totalEarned,
            'graded_at' => now(),
        ]);

        return redirect("/admin/exams/{$examId}/results")->with('success', "Nilai {$score} berhasil disimpan.");
    }
}
