<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaGallery extends Model
{
    use HasFactory;

    protected $table = 'media_gallery';

    protected $fillable = [
        'title',
        'type',
        'url',
    ];

    // Scope for images
    public function scopeImages($query)
    {
        return $query->where('type', 'image');
    }

    // Scope for videos
    public function scopeVideos($query)
    {
        return $query->where('type', 'video');
    }
}
