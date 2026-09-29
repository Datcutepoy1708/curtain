<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'shipping_address',
        'city',
        'district',
        'ward',
        'total_amount',
        'payment_method',
        'payment_gateway',
        'payment_status',
        'transaction_id',
        'paid_at',
        'order_status',
        'stock_restored',
        'quotation_id',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:0',
        'stock_restored' => 'boolean',
        'paid_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_code)) {
                $order->order_code = 'DH-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            }
        });
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function getFormattedTotalAttribute()
    {
        return number_format($this->total_amount, 0, ',', '.') . ' ₫';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->order_status) {
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã duyệt / Cắt may',
            'manufacturing' => 'Đang gia công',
            'shipping' => 'Đang vận chuyển',
            'installed' => 'Đã lắp đặt',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            default => ucfirst($this->order_status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->order_status) {
            'pending' => 'badge-warning',
            'confirmed' => 'badge-info',
            'manufacturing' => 'badge-purple',
            'shipping' => 'badge-blue',
            'installed', 'completed' => 'badge-success',
            'cancelled' => 'badge-danger',
            default => 'badge-neutral',
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match($this->payment_status) {
            'paid' => 'Đã thanh toán',
            'partially_paid' => 'Đã đặt cọc',
            'pending' => 'Chưa thanh toán',
            'failed' => 'Giao dịch thất bại',
            default => $this->payment_status,
        };
    }

    public function canBeCancelledByCustomer(): bool
    {
        return $this->order_status === 'pending';
    }

    public function restoreStock(): bool
    {
        if ($this->stock_restored) {
            return false;
        }

        foreach ($this->items as $item) {
            if ($item->product_id && $item->stock_deducted > 0) {
                Product::where('id', $item->product_id)->increment('stock', $item->stock_deducted);
            }
        }

        $this->update(['stock_restored' => true]);
        return true;
    }
}
