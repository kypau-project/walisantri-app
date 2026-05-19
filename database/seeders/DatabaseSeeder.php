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
            'phone' => '081200000000',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone_verified_at' => now(),
        ]);

        // ===== DATA SANTRI DARI PESANTREN (tanpa user/wali) =====
        $santriPesantren = [
            [
                'name' => 'Ahmad Fauzan Habibi',
                'nis' => '2024010', 'nisn' => '0078901234',
                'class' => '1A-PA', 'room' => 'A1',
                'enrollment_year' => '2024-2025',
                'gender' => 'L',
                'father_name' => 'H. Habibi Syahrul', 'mother_name' => 'Hj. Nurjannah',
            ],
            [
                'name' => 'Siti Aisyah Zahra',
                'nis' => '2024011', 'nisn' => '0078901235',
                'class' => '1B-PI', 'room' => 'B3',
                'enrollment_year' => '2024-2025',
                'gender' => 'P',
                'father_name' => 'Ahmad Zahroni', 'mother_name' => 'Siti Mariam',
            ],
            [
                'name' => 'Muhammad Haikal',
                'nis' => '2024012', 'nisn' => '0078901236',
                'class' => '2A-PA', 'room' => 'C2',
                'enrollment_year' => '2023-2024',
                'gender' => 'L',
                'father_name' => 'Ir. Haikal Mansur', 'mother_name' => 'Dewi Sartika',
            ],
            [
                'name' => 'Fatimah Azzahra',
                'nis' => '2024013', 'nisn' => '0078901237',
                'class' => '1C-PI', 'room' => 'D7',
                'enrollment_year' => '2024-2025',
                'gender' => 'P',
                'father_name' => 'Ustadz Mukhtar', 'mother_name' => 'Hj. Fatimah',
            ],
            [
                'name' => 'Abdullah Syafiq',
                'nis' => '2024014', 'nisn' => '0078901238',
                'class' => '3A-PA', 'room' => 'A5',
                'enrollment_year' => '2022-2023',
                'gender' => 'L',
                'father_name' => 'Syafiq Abdullah', 'mother_name' => 'Aminah Rahmawati',
            ],
            [
                'name' => 'Khadijah Nur Aini',
                'nis' => '2024015', 'nisn' => '0078901239',
                'class' => '2B-PI', 'room' => 'B7',
                'enrollment_year' => '2023-2024',
                'gender' => 'P',
                'father_name' => 'Dr. Nur Hasan', 'mother_name' => 'Khadijah Aminah',
            ],
            [
                'name' => 'Umar Faruq',
                'nis' => '2024016', 'nisn' => '0078901240',
                'class' => '3B-PA', 'room' => 'C4',
                'enrollment_year' => '2022-2023',
                'gender' => 'L',
                'father_name' => 'Faruq Ismail', 'mother_name' => 'Halimah Tusadiyah',
            ],
            [
                'name' => 'Hafidza Ramadhani',
                'nis' => '2024017', 'nisn' => '0078901241',
                'class' => '1A-PI', 'room' => 'D3',
                'enrollment_year' => '2024-2025',
                'gender' => 'P',
                'father_name' => 'Ramadhan Wijaya', 'mother_name' => 'Sri Wahyuni',
            ],
        ];

        foreach ($santriPesantren as $data) {
            Student::create([
                'user_id' => null,
                'name' => $data['name'],
                'nis' => $data['nis'],
                'nisn' => $data['nisn'],
                'class' => $data['class'],
                'room' => $data['room'],
                'enrollment_year' => $data['enrollment_year'],
                'gender' => $data['gender'],
                'father_name' => $data['father_name'],
                'mother_name' => $data['mother_name'],
                'barcode_id' => 'STD-' . strtoupper(Str::random(8)),
                'status' => 'active',
                'birth_date' => '20' . rand(8, 13) . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
            ]);
        }

        // ===== SANTRI DENGAN WALI TERDAFTAR (untuk testing login) =====
        $santriDenganWali = [
            [
                'student_name' => 'Muhammad Rizki Fauzi',
                'nis' => '2024001', 'nisn' => '0071234567',
                'class' => 'VII-A', 'room' => 'Al-Fatihah',
                'enrollment_year' => '2024-2025',
                'gender' => 'L',
                'father_name' => 'H. Fauzi Rahman', 'mother_name' => 'Hj. Aisyah Fauzi',
                'phone' => '081234567890',
                'father_phone' => '081234567890', 'mother_phone' => '081234567891',
            ],
            [
                'student_name' => 'Aisyah Putri Aminah',
                'nis' => '2024002', 'nisn' => '0071234568',
                'class' => '1C-PI', 'room' => 'D7',
                'enrollment_year' => '2024-2025',
                'gender' => 'P',
                'father_name' => 'Aminah Siregar', 'mother_name' => 'Putri Handayani',
                'phone' => '082345678901',
                'father_phone' => '082345678901', 'mother_phone' => '082345678902',
            ],
            [
                'student_name' => 'Fajar Ramadhan',
                'nis' => '2024003', 'nisn' => '0071234569',
                'class' => '2A-PA', 'room' => 'C2',
                'enrollment_year' => '2023-2024',
                'gender' => 'L',
                'father_name' => 'Ramadhan Hakim', 'mother_name' => 'Nurul Hidayah',
                'phone' => '083456789012',
                'father_phone' => '083456789012', 'mother_phone' => '083456789013',
            ],
            [
                'student_name' => 'Nur Hidayah',
                'nis' => '2024004', 'nisn' => '0071234570',
                'class' => '2B-PI', 'room' => 'B7',
                'enrollment_year' => '2023-2024',
                'gender' => 'P',
                'father_name' => 'Hidayat Surya', 'mother_name' => 'Nur Aisyah',
                'phone' => '084567890123',
                'father_phone' => '084567890123', 'mother_phone' => '084567890124',
            ],
            [
                'student_name' => 'Umar Abdullah',
                'nis' => '2024005', 'nisn' => '0071234571',
                'class' => '3A-PA', 'room' => 'A5',
                'enrollment_year' => '2022-2023',
                'gender' => 'L',
                'father_name' => 'Abdullah Mansur', 'mother_name' => 'Maryam Abdullah',
                'phone' => '085678901234',
                'father_phone' => '085678901234', 'mother_phone' => '085678901235',
            ],
        ];

        $months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
        $monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        foreach ($santriDenganWali as $index => $data) {
            $user = User::create([
                'name' => $data['student_name'],
                'username' => $data['nis'],
                'phone' => $data['phone'],
                'password' => Hash::make('password'),
                'role' => 'wali',
                'phone_verified_at' => now(),
            ]);

            $student = Student::create([
                'user_id' => $user->id,
                'name' => $data['student_name'],
                'nis' => $data['nis'],
                'nisn' => $data['nisn'],
                'class' => $data['class'],
                'room' => $data['room'],
                'enrollment_year' => $data['enrollment_year'],
                'gender' => $data['gender'],
                'father_name' => $data['father_name'],
                'mother_name' => $data['mother_name'],
                'father_phone' => $data['father_phone'],
                'mother_phone' => $data['mother_phone'],
                'barcode_id' => 'STD-' . strtoupper(Str::random(8)),
                'status' => 'active',
                'birth_date' => '20' . rand(8, 12) . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                'address' => 'Jl. Pesantren No. ' . ($index + 1) . ', Kota Bandung',
            ]);

            $savingBalance = rand(50000, 500000);
            $saving = Saving::create(['student_id' => $student->id, 'balance' => $savingBalance]);

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
                    'created_at' => now()->subDays(rand(1, 180)),
                ]);
            }
            $saving->update(['balance' => $runningBalance]);

            foreach ($months as $mi => $month) {
                $isPaid = $mi < 3;
                $isPartial = $mi === 3;

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

            if ($index < 3) {
                Bill::create([
                    'student_id' => $student->id,
                    'title' => 'Daftar Ulang TA 2024/2025',
                    'description' => 'Biaya daftar ulang tahun ajaran baru',
                    'amount' => 2500000, 'paid_amount' => 2500000,
                    'type' => 'daftar_ulang', 'status' => 'paid',
                    'due_date' => '2024-07-15',
                ]);
                Bill::create([
                    'student_id' => $student->id,
                    'title' => 'Seragam Baru',
                    'description' => 'Biaya seragam pesantren',
                    'amount' => 850000, 'paid_amount' => 0,
                    'type' => 'seragam', 'status' => 'pending',
                    'due_date' => '2024-08-01',
                ]);
            }

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
                'semester' => 'Ganjil', 'academic_year' => '2024/2025',
                'grades' => $subjects, 'average_score' => round($avgScore, 2),
                'rank' => $index + 1, 'published_at' => '2024-12-20',
            ]);

            $subjects2 = array_map(fn($s) => ['subject' => $s['subject'], 'score' => rand(65, 98)], $subjects);
            $avgScore2 = array_sum(array_column($subjects2, 'score')) / count($subjects2);

            Report::create([
                'student_id' => $student->id,
                'semester' => 'Genap', 'academic_year' => '2023/2024',
                'grades' => $subjects2, 'average_score' => round($avgScore2, 2),
                'rank' => rand(1, 10), 'published_at' => '2024-06-15',
            ]);
        }

        // ===== EXAMS WITH QUESTIONS =====
        $studentIds = Student::whereNotNull('user_id')->pluck('id')->toArray();

        // Exam 1: Completed (with questions + answers)
        $exam1 = Exam::create([
            'title' => 'UTS Al-Quran Semester Ganjil',
            'description' => 'Ujian Tengah Semester mata pelajaran Al-Quran. Kerjakan dengan jujur.',
            'subject' => 'Al-Quran',
            'teacher_name' => 'Ustadz Ahmad Fauzi',
            'exam_date' => '2024-10-15',
            'start_time' => '08:00',
            'end_time' => '10:00',
            'duration_minutes' => 90,
            'shuffle_questions' => false,
            'show_result' => true,
            'status' => 'completed',
        ]);

        $q1 = \App\Models\ExamQuestion::create(['exam_id' => $exam1->id, 'question_text' => 'Surah Al-Fatihah terdiri dari berapa ayat?', 'question_type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'5 ayat'],['key'=>'B','text'=>'6 ayat'],['key'=>'C','text'=>'7 ayat'],['key'=>'D','text'=>'8 ayat']], 'correct_answer' => 'C', 'points' => 10, 'sort_order' => 1]);
        $q2 = \App\Models\ExamQuestion::create(['exam_id' => $exam1->id, 'question_text' => 'Apa arti dari "Bismillahirrahmanirrahim"?', 'question_type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'Segala puji bagi Allah'],['key'=>'B','text'=>'Dengan menyebut nama Allah Yang Maha Pengasih lagi Maha Penyayang'],['key'=>'C','text'=>'Raja di hari pembalasan'],['key'=>'D','text'=>'Hanya kepada-Mu kami menyembah']], 'correct_answer' => 'B', 'points' => 10, 'sort_order' => 2]);
        $q3 = \App\Models\ExamQuestion::create(['exam_id' => $exam1->id, 'question_text' => 'Jelaskan keutamaan membaca Surah Al-Fatihah dalam shalat!', 'question_type' => 'essay', 'correct_answer' => 'Al-Fatihah wajib dibaca dalam setiap rakaat shalat karena merupakan rukun shalat.', 'points' => 20, 'sort_order' => 3]);

        foreach ($studentIds as $sid) {
            $exam1->students()->attach($sid, [
                'access_token' => Str::random(48),
                'status' => 'completed',
                'score' => rand(60, 100),
                'total_points' => rand(25, 40),
                'started_at' => '2024-10-15 08:05:00',
                'finished_at' => '2024-10-15 09:15:00',
            ]);

            // Create answers
            \App\Models\ExamAnswer::create(['exam_id'=>$exam1->id, 'student_id'=>$sid, 'exam_question_id'=>$q1->id, 'answer_text'=>'C', 'is_correct'=>true, 'points_earned'=>10]);
            \App\Models\ExamAnswer::create(['exam_id'=>$exam1->id, 'student_id'=>$sid, 'exam_question_id'=>$q2->id, 'answer_text'=>'B', 'is_correct'=>true, 'points_earned'=>10]);
            \App\Models\ExamAnswer::create(['exam_id'=>$exam1->id, 'student_id'=>$sid, 'exam_question_id'=>$q3->id, 'answer_text'=>'Al-Fatihah wajib dibaca karena merupakan rukun shalat.', 'is_correct'=>null, 'points_earned'=>15]);
        }

        // Exam 2: Active (today, can be taken)
        $exam2 = Exam::create([
            'title' => 'UAS Fiqih',
            'description' => 'Ujian Akhir Semester Fiqih. Dilarang membuka buku.',
            'subject' => 'Fiqih',
            'teacher_name' => 'Ustadzah Maryam',
            'exam_date' => now()->format('Y-m-d'),
            'start_time' => '00:00',
            'end_time' => '23:59',
            'duration_minutes' => 60,
            'shuffle_questions' => true,
            'show_result' => true,
            'status' => 'active',
        ]);

        \App\Models\ExamQuestion::create(['exam_id'=>$exam2->id, 'question_text'=>'Berapa jumlah rukun Islam?', 'question_type'=>'multiple_choice', 'options'=>[['key'=>'A','text'=>'3'],['key'=>'B','text'=>'4'],['key'=>'C','text'=>'5'],['key'=>'D','text'=>'6']], 'correct_answer'=>'C', 'points'=>10, 'sort_order'=>1]);
        \App\Models\ExamQuestion::create(['exam_id'=>$exam2->id, 'question_text'=>'Shalat wajib yang dilakukan 5 waktu disebut?', 'question_type'=>'multiple_choice', 'options'=>[['key'=>'A','text'=>'Shalat Sunnah'],['key'=>'B','text'=>'Shalat Fardhu'],['key'=>'C','text'=>'Shalat Dhuha'],['key'=>'D','text'=>'Shalat Tahajud']], 'correct_answer'=>'B', 'points'=>10, 'sort_order'=>2]);
        \App\Models\ExamQuestion::create(['exam_id'=>$exam2->id, 'question_text'=>'Sebutkan syarat-syarat sah shalat!', 'question_type'=>'essay', 'correct_answer'=>'Islam, baligh, berakal, suci dari hadas, menghadap kiblat, masuk waktu shalat, menutup aurat.', 'points'=>20, 'sort_order'=>3]);
        \App\Models\ExamQuestion::create(['exam_id'=>$exam2->id, 'question_text'=>'Apa yang membatalkan wudhu?', 'question_type'=>'multiple_choice', 'options'=>[['key'=>'A','text'=>'Makan'],['key'=>'B','text'=>'Tidur nyenyak'],['key'=>'C','text'=>'Minum air'],['key'=>'D','text'=>'Berjalan']], 'correct_answer'=>'B', 'points'=>10, 'sort_order'=>4]);
        \App\Models\ExamQuestion::create(['exam_id'=>$exam2->id, 'question_text'=>'Jelaskan perbedaan antara shalat fardhu dan shalat sunnah!', 'question_type'=>'essay', 'correct_answer'=>'Shalat fardhu hukumnya wajib dan berdosa jika ditinggalkan. Shalat sunnah hukumnya tidak wajib.', 'points'=>20, 'sort_order'=>5]);

        foreach ($studentIds as $sid) {
            $exam2->students()->attach($sid, [
                'access_token' => Str::random(48),
                'status' => 'not_started',
            ]);
        }

        // Exam 3: Draft
        $exam3 = Exam::create([
            'title' => 'UAS Matematika',
            'description' => 'Ujian Akhir Semester Matematika.',
            'subject' => 'Matematika',
            'teacher_name' => 'Ustadz Haikal',
            'exam_date' => now()->addDays(7)->format('Y-m-d'),
            'duration_minutes' => 90,
            'status' => 'draft',
        ]);

        \App\Models\ExamQuestion::create(['exam_id'=>$exam3->id, 'question_text'=>'Berapakah hasil dari 15 x 12?', 'question_type'=>'multiple_choice', 'options'=>[['key'=>'A','text'=>'170'],['key'=>'B','text'=>'180'],['key'=>'C','text'=>'190'],['key'=>'D','text'=>'200']], 'correct_answer'=>'B', 'points'=>10, 'sort_order'=>1]);

        foreach ($studentIds as $sid) {
            $exam3->students()->attach($sid, [
                'access_token' => Str::random(48),
                'status' => 'not_started',
            ]);
        }
    }
}
