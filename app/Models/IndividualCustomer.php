<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndividualCustomer extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'nin',
        'gender',
        'dob',
        'lga_id',
        'email',
        'phone_number',
        'address',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    // Relationships
    public function lga()
    {
        return $this->belongsTo(Lga::class);
    }

    public function invoices()
    {
        return $this->morphMany(Invoice::class, 'customer');
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'customer');
    }

    public function properties()
    {
        return $this->morphMany(Property::class, 'customer');
    }

    // Helper methods
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }
}
