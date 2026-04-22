<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\Exam;
use App\Models\Payment;
use App\Models\Report;
use App\Models\Saving;
use App\Models\SavingTransaction;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== ADMIN USER =====
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@uqi.ac.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // ===== WALI SANTRI USERS + STUDENTS =====
        $santriData = [
            [
                'wali_name' => 'Ahmad Fauzi',
                'username' => 'wali1',
                'student_name' => 'Muhammad Rizki Fauzi',
                'nis' => '2024001',
                'class' => 'VII-A',
                'room' => 'Al-Fatihah',
                'gender' => 'L',
                'father_phone' => '081234567890',
                'mother_phone' => '081234567891',
            ],
            [
                'wali_name' => 'Siti Aminah',
                'username' => 'wali2',
                'student_name' => 'Aisyah Putri Aminah',
                'nis' => '2024002',
                'class' => 'VII-B',
                'room' => 'Al-Baqarah',
                'gender' => 'P',
                'father_phone' => '082345678901',
                'mother_phone' => '082345678902',
            ],
            [
                'wali_name' => 'Budi Santoso',
                'username' => 'wali3',
                'student_name' => 'Fajar Ramadhan',
                'nis' => '2024003',
                'class' => 'VIII-A',
                'room' => 'Ali Imran',
                'gender' => 'L',
                'father_phone' => '083456789012',
                'mother_phone' => '083456789013',
            ],
            [
                'wali_name' => 'Dewi Kartika',
                'username' => 'wali4',
                'student_name' => 'Nur Hidayah',
                'nis' => '2024004',
                'class' => 'VIII-B',
                'room' => 'An-Nisa',
                'gender' => 'P',
                'father_phone' => '084567890123',
                'mother_phone' => '084567890124',
            ],
            [
                'wali_name' => 'Hasan Abdullah',
                'username' => 'wali5',
                'student_name' => 'Umar Abdullah',
                'nis' => '2024005',
                'class' => 'IX-A',
                'room' => 'Al-Maidah',
                'gender' => 'L',
                'father_phone' => '085678901234',
                'mother_phone' => '085678901235',
            ],
        ];

        $months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
        $monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        foreach ($santriData as $index => $data) {
            $user = User::create([
                'name' => $data['wali_name'],
                'username' => $data['username'],
                'password' => Hash::make('password'),
                'role' => 'wali',
            ]);

            $student = Student::create([
                'user_id' => $user->id,
                'name' => $data['student_name'],
                'nis' => $data['nis'],
                'class' => $data['class'],
                'room' => $data['room'],
                'gender' => $data['gender'],
                'father_phone' => $data['father_phone'],
                'mother_phone' => $data['mother_phone'],
                'barcode_id' => 'STD-' . strtoupper(Str::random(8)),
                'status' => 'active',
                'birth_date' => fake()->dateTimeBetween('2008-01-01', '2012-12-31'),
                'address' => fake()->address(),
            ]);

            // Create savings account
            $savingBalance = rand(50000, 500000);
            $saving = Saving::create([
                'student_id' => $student->id,
                'balance' => $savingBalance,
            ]);

            // Create saving transactions
            $runningBalance = 0;
            for ($t = 0; $t < rand(3, 6); $t++) {
                $topupAmount = rand(50000, 200000);
                $runningBalance += $topupAmount;
                SavingTransaction::create([
                    'saving_id' => $saving->id,
                    'type' => 'topup',
                    'amount' => $topupAmount,
                    'description' => 'Top up saldo tabungan',
                    'transaction_id' => 'SAV-' . strtoupper(Str::random(10)),
                    'balance_after' => $runningBalance,
                    'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
                ]);
            }
            $saving->update(['balance' => $runningBalance]);

            // Create bills (SPP for 12 months)
            foreach ($months as $mi => $month) {
                $isPaid = $mi < 3; // First 3 months paid
                $isPartial = $mi === 3; // 4th month partial

                $bill = Bill::create([
                    'student_id' => $student->id,
                    'title' => "SPP {$monthNames[$mi]} 2024",
                    'description' => "Sumbangan Pembinaan Pendidikan bulan {$monthNames[$mi]}",
                    'amount' => 750000,
                    'paid_amount' => $isPaid ? 750000 : ($isPartial ? 300000 : 0),
                    'month' => $month,
                    'year' => '2024',
                    'type' => 'spp',
                    'status' => $isPaid ? 'paid' : ($isPartial ? 'partial' : ($mi < 5 ? 'overdue' : 'pending')),
                    'due_date' => "2024-{$month}-10",
                ]);

                // Create payment records for paid bills
                if ($isPaid || $isPartial) {
                    Payment::create([
                        'student_id' => $student->id,
                        'bill_id' => $bill->id,
                        'amount' => $isPaid ? 750000 : 300000,
                        'payment_method' => ['transfer', 'cash', 'ewallet'][rand(0, 2)],
                        'transaction_id' => 'TRX-' . strtoupper(Str::random(12)),
                        'status' => 'success',
                        'paid_at' => "2024-{$month}-" . str_pad(rand(1, 10), 2, '0', STR_PAD_LEFT),
                    ]);
                }
            }

            // Extra bills (registration, uniform)
            if ($index < 3) {
                Bill::create([
                    'student_id' => $student->id,
                    'title' => 'Daftar Ulang TA 2024/2025',
                    'description' => 'Biaya daftar ulang tahun ajaran baru',
                    'amount' => 2500000,
                    'paid_amount' => 2500000,
                    'type' => 'daftar_ulang',
                    'status' => 'paid',
                    'due_date' => '2024-07-15',
                ]);

                Bill::create([
                    'student_id' => $student->id,
                    'title' => 'Seragam Baru',
                    'description' => 'Biaya seragam pesantren',
                    'amount' => 850000,
                    'paid_amount' => 0,
                    'type' => 'seragam',
                    'status' => 'pending',
                    'due_date' => '2024-08-01',
                ]);
            }

            // Create reports
            $subjects = [
                ['subject' => 'Al-Quran', 'score' => rand(75, 98)],
                ['subject' => 'Hadits', 'score' => rand(70, 95)],
                ['subject' => 'Fiqih', 'score' => rand(72, 96)],
                ['subject' => 'Aqidah', 'score' => rand(75, 97)],
                ['subject' => 'Bahasa Arab', 'score' => rand(68, 92)],
                ['subject' => 'Matematika', 'score' => rand(65, 95)],
                ['subject' => 'IPA', 'score' => rand(70, 94)],
                ['subject' => 'Bahasa Indonesia', 'score' => rand(72, 96)],
                ['subject' => 'Bahasa Inggris', 'score' => rand(65, 93)],
            ];

            $avgScore = array_sum(array_column($subjects, 'score')) / count($subjects);

            Report::create([
                'student_id' => $student->id,
                'semester' => 'Ganjil',
                'academic_year' => '2024/2025',
                'grades' => $subjects,
                'average_score' => round($avgScore, 2),
                'rank' => $index + 1,
                'published_at' => '2024-12-20',
            ]);

            // Previous semester report
            $subjects2 = array_map(function ($s) {
                return ['subject' => $s['subject'], 'score' => rand(65, 98)];
            }, $subjects);
            $avgScore2 = array_sum(array_column($subjects2, 'score')) / count($subjects2);

            Report::create([
                'student_id' => $student->id,
                'semester' => 'Genap',
                'academic_year' => '2023/2024',
                'grades' => $subjects2,
                'average_score' => round($avgScore2, 2),
                'rank' => rand(1, 10),
                'published_at' => '2024-06-15',
            ]);
        }

        // ===== EXAMS =====
        $examData = [
            ['title' => 'UTS Al-Quran', 'subject' => 'Al-Quran', 'exam_date' => '2024-10-15', 'status' => 'completed'],
            ['title' => 'UTS Fiqih', 'subject' => 'Fiqih', 'exam_date' => '2024-10-16', 'status' => 'completed'],
            ['title' => 'UTS Matematika', 'subject' => 'Matematika', 'exam_date' => '2024-10-17', 'status' => 'completed'],
            ['title' => 'UAS Al-Quran', 'subject' => 'Al-Quran', 'exam_date' => '2024-12-10', 'status' => 'active'],
            ['title' => 'UAS Bahasa Arab', 'subject' => 'Bahasa Arab', 'exam_date' => '2024-12-12', 'status' => 'upcoming'],
            ['title' => 'UAS Matematika', 'subject' => 'Matematika', 'exam_date' => '2024-12-14', 'status' => 'upcoming'],
        ];

        $studentIds = Student::pluck('id')->toArray();

        foreach ($examData as $ed) {
            $exam = Exam::create([
                'title' => $ed['title'],
                'description' => 'Ujian ' . $ed['title'],
                'subject' => $ed['subject'],
                'exam_date' => $ed['exam_date'],
                'duration_minutes' => 90,
                'exam_url' => 'https://exam.uqi.ac.id/start',
                'status' => $ed['status'],
            ]);

            // Attach all students
            foreach ($studentIds as $sid) {
                $pivotStatus = match ($ed['status']) {
                    'completed' => 'completed',
                    'active' => ['not_started', 'in_progress'][rand(0, 1)],
                    default => 'not_started',
                };

                $exam->students()->attach($sid, [
                    'status' => $pivotStatus,
                    'score' => $pivotStatus === 'completed' ? rand(60, 100) : null,
                    'started_at' => $pivotStatus !== 'not_started' ? $ed['exam_date'] . ' 08:00:00' : null,
                    'finished_at' => $pivotStatus === 'completed' ? $ed['exam_date'] . ' 09:30:00' : null,
                ]);
            }
        }
    }
}
