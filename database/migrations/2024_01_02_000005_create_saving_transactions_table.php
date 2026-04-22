<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saving_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saving_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['topup', 'withdraw']);
            $table->decimal('amount', 12, 2);
            $table->string('description')->nullable();
            $table->string('transaction_id')->unique();
            $table->decimal('balance_after', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saving_transactions');
    }
};
