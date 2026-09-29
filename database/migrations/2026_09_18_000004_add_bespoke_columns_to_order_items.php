<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm các cột mới vào order_items để hỗ trợ đơn hàng may đo (từ báo giá).
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'room_name')) {
                $table->string('room_name')->nullable()->after('product_name');
            }
            if (!Schema::hasColumn('order_items', 'room_label')) {
                $table->string('room_label')->nullable()->after('room_name');
            }
            if (!Schema::hasColumn('order_items', 'width')) {
                $table->decimal('width', 8, 1)->nullable()->after('room_label');
            }
            if (!Schema::hasColumn('order_items', 'height')) {
                $table->decimal('height', 8, 1)->nullable()->after('width');
            }
            if (!Schema::hasColumn('order_items', 'install_type')) {
                $table->string('install_type')->nullable()->after('height');
            }
            if (!Schema::hasColumn('order_items', 'mount_type')) {
                $table->string('mount_type')->nullable()->after('install_type');
            }
            if (!Schema::hasColumn('order_items', 'calculated_units')) {
                $table->decimal('calculated_units', 8, 2)->nullable()->after('mount_type');
            }
            if (!Schema::hasColumn('order_items', 'unit_price')) {
                $table->decimal('unit_price', 14, 0)->nullable()->after('calculated_units');
            }
            if (!Schema::hasColumn('order_items', 'selected_options')) {
                $table->text('selected_options')->nullable()->after('unit_price');
            }
            if (!Schema::hasColumn('order_items', 'options_json')) {
                $table->text('options_json')->nullable()->after('selected_options');
            }
            if (!Schema::hasColumn('order_items', 'stock_deducted')) {
                $table->integer('stock_deducted')->default(0)->after('options_json');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $cols = ['room_name', 'room_label', 'width', 'height', 'install_type', 'mount_type',
                     'calculated_units', 'unit_price', 'selected_options', 'options_json', 'stock_deducted'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('order_items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
