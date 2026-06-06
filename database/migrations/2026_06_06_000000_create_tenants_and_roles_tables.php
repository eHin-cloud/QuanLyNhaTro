<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('bank_name')->default('MB'); // Tên viết tắt ngân hàng
            $table->string('bank_account_no')->nullable(); // Số tài khoản ngân hàng
            $table->string('bank_account_name')->nullable(); // Tên chủ tài khoản nhận tiền
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Chủ trọ, Cư dân, Khách vãng lai
            $table->string('slug')->unique(); // landlord, resident, guest
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
        Schema::dropIfExists('tenants');
    }
};
