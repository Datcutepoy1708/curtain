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
        Schema::table('products', function (Blueprint $table) {
            $table->string('price_unit')->default('sqm')->after('sale_price'); // 'sqm' (m2), 'meter' (mét ngang), 'piece' (bộ)
            $table->decimal('min_area', 4, 2)->default(1.00)->after('price_unit');
            $table->integer('min_width')->default(40)->after('min_area'); // cm
            $table->integer('max_width')->default(350)->after('min_width'); // cm
            $table->integer('min_height')->default(50)->after('max_width'); // cm
            $table->integer('max_height')->default(400)->after('min_height'); // cm
            $table->tinyInteger('blackout_rate')->default(100)->after('max_height'); // % cản sáng (70, 80, 90, 100)
            $table->string('installation_type')->default('indoor')->after('blackout_rate'); // 'indoor', 'outdoor', 'both'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'price_unit',
                'min_area',
                'min_width',
                'max_width',
                'min_height',
                'max_height',
                'blackout_rate',
                'installation_type',
            ]);
        });
    }
};
