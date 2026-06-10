<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->foreignId('resident_id')->constrained('residents')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('image_path')->nullable();
            $table->string('category')->default('khác'); // điện, nước, nội thất, khác
            $table->enum('status', ['pending', 'processing', 'resolved'])->default('pending');
            $table->string('assigned_to')->nullable(); // Thợ phân công sửa
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
