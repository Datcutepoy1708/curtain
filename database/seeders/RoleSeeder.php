<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $allPermissions = array_keys(
            collect(User::availablePermissions())->flatMap(fn($g) => $g['permissions'])->toArray()
        );

        $roles = [
            [
                'name' => 'Super Admin (Quản Trị Tối Cao)',
                'code' => 'ROLE_ADMIN',
                'description' => 'Toàn quyền điều hành và quản trị tất cả các phân hệ hệ thống',
                'permissions' => $allPermissions,
            ],
            [
                'name' => 'Quản Lý Showroom & Vận Hành',
                'code' => 'ROLE_MANAGER',
                'description' => 'Quản lý danh mục rèm, đơn hàng, khảo sát đo đạc và báo cáo doanh thu',
                'permissions' => [
                    'products.view', 'products.create', 'products.edit', 'options.manage',
                    'orders.view', 'orders.status',
                    'consultations.view', 'consultations.assign',
                    'banners.manage', 'news.manage', 'discounts.manage',
                    'customers.view', 'reviews.manage', 'statistics.view',
                ],
            ],
            [
                'name' => 'Nhân Viên Tư Vấn & Bán Hàng',
                'code' => 'ROLE_SALES',
                'description' => 'Tư vấn mẫu rèm, xử lý đơn đặt hàng, tiếp nhận lịch hẹn đo đạc của khách',
                'permissions' => [
                    'products.view',
                    'orders.view', 'orders.status',
                    'consultations.view', 'consultations.assign',
                    'customers.view',
                ],
            ],
            [
                'name' => 'Thợ Kỹ Thuật & Khảo Sát Đo Đạc',
                'code' => 'ROLE_TECHNICIAN',
                'description' => 'Trực tiếp khảo sát mang mẫu tận nhà, đo kích thước và cập nhật tiến độ thi công',
                'permissions' => [
                    'products.view',
                    'consultations.view', 'consultations.assign',
                    'orders.view',
                ],
            ],
            [
                'name' => 'Nhân Viên Nội Bộ',
                'code' => 'ROLE_STAFF',
                'description' => 'Hỗ trợ viết bài cẩm nang chọn rèm, kiểm tra thông tin sản phẩm',
                'permissions' => [
                    'products.view',
                    'news.manage',
                ],
            ],
        ];

        foreach ($roles as $r) {
            Role::updateOrCreate(['code' => $r['code']], $r);
        }
    }
}
