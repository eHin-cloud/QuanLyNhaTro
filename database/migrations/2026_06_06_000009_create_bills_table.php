<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->foreignId('electric_water_log_id')->nullable()->constrained('electric_water_logs')->onDelete('set null');
            $table->string('billing_month'); // Định dạng "YYYY-MM"
            $table->unsignedInteger('room_price');
            $table->unsignedInteger('electricity_usage')->default(0);
            $table->unsignedInteger('electricity_cost')->default(0);
            $table->unsignedInteger('water_usage')->default(0);
            $table->unsignedInteger('water_cost')->default(0);
            $table->unsignedInteger('service_cost')->default(150000);
            $table->unsignedInteger('total_amount');
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->timestamp('payment_date')->nullable();
            $table->text('vietqr_url')->nullable();
            $table->timestamps();

            $table->unique(['room_id', 'billing_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
