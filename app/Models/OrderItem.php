<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'room_label',
        'room_name',
        'width',
        'height',
        'mount_type',
        'install_type',
        'calculated_units',
        'unit_price',
        'selected_options',
        'options_json',
        'quantity',
        'stock_deducted',
        'subtotal',
    ];

    protected $casts = [
        'width' => 'decimal:1',
        'height' => 'decimal:1',
        'calculated_units' => 'decimal:2',
        'unit_price' => 'decimal:0',
        'subtotal' => 'decimal:0',
        'selected_options' => 'array',
        'options_json' => 'array',
        'quantity' => 'integer',
        'stock_deducted' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getFormattedSubtotalAttribute()
    {
        return number_format($this->subtotal, 0, ',', '.') . ' ₫';
    }
}
