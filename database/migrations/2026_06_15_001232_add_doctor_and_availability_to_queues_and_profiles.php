<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctor_profiles', function (Blueprint $table) {
            $table->boolean('is_available')->default(true)->after('consultation_fee');
        });

        Schema::table('queues', function (Blueprint $table) {
            $table->foreignId('doctor_profile_id')->nullable()->constrained('doctor_profiles')->nullOnDelete()->after('patient_id');
        });
    }

    public function down(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->dropForeign(['doctor_profile_id']);
            $table->dropColumn('doctor_profile_id');
        });

        Schema::table('doctor_profiles', function (Blueprint $table) {
            $table->dropColumn('is_available');
        });
    }
};