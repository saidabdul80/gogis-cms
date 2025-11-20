<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevenueHead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
    ];

    // Relationships
    public function subHeads()
    {
        return $this->hasMany(RevenueSubHead::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
