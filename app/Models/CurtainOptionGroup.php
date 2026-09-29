<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurtainOptionGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'applies_to',
        'is_required',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function values()
    {
        return $this->hasMany(CurtainOptionValue::class, 'group_id')->orderBy('sort_order');
    }
}
