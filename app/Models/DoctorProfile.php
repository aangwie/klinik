<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorProfile extends Model
{
    protected $fillable = [
        'user_id', 'full_name', 'address', 'birth_place',
        'birth_date', 'phone', 'str_number', 'specialization', 'photo',
        'consultation_fee', 'is_available',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_available' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }
}
