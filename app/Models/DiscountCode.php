<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'max_discount_amount',
        'min_order_value',
        'usage_limit',
        'used_count',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function isValid(): bool
    {
        if ($this->status !== 'active') return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        if ($this->start_date && now()->lt($this->start_date)) return false;
        if ($this->end_date && now()->gt($this->end_date)) return false;
        return true;
    }

    public function calculateDiscount(float $orderTotal): float
    {
        if ($orderTotal < (float)$this->min_order_value) return 0;

        $discount = $this->discount_type === 'percent'
            ? $orderTotal * ((float)$this->discount_value / 100)
            : (float)$this->discount_value;

        if ($this->max_discount_amount && $discount > (float)$this->max_discount_amount) {
            $discount = (float)$this->max_discount_amount;
        }

        return min($discount, $orderTotal);
    }
}
