<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'product_id',
        'room_label',
        'width',
        'height',
        'mount_type',
        'calculated_units',
        'unit_price',
        'selected_options',
        'subtotal',
        'quantity',
    ];

    protected $casts = [
        'width' => 'decimal:1',
        'height' => 'decimal:1',
        'calculated_units' => 'decimal:2',
        'unit_price' => 'decimal:0',
        'subtotal' => 'decimal:0',
        'selected_options' => 'array',
        'quantity' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedSubtotalAttribute()
    {
        return number_format($this->subtotal, 0, ',', '.') . ' ₫';
    }
}
