<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pharmacy_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->decimal('total', 12, 0)->default(0);
            $table->enum('payment_method', ['tunai', 'qris', 'transfer', 'debit'])->nullable();
            $table->enum('status', ['menunggu', 'lunas'])->default('menunggu');
            $table->string('receipt_number', 20)->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pharmacy_sales');
    }
};