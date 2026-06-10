<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('code', 50);
            $table->string('name', 150);
            $table->string('unit', 30)->default('cai');
            $table->unsignedInteger('total_quantity')->default(0);
            $table->unsignedInteger('allocated_quantity')->default(0);
            $table->text('description')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();

            $table->unique(['tenant_id', 'code']);
        });

        Schema::create('room_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->unsignedInteger('quantity')->default(0);
            $table->timestamp('last_allocated_at')->nullable();
            $table->timestamp('last_recovered_at')->nullable();
            $table->timestamps();

            $table->unique(['room_id', 'equipment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_equipment');
        Schema::dropIfExists('equipment');
    }
};
