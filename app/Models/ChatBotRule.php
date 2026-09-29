<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatBotRule extends Model
{
    use HasFactory;

    protected $primaryKey = 'rule_id';

    protected $fillable = [
        'rule_name',
        'keywords',
        'match_type',
        'response_message',
        'quick_replies',
        'action_type',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'quick_replies' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderByDesc('priority')->orderBy('rule_id');
    }

    protected static function stripVietnameseDiacritics(string $str): string
    {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/u", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/u", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/u", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/u", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/u", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/u", 'y', $str);
        $str = preg_replace("/(đ)/u", 'd', $str);
        return mb_strtolower(trim($str), 'UTF-8');
    }

    public function matches(string $message): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $messageLower = mb_strtolower(trim($message), 'UTF-8');
        $messageStripped = self::stripVietnameseDiacritics($message);
        $keywords = array_filter(array_map('trim', explode(',', $this->keywords)));

        switch ($this->match_type) {
            case 'EXACT':
                foreach ($keywords as $kw) {
                    $kwLower = mb_strtolower($kw, 'UTF-8');
                    $kwStripped = self::stripVietnameseDiacritics($kw);
                    if ($messageLower === $kwLower || $messageStripped === $kwStripped) {
                        return true;
                    }
                }
                return false;

            case 'REGEX':
                foreach ($keywords as $pattern) {
                    if (@preg_match($pattern, $message)) {
                        return true;
                    }
                }
                return false;

            case 'CONTAINS':
            default:
                foreach ($keywords as $kw) {
                    $kwLower = mb_strtolower($kw, 'UTF-8');
                    $kwStripped = self::stripVietnameseDiacritics($kw);
                    if (!empty($kwLower) && str_contains($messageLower, $kwLower)) {
                        return true;
                    }
                    if (!empty($kwStripped) && str_contains($messageStripped, $kwStripped)) {
                        return true;
                    }
                }
                return false;
        }
    }
}
