<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'medical_record_number', 'nik', 'name', 'birth_date',
        'gender', 'phone', 'address', 'occupation'
    ];

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }

    public function examinations()
    {
        return $this->hasMany(Examination::class);
    }

    public function doctorPayments()
    {
        return $this->hasMany(DoctorPayment::class);
    }

    public function pharmacySales()
    {
        return $this->hasMany(PharmacySale::class);
    }
}