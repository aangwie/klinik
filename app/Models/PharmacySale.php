<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PharmacySale extends Model
{
    protected $fillable = [
        'prescription_id', 'patient_id', 'total',
        'payment_method', 'status', 'receipt_number'
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}