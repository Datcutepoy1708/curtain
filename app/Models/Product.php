<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'price',
        'sale_price',
        'price_unit', // 'sqm', 'meter', 'piece'
        'min_area',
        'min_width',
        'max_width',
        'min_height',
        'max_height',
        'blackout_rate',
        'installation_type',
        'stock',
        'material',
        'origin',
        'description',
        'image',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'sale_price' => 'decimal:0',
        'min_area' => 'decimal:2',
        'min_width' => 'integer',
        'max_width' => 'integer',
        'min_height' => 'integer',
        'max_height' => 'integer',
        'blackout_rate' => 'integer',
        'stock' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = 'REM-' . strtoupper(Str::random(6));
            }
        });

        static::updating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getEffectivePriceAttribute()
    {
        return ($this->sale_price && $this->sale_price > 0 && $this->sale_price < $this->price)
            ? $this->sale_price
            : $this->price;
    }

    public function getUnitLabelAttribute()
    {
        return match($this->price_unit) {
            'sqm' => 'm²',
            'meter' => 'mét ngang',
            'piece' => 'bộ/chiếc',
            default => 'm²',
        };
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', '.') . ' ₫ / ' . $this->unit_label;
    }

    public function getFormattedSalePriceAttribute()
    {
        return $this->sale_price ? number_format($this->sale_price, 0, ',', '.') . ' ₫ / ' . $this->unit_label : null;
    }

    public function getStockUnitLabelAttribute(): string
    {
        return match($this->price_unit) {
            'sqm' => 'm² phôi vải',
            'meter' => 'mét vải',
            'piece' => 'bộ/chiếc',
            default => 'đơn vị',
        };
    }

    public function calculateStockNeeded(int $quantity = 1, float $width = 150.0, float $calculatedUnits = 1.0): int
    {
        $q = max(1, $quantity);
        return match($this->price_unit) {
            'sqm' => max(1, (int) ceil($q * max($calculatedUnits, 1.0))),
            'meter' => max(1, (int) ceil($q * max($width / 100.0, 1.0))),
            'piece' => $q,
            default => $q,
        };
    }

    public function hasStockAvailable(int $quantity = 1, float $width = 150.0, float $calculatedUnits = 1.0): bool
    {
        $needed = $this->calculateStockNeeded($quantity, $width, $calculatedUnits);
        return $this->stock >= $needed;
    }

    public function scopeBestSellers($query, int $limit = 8)
    {
        return $query->where('is_active', true)
            ->select('products.*')
            ->selectSub(function ($q) {
                $q->from('order_items')
                  ->join('orders', 'order_items.order_id', '=', 'orders.id')
                  ->whereColumn('order_items.product_id', 'products.id')
                  ->where('orders.order_status', '!=', 'cancelled')
                  ->selectRaw('COALESCE(SUM(order_items.quantity), 0)');
            }, 'total_sold')
            ->orderByDesc('total_sold')
            ->take($limit);
    }

    public function getTotalSoldAttribute(): int
    {
        if (isset($this->attributes['total_sold'])) {
            return (int) $this->attributes['total_sold'];
        }

        return (int) OrderItem::where('product_id', $this->id)
            ->whereHas('order', function ($q) {
                $q->where('order_status', '!=', 'cancelled');
            })
            ->sum('quantity');
    }


    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('status', 'approved')->latest();
    }

    public function getAverageRatingAttribute()
    {
        $avg = $this->reviews()->where('status', 'approved')->avg('rating');
        return $avg ? round($avg, 1) : 5.0;
    }

    public function getApprovedReviewsCountAttribute()
    {
        return $this->reviews()->where('status', 'approved')->count();
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function getAllImagesAttribute(): array
    {
        $list = [];
        if ($this->image) {
            $list[] = $this->image;
        }
        foreach ($this->images as $img) {
            if (!in_array($img->image_url, $list)) {
                $list[] = $img->image_url;
            }
        }
        return count($list) > 0 ? $list : ['https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=600&q=80'];
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
}
