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
        // 1. Bảng lưu từng ô cửa đo đạc thực tế tại nhà khách
        Schema::create('consultation_windows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->constrained('consultations')->cascadeOnDelete();
            $table->string('room_name'); // e.g., "Phòng khách - Cửa ban công", "Phòng ngủ Master"
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('product_name');
            $table->decimal('width', 8, 1); // Chiều rộng (cm)
            $table->decimal('height', 8, 1); // Chiều cao (cm)
            $table->string('install_type')->default('inside'); // inside: Lọt lòng, outside: Phủ bì
            $table->string('fabric_color')->nullable(); // Mã màu vải hoặc tên màu
            $table->boolean('has_sheer')->default(false); // Lớp voan lấy sáng
            $table->string('sewing_style')->default('wave'); // wave: May định hình sóng, pleat: Xếp ly, eyelet: Ore, roman: Rèm Roman
            $table->string('motor_type')->default('manual'); // manual: Ray cơ, smart_wifi: Động cơ Tuya, somfy: Động cơ Somfy
            $table->integer('quantity')->default(1);
            $table->decimal('estimated_price', 14, 0)->default(0);
            $table->string('photo_path')->nullable(); // Ảnh chụp hiện trạng ô cửa
            $table->text('notes')->nullable(); // Ghi chú kỹ thuật: thạch cao, vướng điều hòa...
            $table->timestamps();
        });

        // 2. Bảng lưu bản báo giá chính thức có phiên bản (v1, v2...)
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_code')->unique(); // e.g., BG-20260918-001
            $table->foreignId('consultation_id')->constrained('consultations')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->integer('version')->default(1); // 1, 2, 3...
            $table->string('status')->default('draft'); // draft, sent, revision_requested, accepted, rejected, converted
            $table->decimal('subtotal', 14, 0)->default(0);
            $table->decimal('discount_amount', 14, 0)->default(0);
            $table->decimal('installation_fee', 14, 0)->default(0);
            $table->decimal('total_amount', 14, 0)->default(0);
            $table->integer('deposit_percent')->default(30); // 30%, 50%
            $table->decimal('deposit_amount', 14, 0)->default(0);
            $table->date('valid_until')->nullable(); // Thời hạn hiệu lực
            $table->date('estimated_delivery_date')->nullable(); // Ngày lắp đặt dự kiến
            $table->text('customer_notes')->nullable(); // Phản hồi của khách khi duyệt hoặc yêu cầu sửa
            $table->text('admin_notes')->nullable(); // Ghi chú nội bộ của thợ/nhân viên
            $table->timestamps();
        });

        // 3. Bảng lưu từng dòng chi tiết của bản báo giá
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotations')->cascadeOnDelete();
            $table->foreignId('consultation_window_id')->nullable()->constrained('consultation_windows')->nullOnDelete();
            $table->string('room_name');
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('product_name');
            $table->decimal('width', 8, 1);
            $table->decimal('height', 8, 1);
            $table->string('install_type')->default('inside');
            $table->decimal('calculated_units', 8, 2)->default(1); // mét ngang hoặc m²
            $table->string('unit_label')->default('mét ngang');
            $table->decimal('unit_price', 14, 0)->default(0);
            $table->decimal('fabric_cost', 14, 0)->default(0);
            $table->decimal('options_cost', 14, 0)->default(0);
            $table->text('options_detail')->nullable(); // JSON cấu hình
            $table->integer('quantity')->default(1);
            $table->decimal('subtotal', 14, 0)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 4. Bổ sung trường liên kết vào bảng consultations và orders
        if (Schema::hasTable('consultations') && !Schema::hasColumn('consultations', 'current_quotation_id')) {
            Schema::table('consultations', function (Blueprint $table) {
                $table->foreignId('current_quotation_id')->nullable()->constrained('quotations')->nullOnDelete();
                $table->decimal('estimated_amount', 14, 0)->nullable();
            });
        }

        if (Schema::hasTable('orders') && !Schema::hasColumn('orders', 'quotation_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('quotation_id')->nullable()->constrained('quotations')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'quotation_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['quotation_id']);
                $table->dropColumn('quotation_id');
            });
        }

        if (Schema::hasTable('consultations') && Schema::hasColumn('consultations', 'current_quotation_id')) {
            Schema::table('consultations', function (Blueprint $table) {
                $table->dropForeign(['current_quotation_id']);
                $table->dropColumn(['current_quotation_id', 'estimated_amount']);
            });
        }

        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('consultation_windows');
    }
};
