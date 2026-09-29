<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'system_settings';

    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_group',
        'description',
    ];

    public static function get(string $key, $default = null)
    {
        $item = static::where('setting_key', $key)->first();
        return $item ? $item->setting_value : $default;
    }

    public static function set(string $key, $value, string $group = 'general', ?string $desc = null): void
    {
        static::updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => $value,
                'setting_group' => $group,
                'description' => $desc,
            ]
        );
    }
}
