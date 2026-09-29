<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'category',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public static function categories(): array
    {
        return [
            'do_dac' => [
                'name' => 'Khảo sát & Đo đạc',
                'icon' => 'fa-ruler-combined',
                'color' => '#b8935c',
            ],
            'chat_lieu' => [
                'name' => 'Chất liệu & Cản sáng',
                'icon' => 'fa-certificate',
                'color' => '#16a34a',
            ],
            'lap_dat' => [
                'name' => 'May đo & Lắp đặt',
                'icon' => 'fa-screwdriver-wrench',
                'color' => '#2563eb',
            ],
            'bao_hanh' => [
                'name' => 'Bảo hành & Thanh toán',
                'icon' => 'fa-shield-halved',
                'color' => '#7c3aed',
            ],
            'general' => [
                'name' => 'Thông tin chung',
                'icon' => 'fa-circle-question',
                'color' => '#64748b',
            ],
        ];
    }

    public function getCategoryLabelAttribute(): string
    {
        $cats = self::categories();
        return $cats[$this->category]['name'] ?? 'Thông tin chung';
    }

    public function getCategoryIconAttribute(): string
    {
        $cats = self::categories();
        return $cats[$this->category]['icon'] ?? 'fa-circle-question';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }
}
