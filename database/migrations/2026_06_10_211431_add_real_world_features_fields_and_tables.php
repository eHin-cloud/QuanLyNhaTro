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
        // 1. Cập nhật cấu hình payment gateway cho tenants
        if (!Schema::hasColumn('tenants', 'payment_gateway_config')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->json('payment_gateway_config')->nullable()->after('bank_account_name');
            });
        }

        // 2. Cập nhật xác thực OTP & Ký số cho hợp đồng
        Schema::table('contracts', function (Blueprint $table) {
            if (!Schema::hasColumn('contracts', 'is_signed')) {
                $table->boolean('is_signed')->default(false)->after('status');
            }
            if (!Schema::hasColumn('contracts', 'signed_at')) {
                $table->timestamp('signed_at')->nullable()->after('is_signed');
            }
            if (!Schema::hasColumn('contracts', 'otp_code')) {
                $table->string('otp_code', 6)->nullable()->after('signed_at');
            }
            if (!Schema::hasColumn('contracts', 'otp_expires_at')) {
                $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
            }
            if (!Schema::hasColumn('contracts', 'signer_ip')) {
                $table->string('signer_ip', 45)->nullable()->after('otp_expires_at');
            }
        });

        // 3. Tạo bảng Transactions phục vụ Sổ Quỹ Thu Chi
        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id');
                $table->string('type'); // 'income' (thu) hoặc 'expense' (chi)
                $table->decimal('amount', 15, 2);
                $table->string('category'); // 'rental', 'repair', 'utility', 'marketing', 'other'
                $table->string('description', 500)->nullable();
                $table->date('transaction_date');
                $table->timestamps();

                $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');

        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['is_signed', 'signed_at', 'otp_code', 'otp_expires_at', 'signer_ip']);
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('payment_gateway_config');
        });
    }
};
