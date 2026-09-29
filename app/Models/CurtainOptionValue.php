<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurtainOptionValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'name',
        'image_url',
        'price_impact_type',
        'extra_price',
        'is_default',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'extra_price' => 'decimal:0',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(CurtainOptionGroup::class, 'group_id');
    }

    public function getFormattedExtraPriceAttribute()
    {
        if ($this->extra_price <= 0) {
            return 'Miễn phí';
        }

        $unitLabel = match($this->price_impact_type) {
            'per_meter' => ' / mét',
            'per_sqm' => ' / m²',
            default => '',
        };

        return '+ ' . number_format($this->extra_price, 0, ',', '.') . ' ₫' . $unitLabel;
    }
}
