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
        Schema::table('consultations', function (Blueprint $table) {
            $table->string('service_mode', 30)->default('home_survey')->after('status');
            $table->string('province_code', 10)->nullable()->after('address');
            $table->string('province_name', 100)->nullable()->after('province_code');
            $table->string('contact_method', 30)->nullable()->after('service_mode');
            $table->boolean('sample_box_requested')->default(false)->after('contact_method');
            $table->string('sample_box_status', 30)->default('none')->after('sample_box_requested');
            $table->string('shipping_tracking_code', 100)->nullable()->after('sample_box_status');
            $table->string('measurement_status', 30)->default('pending')->after('shipping_tracking_code');
            $table->boolean('fit_guarantee_eligible')->default(false)->after('measurement_status');
            $table->timestamp('fit_guarantee_confirmed_at')->nullable()->after('fit_guarantee_eligible');
            $table->foreignId('fit_guarantee_confirmed_by')
                ->nullable()
                ->after('fit_guarantee_confirmed_at')
                ->constrained('users')
                ->nullOnDelete();
            $table->string('installation_method', 40)->nullable()->after('fit_guarantee_confirmed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropForeign(['fit_guarantee_confirmed_by']);
            $table->dropColumn([
                'service_mode',
                'province_code',
                'province_name',
                'contact_method',
                'sample_box_requested',
                'sample_box_status',
                'shipping_tracking_code',
                'measurement_status',
                'fit_guarantee_eligible',
                'fit_guarantee_confirmed_at',
                'fit_guarantee_confirmed_by',
                'installation_method',
            ]);
        });
    }
};
