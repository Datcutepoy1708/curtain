<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'consultation_window_id',
        'room_name',
        'product_id',
        'product_name',
        'width',
        'height',
        'install_type',
        'calculated_units',
        'unit_label',
        'unit_price',
        'fabric_cost',
        'options_cost',
        'options_detail',
        'quantity',
        'subtotal',
        'notes',
    ];

    protected $casts = [
        'width' => 'float',
        'height' => 'float',
        'calculated_units' => 'float',
        'unit_price' => 'decimal:0',
        'fabric_cost' => 'decimal:0',
        'options_cost' => 'decimal:0',
        'subtotal' => 'decimal:0',
        'options_detail' => 'array',
        'quantity' => 'integer',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function consultationWindow()
    {
        return $this->belongsTo(ConsultationWindow::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getInstallTypeLabelAttribute(): string
    {
        return $this->install_type === 'inside' ? 'Lọt lòng' : 'Phủ bì';
    }
}
