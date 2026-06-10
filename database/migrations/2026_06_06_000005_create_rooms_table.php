<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained('buildings')->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('room_number');
            $table->integer('floor');
            $table->enum('status', ['empty', 'occupied', 'overdue'])->default('empty');
            $table->unsignedInteger('price'); // Giá thuê phòng VNĐ/tháng
            $table->unsignedInteger('area')->default(25); // Diện tích phòng m2
            $table->json('amenities')->nullable(); // Tiện ích dạng JSON
            $table->text('description')->nullable();
            $table->timestamps();

            // Đảm bảo số phòng là duy nhất trong một tòa nhà
            $table->unique(['building_id', 'room_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
