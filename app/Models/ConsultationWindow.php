<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationWindow extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_id',
        'room_name',
        'product_id',
        'product_name',
        'width',
        'height',
        'install_type',
        'fabric_color',
        'has_sheer',
        'sewing_style',
        'motor_type',
        'quantity',
        'estimated_price',
        'photo_path',
        'notes',
    ];

    protected $casts = [
        'width' => 'float',
        'height' => 'float',
        'has_sheer' => 'boolean',
        'quantity' => 'integer',
        'estimated_price' => 'decimal:0',
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function quotationItems()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function getInstallTypeLabelAttribute(): string
    {
        return $this->install_type === 'inside' ? 'Lọt lòng (Trong khung)' : 'Phủ bì (Trùm tường)';
    }

    public function getSewingStyleLabelAttribute(): string
    {
        return match ($this->sewing_style) {
            'wave' => 'May định hình sóng rèm cao cấp',
            'pleat' => 'May xếp ly 2-3 cánh truyền thống',
            'eyelet' => 'May ore khuyên xỏ lỗ',
            'roman' => 'May xếp lớp Roman',
            default => 'Tiêu chuẩn',
        };
    }

    public function getMotorTypeLabelAttribute(): string
    {
        return match ($this->motor_type) {
            'smart_wifi' => 'Động cơ điện Tuya Smart Wifi',
            'somfy' => 'Động cơ cao cấp Somfy (Pháp)',
            default => 'Ray trượt cơ chống ồn tiêu chuẩn',
        };
    }

    /**
     * Tính số lượng quy đổi tính tiền (mét ngang hoặc m2 hoặc bộ)
     */
    public function getCalculatedUnitsAttribute(): float
    {
        $unit = $this->product ? $this->product->price_unit : 'meter';

        if ($unit === 'meter') {
            return max(round($this->width / 100, 2), 1.0);
        }

        if ($unit === 'sqm') {
            $sqm = ($this->width * $this->height) / 10000;
            return max(round($sqm, 2), 1.0);
        }

        return 1.0;
    }

    public function getUnitLabelAttribute(): string
    {
        return match ($this->product?->price_unit ?? 'meter') {
            'meter' => 'mét ngang',
            'sqm' => 'm²',
            'piece' => 'bộ',
            default => 'mét',
        };
    }

    /**
     * Tính toán giá dự toán chuẩn cho ô cửa này
     */
    public function calculatePricing(): array
    {
        $unitPrice = $this->product ? (float)$this->product->effective_price : 750000;
        $units = $this->calculated_units;
        $fabricCost = $unitPrice * $units;

        $optionsCost = 0;
        $optionsDetail = [];

        // Phụ phí may 2 lớp voan trắng
        if ($this->has_sheer) {
            $sheerPrice = 350000 * $units;
            $optionsCost += $sheerPrice;
            $optionsDetail[] = [
                'name' => 'Lớp voan trắng lấy sáng mềm mại',
                'price' => $sheerPrice
            ];
        }

        // Phụ phí may định hình sóng
        if ($this->sewing_style === 'wave') {
            $wavePrice = 120000 * $units;
            $optionsCost += $wavePrice;
            $optionsDetail[] = [
                'name' => 'Công may định hình sóng chuẩn khách sạn',
                'price' => $wavePrice
            ];
        }

        // Phụ phí động cơ thông minh
        if ($this->motor_type === 'smart_wifi') {
            $motorPrice = 1850000;
            $optionsCost += $motorPrice;
            $optionsDetail[] = [
                'name' => 'Động cơ rèm tự động Tuya Smart Wifi',
                'price' => $motorPrice
            ];
        } elseif ($this->motor_type === 'somfy') {
            $motorPrice = 3900000;
            $optionsCost += $motorPrice;
            $optionsDetail[] = [
                'name' => 'Động cơ Somfy nhập khẩu Pháp siêu êm',
                'price' => $motorPrice
            ];
        }

        $singleTotal = $fabricCost + $optionsCost;
        $subtotal = $singleTotal * $this->quantity;

        return [
            'unit_price' => $unitPrice,
            'calculated_units' => $units,
            'unit_label' => $this->unit_label,
            'fabric_cost' => $fabricCost,
            'options_cost' => $optionsCost,
            'options_detail' => $optionsDetail,
            'subtotal' => $subtotal,
        ];
    }
}
