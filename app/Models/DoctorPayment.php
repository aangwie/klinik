<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorPayment extends Model
{
    protected $fillable = [
        'examination_id', 'patient_id', 'consultation_fee',
        'action_fee', 'total', 'payment_method', 'status', 'invoice_number'
    ];

    public function examination()
    {
        return $this->belongsTo(Examination::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}