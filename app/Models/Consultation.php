<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'address',
        'city',
        'district',
        'ward',
        'preferred_date',
        'preferred_time',
        'curtain_types_interested',
        'estimated_windows',
        'notes',
        'status',
        'staff_id',
        'quotation_amount',
        'admin_note',
        'current_quotation_id',
        'estimated_amount',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'curtain_types_interested' => 'array',
        'estimated_windows' => 'integer',
        'quotation_amount' => 'decimal:0',
        'estimated_amount' => 'decimal:0',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (empty($item->code)) {
                $item->code = 'LH-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function windows()
    {
        return $this->hasMany(ConsultationWindow::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class)->orderBy('version', 'desc');
    }

    public function currentQuotation()
    {
        return $this->belongsTo(Quotation::class, 'current_quotation_id');
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => ['label' => 'Chờ tiếp nhận', 'color' => '#d97706', 'bg' => '#fef3c7'],
            'assigned' => ['label' => 'Đã phân công', 'color' => '#2563eb', 'bg' => '#dbeafe'],
            'surveying' => ['label' => 'Đang đo đạc', 'color' => '#7c3aed', 'bg' => '#ede9fe'],
            'quoted' => ['label' => 'Đã lập báo giá', 'color' => '#0891b2', 'bg' => '#cffafe'],
            'completed' => ['label' => 'Đã ký hợp đồng / Tạo đơn', 'color' => '#16a34a', 'bg' => '#dcfce7'],
            'cancelled' => ['label' => 'Đã hủy', 'color' => '#dc2626', 'bg' => '#fee2e2'],
            default => ['label' => $this->status, 'color' => '#64748b', 'bg' => '#f1f5f9'],
        };
    }
}
