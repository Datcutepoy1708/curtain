<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'product_reviews';

    protected $fillable = [
        'product_id',
        'user_id',
        'order_id',
        'customer_name',
        'customer_phone',
        'rating',
        'comment',
        'reply_content',
        'replied_by',
        'replied_at',
        'is_verified_buyer',
        'status',
    ];

    protected $casts = [
        'is_verified_buyer' => 'boolean',
        'rating' => 'integer',
        'replied_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replier()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function hasReply(): bool
    {
        return !empty(trim($this->reply_content ?? ''));
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved')->latest();
    }
}
