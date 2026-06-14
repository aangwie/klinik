<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->string('category', 50)->nullable();
            $table->string('unit', 20)->nullable();
            $table->integer('stock')->default(0);
            $table->decimal('purchase_price', 12, 0)->default(0);
            $table->decimal('selling_price', 12, 0)->default(0);
            $table->date('expired_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};