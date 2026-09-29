<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'thumbnail_url',
        'excerpt',
        'content',
        'category',
        'author_id',
        'status',
        'views',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function getAuthorNameAttribute(): string
    {
        return $this->author?->name ?? 'CurtainLux Design Studio';
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')->latest();
    }
}
