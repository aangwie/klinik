<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('examination_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->decimal('consultation_fee', 12, 0)->default(50000);
            $table->decimal('action_fee', 12, 0)->default(0);
            $table->decimal('total', 12, 0)->default(0);
            $table->enum('payment_method', ['tunai', 'qris', 'transfer', 'debit'])->nullable();
            $table->enum('status', ['menunggu', 'lunas'])->default('menunggu');
            $table->string('invoice_number', 50)->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_payments');
    }
};