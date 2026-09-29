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
        // 1. Discount Codes (Mã giảm giá)
        Schema::create('discount_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->string('discount_type')->default('percent'); // percent, fixed
            $table->decimal('discount_value', 12, 2)->default(0);
            $table->decimal('max_discount_amount', 12, 2)->nullable();
            $table->decimal('min_order_value', 12, 2)->default(0);
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
        });

        // 2. Banners & Sliders (Quảng cáo & Hero Banner)
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('image_url');
            $table->string('link_url')->nullable();
            $table->string('position')->default('hero'); // hero, promo, popup
            $table->integer('sort_order')->default(0);
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
        });

        // 3. News & Articles CMS (Tin tức & Cẩm nang rèm cửa)
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('thumbnail_url')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('category')->default('guide'); // guide, trends, news
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('published'); // published, draft
            $table->integer('views')->default(0);
            $table->timestamps();
        });

        // 4. Product Reviews (Đánh giá chất lượng rèm cửa)
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->tinyInteger('rating')->default(5);
            $table->text('comment');
            $table->string('status')->default('approved'); // approved, pending, hidden
            $table->timestamps();
        });

        // 5. System Settings (Cấu hình hệ thống)
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key')->unique();
            $table->text('setting_value')->nullable();
            $table->string('setting_group')->default('general'); // general, contact, bank, booking
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('product_reviews');
        Schema::dropIfExists('news');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('discount_codes');
    }
};
