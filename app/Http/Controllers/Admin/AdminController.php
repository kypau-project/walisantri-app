<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use App\Models\Exam;
use App\Models\Report;
use App\Models\Saving;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_students' => Student::count(),
            'active_students' => Student::where('status', 'active')->count(),
            'total_bills' => Bill::count(),
            'pending_bills' => Bill::whereIn('status', ['pending', 'partial', 'overdue'])->count(),
            'total_payments' => Payment::where('status', 'success')->sum('amount'),
            'total_savings' => Saving::sum('balance'),
            'recent_payments' => Payment::with(['student', 'bill'])->orderBy('created_at', 'desc')->take(5)->get(),
            'recent_students' => Student::orderBy('created_at', 'desc')->take(5)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    // ===== STUDENT MANAGEMENT =====
    public function students()
    {
        $students = Student::with('user')->orderBy('name')->paginate(20);
        return view('admin.students.index', compact('students'));
    }

    public function createStudent()
    {
        return view('admin.students.create');
    }

    public function storeStudent(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'required|string|unique:students',
            'username' => 'required|string|unique:users',
            'password' => 'required|string|min:6',
            'class' => 'nullable|string',
            'room' => 'nullable|string',
            'father_phone' => 'nullable|string|min:8|max:15',
            'mother_phone' => 'nullable|string|min:8|max:15',
            'gender' => 'required|in:L,P',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'wali',
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'nis' => $request->nis,
            'class' => $request->class,
            'room' => $request->room,
            'father_phone' => $request->father_phone,
            'mother_phone' => $request->mother_phone,
            'gender' => $request->gender,
            'barcode_id' => 'STD-' . strtoupper(Str::random(8)),
        ]);

        Saving::create(['student_id' => $student->id, 'balance' => 0]);

        return redirect('/admin/students')->with('success', 'Santri berhasil ditambahkan.');
    }

    public function editStudent($id)
    {
        $student = Student::with('user')->findOrFail($id);
        return view('admin.students.edit', compact('student'));
    }

    public function updateStudent(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'class' => 'nullable|string',
            'room' => 'nullable|string',
            'father_phone' => 'nullable|string|min:8|max:15',
            'mother_phone' => 'nullable|string|min:8|max:15',
        ]);

        $student->update($request->only('name', 'class', 'room', 'father_phone', 'mother_phone', 'address', 'gender', 'status'));

        return redirect('/admin/students')->with('success', 'Data santri berhasil diperbarui.');
    }

    public function deleteStudent($id)
    {
        $student = Student::findOrFail($id);
        $student->user->delete();

        return redirect('/admin/students')->with('success', 'Data santri berhasil dihapus.');
    }

    // ===== BILL MANAGEMENT =====
    public function bills()
    {
        $bills = Bill::with('student')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.bills.index', compact('bills'));
    }

    public function createBill()
    {
        $students = Student::orderBy('name')->get();
        return view('admin.bills.create', compact('students'));
    }

    public function storeBill(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1000',
            'type' => 'required|string',
            'month' => 'nullable|string',
            'year' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        Bill::create($request->only('student_id', 'title', 'description', 'amount', 'type', 'month', 'year', 'due_date'));

        return redirect('/admin/bills')->with('success', 'Tagihan berhasil dibuat.');
    }

    // ===== PAYMENT MANAGEMENT =====
    public function payments()
    {
        $payments = Payment::with(['student', 'bill'])->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.payments.index', compact('payments'));
    }

    // ===== REPORT MANAGEMENT =====
    public function reports()
    {
        $reports = Report::with('student')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.reports.index', compact('reports'));
    }

    public function createReport()
    {
        $students = Student::orderBy('name')->get();
        return view('admin.reports.create', compact('students'));
    }

    public function storeReport(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'semester' => 'required|string',
            'academic_year' => 'required|string',
            'grades' => 'nullable|array',
        ]);

        $grades = $request->grades ?? [];
        $avgScore = count($grades) > 0 ? array_sum(array_column($grades, 'score')) / count($grades) : null;

        Report::create([
            'student_id' => $request->student_id,
            'semester' => $request->semester,
            'academic_year' => $request->academic_year,
            'grades' => $grades,
            'average_score' => $avgScore,
            'published_at' => now(),
        ]);

        return redirect('/admin/reports')->with('success', 'Raport berhasil dibuat.');
    }

    // ===== EXAM MANAGEMENT =====
    public function exams()
    {
        $exams = Exam::withCount('students')->orderBy('exam_date', 'desc')->paginate(20);
        return view('admin.exams.index', compact('exams'));
    }

    public function createExam()
    {
        $students = Student::orderBy('name')->get();
        return view('admin.exams.create', compact('students'));
    }

    public function storeExam(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string',
            'exam_date' => 'required|date',
            'duration_minutes' => 'required|integer|min:10',
            'exam_url' => 'nullable|url',
            'student_ids' => 'nullable|array',
        ]);

        $exam = Exam::create($request->only('title', 'description', 'subject', 'exam_date', 'duration_minutes', 'exam_url'));

        if ($request->student_ids) {
            $exam->students()->attach($request->student_ids);
        } else {
            $exam->students()->attach(Student::pluck('id'));
        }

        return redirect('/admin/exams')->with('success', 'Ujian berhasil dibuat.');
    }
}
