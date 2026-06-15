<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $fillable = [
        'code', 'name', 'category', 'unit', 'stock', 'low_stock',
        'purchase_price', 'selling_price', 'expired_date'
    ];

    protected $casts = [
        'expired_date' => 'date',
    ];

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function scopeExpiringSoon($query, $months = 6)
    {
        return $query->whereNotNull('expired_date')
            ->where('expired_date', '>=', now())
            ->where('expired_date', '<=', now()->addMonths($months))
            ->orderBy('expired_date');
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'low_stock');
    }
}