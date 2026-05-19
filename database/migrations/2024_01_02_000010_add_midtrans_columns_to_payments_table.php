<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'snap_token')) {
                $table->string('snap_token')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('payments', 'midtrans_response')) {
                $table->json('midtrans_response')->nullable()->after('snap_token');
            }
        });

        // Add 'expire' to status enum if not exists (MySQL specific)
        // We'll use a raw query to modify the enum
        try {
            DB::statement("ALTER TABLE payments MODIFY COLUMN status ENUM('pending', 'success', 'failed', 'refunded', 'expire') DEFAULT 'pending'");
        } catch (\Exception $e) {
            // Silently ignore if already updated or not MySQL
        }
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['snap_token', 'midtrans_response']);
        });
    }
};
