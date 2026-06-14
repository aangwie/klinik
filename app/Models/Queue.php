<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    protected $fillable = ['queue_number', 'patient_id', 'status', 'date'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}