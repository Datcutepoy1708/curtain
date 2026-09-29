<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'module',
        'description',
        'target_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to quickly record an audit trail entry.
     */
    public static function record(
        string $action,
        string $module,
        string $description,
        ?string $targetId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): self {
        $user = Auth::user();

        return self::create([
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : 'Khách / Hệ thống',
            'user_role' => $user ? $user->role : 'system',
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'target_id' => $targetId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip() ?? '127.0.0.1',
            'user_agent' => substr(Request::userAgent() ?? 'CLI / Browser', 0, 500),
            'created_at' => now(),
        ]);
    }

    public static function modules(): array
    {
        return [
            'auth' => 'Xác thực & Đăng nhập',
            'orders' => 'Đơn hàng',
            'consultations' => 'Khảo sát & Đo đạc',
            'quotations' => 'Báo giá',
            'products' => 'Sản phẩm & Rèm',
            'options' => 'Tùy chọn ray phụ kiện',
            'discounts' => 'Mã giảm giá (Coupon)',
            'faqs' => 'Hỏi đáp (FAQ)',
            'staff' => 'Nhân sự & Phân quyền',
            'settings' => 'Cài đặt hệ thống',
        ];
    }

    public static function actions(): array
    {
        return [
            'login' => ['label' => 'Đăng nhập', 'badge' => 'badge-info'],
            'logout' => ['label' => 'Đăng xuất', 'badge' => 'badge-neutral'],
            'create' => ['label' => 'Tạo mới', 'badge' => 'badge-success'],
            'update' => ['label' => 'Cập nhật', 'badge' => 'badge-blue'],
            'delete' => ['label' => 'Xóa', 'badge' => 'badge-danger'],
            'assign' => ['label' => 'Phân công', 'badge' => 'badge-purple'],
            'status' => ['label' => 'Đổi trạng thái', 'badge' => 'badge-warning'],
            'send' => ['label' => 'Gửi dữ liệu', 'badge' => 'badge-info'],
        ];
    }

    public function getActionBadgeAttribute(): array
    {
        $actions = self::actions();
        return $actions[$this->action] ?? ['label' => $this->action, 'badge' => 'badge-neutral'];
    }

    public function getModuleLabelAttribute(): string
    {
        $modules = self::modules();
        return $modules[$this->module] ?? ucfirst($this->module);
    }
}
