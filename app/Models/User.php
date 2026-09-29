<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'permissions',
        'phone',
        'status',
        'avatar',
        'provider',
        'provider_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isTechnician(): bool
    {
        return $this->role === 'technician';
    }

    public function isSales(): bool
    {
        return $this->role === 'sales';
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['admin', 'manager', 'technician', 'sales', 'staff']);
    }

    public function hasPermission(string $permissionKey): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $userPerms = is_array($this->permissions) ? $this->permissions : [];
        if (!empty($userPerms)) {
            return in_array($permissionKey, $userPerms);
        }

        // Inherit from role if user-specific permissions are empty
        $role = Role::where('code', $this->role)
            ->orWhere('code', 'ROLE_' . strtoupper($this->role))
            ->first();

        return $role ? $role->hasPermission($permissionKey) : false;
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function getRoleNameAttribute(): string
    {
        return match($this->role) {
            'admin' => 'Super Admin (Quản Trị)',
            'manager' => 'Quản Lý Cửa Hàng',
            'technician' => 'Kỹ Thuật / Thợ Đo Đạc',
            'sales' => 'Nhân Viên Bán Hàng',
            'staff' => 'Nhân Viên',
            default => 'Khách Hàng',
        };
    }

    public function getRoleBadgeClassAttribute(): string
    {
        return match($this->role) {
            'admin' => 'badge-success',
            'manager' => 'badge-blue',
            'technician' => 'badge-purple',
            'sales' => 'badge-warning',
            default => 'badge-neutral',
        };
    }

    public static function availablePermissions(): array
    {
        return [
            'products' => [
                'label' => 'Sản Phẩm & Phụ Kiện Rèm',
                'permissions' => [
                    'products.view' => 'Xem danh sách mẫu rèm & danh mục',
                    'products.create' => 'Thêm mẫu rèm & danh mục mới',
                    'products.edit' => 'Chỉnh sửa mẫu rèm & danh mục',
                    'products.delete' => 'Xóa mẫu rèm & danh mục',
                    'options.manage' => 'Quản lý tùy chọn thanh ray & phụ kiện',
                ]
            ],
            'orders' => [
                'label' => 'Đơn Hàng & Thi Công Cắt May',
                'permissions' => [
                    'orders.view' => 'Xem danh sách đơn đặt rèm',
                    'orders.status' => 'Cập nhật tiến độ & trạng thái đơn',
                    'orders.delete' => 'Xóa hoặc hủy đơn hàng',
                ]
            ],
            'consultations' => [
                'label' => 'Khảo Sát & Đo Đạc Tận Nhà',
                'permissions' => [
                    'consultations.view' => 'Xem danh sách lịch hẹn đo đạc',
                    'consultations.assign' => 'Phân công & cập nhật kết quả đo',
                    'consultations.delete' => 'Xóa lịch hẹn khảo sát',
                ]
            ],
            'marketing' => [
                'label' => 'Marketing & Nội Dung',
                'permissions' => [
                    'banners.manage' => 'Quản lý banner & quảng cáo',
                    'news.manage' => 'Quản lý cẩm nang chọn rèm',
                    'discounts.manage' => 'Quản lý mã giảm giá coupon',
                ]
            ],
            'customers' => [
                'label' => 'Khách Hàng & Đánh Giá',
                'permissions' => [
                    'customers.view' => 'Xem danh sách khách hàng',
                    'reviews.manage' => 'Kiểm duyệt & xóa đánh giá',
                ]
            ],
            'system' => [
                'label' => 'Quản Trị & Nhân Sự',
                'permissions' => [
                    'staff.manage' => 'Quản lý nhân viên & phân quyền',
                    'settings.manage' => 'Cấu hình hệ thống & thông tin showroom',
                    'statistics.view' => 'Xem báo cáo doanh số & thống kê',
                ]
            ],
        ];
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->name));
        if (count($words) >= 2) {
            return mb_strtoupper(mb_substr($words[0], 0, 1) . mb_substr(end($words), 0, 1));
        }
        return mb_strtoupper(mb_substr($this->name, 0, 2));
    }

    public function getFormattedPhoneAttribute(): string
    {
        if (!$this->phone) {
            return '';
        }
        $digits = preg_replace('/\D/', '', $this->phone);
        if (strlen($digits) === 10) {
            return substr($digits, 0, 4) . ' ' . substr($digits, 4, 3) . ' ' . substr($digits, 7, 3);
        }
        return $this->phone;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }
}
