<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('nis')->unique()->comment('Nomor Induk (internal pesantren)');
            $table->string('nisn')->nullable()->comment('Nomor Induk Siswa Nasional');
            $table->string('class')->nullable()->comment('Kelas, contoh: 1C-PI');
            $table->string('room')->nullable()->comment('Kamar, contoh: D7');
            $table->string('enrollment_year')->nullable()->comment('Tahun masuk, contoh: 2024-2025');
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('father_phone', 15)->nullable();
            $table->string('mother_phone', 15)->nullable();
            $table->string('barcode_id')->unique()->nullable();
            $table->string('photo')->nullable()->comment('Path foto profil santri');
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['L', 'P'])->default('L');
            $table->text('address')->nullable();
            $table->enum('status', ['active', 'inactive', 'alumni'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
