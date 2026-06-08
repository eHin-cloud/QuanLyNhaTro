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
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->string('billing_month');
            $table->unsignedInteger('old_electricity')->default(0);
            $table->unsignedInteger('new_electricity')->default(0);
            $table->unsignedInteger('old_water')->default(0);
            $table->unsignedInteger('new_water')->default(0);
            $table->unsignedInteger('electricity_price')->default(3500);
            $table->unsignedInteger('water_price')->default(15000);
            $table->enum('status', ['sent', 'paid', 'overdue'])->default('sent');
            $table->timestamps();

            $table->unique(['room_id', 'billing_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utility_records');
    }
};
