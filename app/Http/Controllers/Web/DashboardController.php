<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $student = $user->student;

        if (!$student) {
            return view('dashboard', ['student' => null]);
        }

        $pendingBills = $student->bills()->whereIn('status', ['pending', 'partial', 'overdue'])->count();
        $totalPaid = $student->payments()->where('status', 'success')->sum('amount');
        $savingBalance = $student->savingAccount?->balance ?? 0;
        $upcomingExams = $student->exams()->where('exams.status', '!=', 'completed')->count();

        return view('dashboard', compact('student', 'pendingBills', 'totalPaid', 'savingBalance', 'upcomingExams'));
    }

    public function profile(Request $request)
    {
        $student = $request->user()->student;
        return view('profile.index', compact('student'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'father_phone' => 'nullable|string|min:8|max:15|regex:/^[0-9]+$/',
            'mother_phone' => 'nullable|string|min:8|max:15|regex:/^[0-9]+$/',
            'address' => 'nullable|string|max:500',
        ]);

        $student = $request->user()->student;

        if (!$student) {
            return back()->with('error', 'Data santri tidak ditemukan.');
        }

        $student->update($request->only('father_phone', 'mother_phone', 'address'));

        return back()->with('success', 'Data kontak berhasil diperbarui.');
    }

    public function bills(Request $request)
    {
        $student = $request->user()->student;
        $bills = $student->bills()->orderBy('created_at', 'desc')->get();
        return view('bills.index', compact('student', 'bills'));
    }

    public function payBill(Request $request)
    {
        $request->validate([
            'bill_id' => 'required|exists:bills,id',
            'amount' => 'required|numeric|min:1000',
        ]);

        $student = $request->user()->student;
        $bill = $student->bills()->findOrFail($request->bill_id);

        if ($bill->status === 'paid') {
            return back()->with('error', 'Tagihan sudah lunas.');
        }

        $remaining = $bill->amount - $bill->paid_amount;
        $payAmount = min($request->amount, $remaining);

        Payment::create([
            'student_id' => $student->id,
            'bill_id' => $bill->id,
            'amount' => $payAmount,
            'payment_method' => $request->payment_method ?? 'transfer',
            'transaction_id' => 'TRX-' . strtoupper(Str::random(12)),
            'status' => 'success',
            'paid_at' => now(),
        ]);

        $bill->paid_amount += $payAmount;
        $bill->status = $bill->paid_amount >= $bill->amount ? 'paid' : 'partial';
        $bill->save();

        return back()->with('success', 'Pembayaran sebesar Rp ' . number_format($payAmount, 0, ',', '.') . ' berhasil!');
    }

    public function payments(Request $request)
    {
        $student = $request->user()->student;
        $payments = $student->payments()->with('bill')->orderBy('created_at', 'desc')->get();
        return view('payments.index', compact('student', 'payments'));
    }

    public function savings(Request $request)
    {
        $student = $request->user()->student;
        $saving = $student->savingAccount;
        $transactions = $saving ? $saving->transactions()->orderBy('created_at', 'desc')->get() : collect();
        return view('savings.index', compact('student', 'saving', 'transactions'));
    }

    public function topupSaving(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        $student = $request->user()->student;
        $saving = $student->savingAccount ?? $student->savingAccount()->create(['balance' => 0]);

        $saving->balance += $request->amount;
        $saving->save();

        $saving->transactions()->create([
            'type' => 'topup',
            'amount' => $request->amount,
            'description' => $request->description ?? 'Top up saldo',
            'transaction_id' => 'SAV-' . strtoupper(Str::random(10)),
            'balance_after' => $saving->balance,
        ]);

        return back()->with('success', 'Top up Rp ' . number_format($request->amount, 0, ',', '.') . ' berhasil!');
    }

    public function exams(Request $request)
    {
        $student = $request->user()->student;
        $exams = $student->exams()->orderBy('exam_date', 'desc')->get();
        return view('exams.index', compact('student', 'exams'));
    }

    public function reports(Request $request)
    {
        $student = $request->user()->student;
        $reports = $student->reports()->whereNotNull('published_at')->orderBy('published_at', 'desc')->get();
        return view('reports.index', compact('student', 'reports'));
    }

    public function downloadReport(Request $request, $id)
    {
        $student = $request->user()->student;
        $report = $student->reports()->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.report', [
            'student' => $student,
            'report' => $report,
        ]);

        $filename = str_replace(['/', '\\'], '-', "Raport_{$student->name}_{$report->semester}_{$report->academic_year}.pdf");
        return $pdf->download($filename);
    }
}
