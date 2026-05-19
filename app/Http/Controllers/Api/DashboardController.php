<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard summary — semua ringkasan data santri untuk halaman utama Flutter
     */
    public function index(Request $request): JsonResponse
    {
        $student = $request->user()->student;

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Data santri tidak ditemukan.'], 404);
        }

        $student->load('savingAccount');

        // Stats
        $pendingBills = $student->bills()->whereIn('status', ['pending', 'partial', 'overdue'])->count();
        $totalPaid = $student->payments()->where('status', 'success')->sum('amount');
        $savingBalance = $student->savingAccount?->balance ?? 0;
        $upcomingExams = $student->exams()
            ->where('exams.status', 'active')
            ->wherePivotNotIn('status', ['completed', 'missed'])
            ->count();

        // Recent bills (5 terbaru yang belum lunas)
        $recentBills = $student->bills()
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->orderBy('due_date')
            ->limit(5)
            ->get()
            ->map(fn($b) => [
                'id' => $b->id,
                'title' => $b->title,
                'amount' => $b->amount,
                'paid_amount' => $b->paid_amount,
                'remaining' => $b->amount - $b->paid_amount,
                'due_date' => $b->due_date?->format('Y-m-d'),
                'status' => $b->status,
            ]);

        // Recent payments (5 terbaru)
        $recentPayments = $student->payments()
            ->with('bill')
            ->where('status', 'success')
            ->orderBy('paid_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'bill_title' => $p->bill?->title ?? 'N/A',
                'amount' => $p->amount,
                'payment_method' => $p->payment_method,
                'paid_at' => $p->paid_at?->format('Y-m-d H:i'),
            ]);

        // Upcoming exams (3 terbaru)
        $upcomingExamsList = $student->exams()
            ->where('exams.status', 'active')
            ->withPivot('status', 'access_token')
            ->withCount('questions')
            ->orderBy('exam_date')
            ->limit(3)
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'title' => $e->title,
                'subject' => $e->subject,
                'teacher_name' => $e->teacher_name,
                'exam_date' => $e->exam_date->format('Y-m-d'),
                'start_time' => $e->start_time,
                'end_time' => $e->end_time,
                'duration_minutes' => $e->duration_minutes,
                'questions_count' => $e->questions_count,
                'pivot_status' => $e->pivot->status,
                'exam_url' => url('/exams/join/' . $e->pivot->access_token),
            ]);

        return response()->json([
            'success' => true,
            'data' => [
                'student' => new StudentResource($student),
                'stats' => [
                    'pending_bills' => $pendingBills,
                    'total_paid' => $totalPaid,
                    'total_paid_formatted' => 'Rp ' . number_format($totalPaid, 0, ',', '.'),
                    'saving_balance' => $savingBalance,
                    'saving_balance_formatted' => 'Rp ' . number_format($savingBalance, 0, ',', '.'),
                    'upcoming_exams' => $upcomingExams,
                ],
                'recent_bills' => $recentBills,
                'recent_payments' => $recentPayments,
                'upcoming_exams' => $upcomingExamsList,
            ],
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai.',
            ], 422);
        }

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->new_password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah.',
        ]);
    }
}
