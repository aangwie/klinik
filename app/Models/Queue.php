<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    protected $fillable = ['queue_number', 'patient_id', 'doctor_profile_id', 'status', 'date'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctorProfile()
    {
        return $this->belongsTo(DoctorProfile::class);
    }
}