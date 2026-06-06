<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('electric_water_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->string('billing_month'); // Định dạng "YYYY-MM"
            $table->unsignedInteger('old_electricity');
            $table->unsignedInteger('new_electricity');
            $table->unsignedInteger('old_water');
            $table->unsignedInteger('new_water');
            $table->unsignedInteger('electricity_price')->default(3500);
            $table->unsignedInteger('water_price')->default(15000);
            $table->timestamps();

            // Đảm bảo mỗi phòng chỉ chốt điện nước 1 lần/tháng
            $table->unique(['room_id', 'billing_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('electric_water_logs');
    }
};
