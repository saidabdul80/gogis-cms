<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taxpayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'tin',
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone_number',
        'address',
    ];

    // Relationships
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'owner');
    }
}
