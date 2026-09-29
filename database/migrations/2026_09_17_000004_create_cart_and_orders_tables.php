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
        // 1. Giỏ hàng (Cart Items) với các thông số đo đạc rèm
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable()->index(); // Session for guest users
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('room_label')->nullable(); // Vd: "Cửa phòng khách", "Ban công master"
            $table->decimal('width', 6, 1)->default(150.0); // Chiều rộng thực tế (cm)
            $table->decimal('height', 6, 1)->default(220.0); // Chiều cao thực tế (cm)
            $table->string('mount_type')->default('outside'); // 'inside' (lọt lòng), 'outside' (phủ bì)
            $table->decimal('calculated_units', 6, 2)->default(1.00); // Số m2 hoặc mét ngang sau làm tròn
            $table->decimal('unit_price', 12, 0); // Đơn giá cơ sở tại thời điểm thêm
            $table->json('selected_options')->nullable(); // JSON lưu các options chọn thêm (motor, kiểu may, ray...)
            $table->decimal('subtotal', 12, 0); // Tổng thành tiền sau khi tính options và diện tích
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });

        // 2. Đơn hàng (Orders)
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique(); // DH-20260901-XXX
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->string('shipping_address');
            $table->string('city')->default('Hồ Chí Minh');
            $table->string('district')->nullable();
            $table->string('ward')->nullable();
            $table->decimal('total_amount', 14, 0)->default(0);
            $table->string('payment_method')->default('cod'); // 'cod', 'bank_transfer', 'deposit'
            $table->string('payment_status')->default('pending'); // 'pending', 'paid', 'partially_paid'
            $table->string('order_status')->default('pending'); // 'pending', 'confirmed', 'manufacturing', 'shipping', 'installed', 'completed', 'cancelled'
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 3. Chi tiết đơn hàng (Order Items)
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('product_name');
            $table->string('room_label')->nullable(); // Vd: "Cửa sổ phòng ngủ"
            $table->decimal('width', 6, 1)->default(100.0);
            $table->decimal('height', 6, 1)->default(200.0);
            $table->string('mount_type')->default('outside');
            $table->decimal('calculated_units', 6, 2)->default(1.00);
            $table->decimal('unit_price', 12, 0);
            $table->json('selected_options')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('subtotal', 12, 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('cart_items');
    }
};
