<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $fillable = [
        'code', 'name', 'category', 'unit', 'stock',
        'purchase_price', 'selling_price', 'expired_date'
    ];

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
}