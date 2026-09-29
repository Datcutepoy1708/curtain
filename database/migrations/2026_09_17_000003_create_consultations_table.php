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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // LH-20260901-XXX
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->string('address');
            $table->string('city')->default('Hồ Chí Minh');
            $table->string('district')->nullable();
            $table->string('ward')->nullable();
            $table->date('preferred_date');
            $table->string('preferred_time')->nullable(); // 'morning' (8h-11h), 'afternoon' (14h-17h), 'evening' (18h-20h)
            $table->json('curtain_types_interested')->nullable(); // ["rem-vai-2-lop", "rem-cau-vong"]
            $table->integer('estimated_windows')->default(1);
            $table->text('notes')->nullable();
            $table->string('status')->default('pending'); // 'pending', 'assigned', 'surveying', 'quoted', 'completed', 'cancelled'
            $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('quotation_amount', 14, 0)->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
