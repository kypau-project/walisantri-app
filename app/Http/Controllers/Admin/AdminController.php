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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_students' => Student::count(),
            'active_students' => Student::where('status', 'active')->count(),
            'claimed_students' => Student::whereNotNull('user_id')->count(),
            'unclaimed_students' => Student::whereNull('user_id')->count(),
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

    /**
     * Tambah santri baru (data dari pesantren, TANPA user)
     * Wali santri akan registrasi sendiri nanti
     */
    public function storeStudent(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'required|string|unique:students',
            'nisn' => 'nullable|string|max:20',
            'class' => 'nullable|string|max:50',
            'room' => 'nullable|string|max:50',
            'enrollment_year' => 'nullable|string|max:20',
            'gender' => 'required|in:L,P',
            'birth_date' => 'nullable|date',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'father_phone' => 'nullable|string|min:8|max:15',
            'mother_phone' => 'nullable|string|min:8|max:15',
            'address' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos/santri', 'public');
        }

        $student = Student::create([
            'user_id' => null,
            'name' => $request->name,
            'nis' => $request->nis,
            'nisn' => $request->nisn,
            'class' => $request->class,
            'room' => $request->room,
            'enrollment_year' => $request->enrollment_year,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'father_name' => $request->father_name,
            'mother_name' => $request->mother_name,
            'father_phone' => $request->father_phone,
            'mother_phone' => $request->mother_phone,
            'address' => $request->address,
            'photo' => $photoPath,
            'barcode_id' => 'STD-' . strtoupper(Str::random(8)),
            'status' => 'active',
        ]);

        Saving::create(['student_id' => $student->id, 'balance' => 0]);

        return redirect('/admin/students')->with('success', 'Data santri berhasil ditambahkan. Wali santri dapat mendaftar dengan mencocokkan nama & No. Induk.');
    }

    public function editStudent($id)
    {
        $student = Student::with('user')->findOrFail($id);
        return view('admin.students.edit', compact('student'));
    }

    /**
     * Update data santri + foto
     */
    public function updateStudent(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'nisn' => 'nullable|string|max:20',
            'class' => 'nullable|string|max:50',
            'room' => 'nullable|string|max:50',
            'enrollment_year' => 'nullable|string|max:20',
            'gender' => 'required|in:L,P',
            'birth_date' => 'nullable|date',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'father_phone' => 'nullable|string|min:8|max:15',
            'mother_phone' => 'nullable|string|min:8|max:15',
            'address' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive,alumni',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Handle foto upload
        if ($request->hasFile('photo')) {
            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                Storage::disk('public')->delete($student->photo);
            }
            $student->photo = $request->file('photo')->store('photos/santri', 'public');
        }

        $student->update([
            'name' => $request->name,
            'nisn' => $request->nisn,
            'class' => $request->class,
            'room' => $request->room,
            'enrollment_year' => $request->enrollment_year,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'father_name' => $request->father_name,
            'mother_name' => $request->mother_name,
            'father_phone' => $request->father_phone,
            'mother_phone' => $request->mother_phone,
            'address' => $request->address,
            'status' => $request->status,
            'photo' => $student->photo,
        ]);

        return redirect('/admin/students')->with('success', 'Data santri berhasil diperbarui.');
    }

    public function deleteStudent($id)
    {
        $student = Student::findOrFail($id);

        // Hapus foto
        if ($student->photo && Storage::disk('public')->exists($student->photo)) {
            Storage::disk('public')->delete($student->photo);
        }

        // Hapus user jika ada
        if ($student->user) {
            $student->user->delete();
        }

        $student->delete();

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
}

