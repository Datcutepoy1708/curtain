<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_code',
        'consultation_id',
        'order_id',
        'version',
        'status',
        'subtotal',
        'discount_amount',
        'installation_fee',
        'total_amount',
        'deposit_percent',
        'deposit_amount',
        'valid_until',
        'estimated_delivery_date',
        'customer_notes',
        'customer_offer_amount',
        'admin_notes',
    ];

    protected $casts = [
        'version' => 'integer',
        'subtotal' => 'decimal:0',
        'discount_amount' => 'decimal:0',
        'installation_fee' => 'decimal:0',
        'total_amount' => 'decimal:0',
        'deposit_percent' => 'integer',
        'deposit_amount' => 'decimal:0',
        'customer_offer_amount' => 'decimal:0',
        'valid_until' => 'date',
        'estimated_delivery_date' => 'date',
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'draft' => ['label' => 'Bản nháp', 'bg' => '#f1f5f9', 'color' => '#475569'],
            'sent' => ['label' => 'Đã công bố - Chờ khách duyệt', 'bg' => '#eff6ff', 'color' => '#2563eb'],
            'revision_requested' => ['label' => 'Khách yêu cầu điều chỉnh', 'bg' => '#fffbeb', 'color' => '#d97706'],
            'accepted' => ['label' => '✓ Khách đã duyệt báo giá', 'bg' => '#ecfdf5', 'color' => '#16a34a'],
            'rejected' => ['label' => 'Đã từ chối', 'bg' => '#fef2f2', 'color' => '#dc2626'],
            'converted' => ['label' => '★ Đã chuyển thành đơn may', 'bg' => '#f5f3ff', 'color' => '#7c3aed'],
            default => ['label' => ucfirst($this->status), 'bg' => '#f8fafc', 'color' => '#64748b'],
        };
    }

    public function getRemainingDepositAttribute(): float
    {
        return max(0, (float)$this->total_amount - (float)$this->deposit_amount);
    }

    public function canBeAccepted(): bool
    {
        return $this->status === 'sent'
            && (!$this->valid_until || !$this->valid_until->isBefore(today()))
            && (int) $this->consultation?->quotations()
                ->whereIn('status', ['sent', 'revision_requested', 'accepted', 'converted'])
                ->orderByDesc('version')->value('id') === $this->id;
    }

    public function recalculateTotals(): void
    {
        $subtotal = $this->items()->sum('subtotal');
        $this->subtotal = $subtotal;
        $this->total_amount = max(0, $subtotal - (float)$this->discount_amount + (float)$this->installation_fee);
        
        $percent = $this->deposit_percent > 0 ? $this->deposit_percent : 30;
        $this->deposit_amount = round(($this->total_amount * $percent) / 100);
        $this->save();
    }
}
