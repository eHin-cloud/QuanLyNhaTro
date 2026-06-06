<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('utility_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->string('billing_month'); // Format "YYYY-MM" (e.g., "2026-06")
            $table->unsignedInteger('old_electricity');
            $table->unsignedInteger('new_electricity');
            $table->unsignedInteger('old_water');
            $table->unsignedInteger('new_water');
            $table->unsignedInteger('electricity_price')->default(3500); // 3,500 VND/kWh
            $table->unsignedInteger('water_price')->default(15000);     // 15,000 VND/m3
            $table->enum('status', ['draft', 'sent', 'paid'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utility_records');
    }
};
