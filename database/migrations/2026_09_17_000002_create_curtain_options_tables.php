<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Nhóm tùy chọn (Kiểu may, Ray treo, Động cơ, Lớp voan)
        Schema::create('curtain_option_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // Tên hiển thị: "Kiểu May Rèm", "Hệ Thanh Ray", "Động Cơ Thông Minh", "Lớp Voan"
            $table->string('code')->unique();    // 'header_style', 'track_type', 'motor_type', 'sheer_layer'
            $table->string('applies_to')->default('all'); // 'all', 'fabric' (rèm vải), 'roller' (rèm cuốn), 'wooden' (rèm gỗ)
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 2. Chi tiết từng tùy chọn & Phụ phí cộng thêm
        Schema::create('curtain_option_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('curtain_option_groups')->onDelete('cascade');
            $table->string('name');              // Ví dụ: "May định hình (Wave fold)", "Ray bi chống ồn", "Động cơ Tuya Zigbee"
            $table->string('image_url')->nullable(); // Ảnh minh họa kiểu dáng / phụ kiện
            $table->string('price_impact_type')->default('fixed'); // 'fixed' (cố định), 'per_meter' (theo mét ngang), 'per_sqm' (theo m2)
            $table->decimal('extra_price', 12, 0)->default(0); // Số tiền cộng thêm (VNĐ)
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curtain_option_values');
        Schema::dropIfExists('curtain_option_groups');
    }
};
