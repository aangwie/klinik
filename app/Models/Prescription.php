<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $fillable = ['examination_id', 'medicine_id', 'qty', 'instruction', 'status'];

    public function examination()
    {
        return $this->belongsTo(Examination::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function pharmacySale()
    {
        return $this->hasOne(PharmacySale::class);
    }
}