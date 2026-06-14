<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->string('queue_number', 10); // A001
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['menunggu', 'dipanggil', 'diperiksa', 'selesai'])->default('menunggu');
            $table->date('date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};