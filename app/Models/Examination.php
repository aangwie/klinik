<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Examination extends Model
{
    protected $fillable = [
        'patient_id', 'doctor_id', 'complaint', 'blood_pressure',
        'weight', 'height', 'temperature', 'pulse', 'diagnosis',
        'actions', 'notes', 'status'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function doctorPayment()
    {
        return $this->hasOne(DoctorPayment::class);
    }
}