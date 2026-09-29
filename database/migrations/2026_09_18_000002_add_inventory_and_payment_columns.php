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
        // 1. Bổ sung các cột theo dõi tồn kho và giao dịch thanh toán vào bảng orders
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'stock_restored')) {
                $table->boolean('stock_restored')->default(false)->after('order_status');
            }
            if (!Schema::hasColumn('orders', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('orders', 'payment_gateway')) {
                $table->string('payment_gateway')->nullable()->after('payment_method'); // 'vnpay', 'vietqr', 'cod'
            }
            if (!Schema::hasColumn('orders', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('transaction_id');
            }
        });

        // 2. Bổ sung số lượng tồn kho đã khấu trừ vào từng dòng chi tiết đơn hàng (order_items)
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'stock_deducted')) {
                $table->integer('stock_deducted')->default(0)->after('quantity');
            }
        });

        // 3. Bổ sung liên kết đơn hàng và cờ xác thực người mua thật cho đánh giá sản phẩm (product_reviews)
        Schema::table('product_reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('product_reviews', 'order_id')) {
                $table->foreignId('order_id')->nullable()->after('user_id')->constrained('orders')->nullOnDelete();
            }
            if (!Schema::hasColumn('product_reviews', 'is_verified_buyer')) {
                $table->boolean('is_verified_buyer')->default(false)->after('comment');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            if (Schema::hasColumn('product_reviews', 'order_id')) {
                $table->dropForeign(['order_id']);
                $table->dropColumn('order_id');
            }
            if (Schema::hasColumn('product_reviews', 'is_verified_buyer')) {
                $table->dropColumn('is_verified_buyer');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'stock_deducted')) {
                $table->dropColumn('stock_deducted');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'stock_restored')) {
                $table->dropColumn('stock_restored');
            }
            if (Schema::hasColumn('orders', 'transaction_id')) {
                $table->dropColumn('transaction_id');
            }
            if (Schema::hasColumn('orders', 'payment_gateway')) {
                $table->dropColumn('payment_gateway');
            }
            if (Schema::hasColumn('orders', 'paid_at')) {
                $table->dropColumn('paid_at');
            }
        });
    }
};
