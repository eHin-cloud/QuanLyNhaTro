<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->index(['tenant_id', 'room_number'], 'rooms_tenant_room_number_idx');
            $table->index(['tenant_id', 'status'], 'rooms_tenant_status_idx');
            $table->index(['tenant_id', 'floor'], 'rooms_tenant_floor_idx');
            $table->index(['tenant_id', 'price'], 'rooms_tenant_price_idx');
        });

        Schema::table('residents', function (Blueprint $table) {
            $table->index(['tenant_id', 'name'], 'residents_tenant_name_idx');
            $table->index(['tenant_id', 'phone'], 'residents_tenant_phone_idx');
            $table->index(['tenant_id', 'cccd'], 'residents_tenant_cccd_idx');
            $table->index(['room_id', 'status'], 'residents_room_status_idx');
        });

        Schema::table('utility_records', function (Blueprint $table) {
            $table->index(['billing_month', 'status'], 'utility_records_month_status_idx');
            $table->index(['room_id', 'status'], 'utility_records_room_status_idx');
            $table->index(['payment_method', 'billing_month'], 'utility_records_method_month_idx');
        });
    }

    public function down(): void
    {
        Schema::table('utility_records', function (Blueprint $table) {
            $table->dropIndex('utility_records_method_month_idx');
            $table->dropIndex('utility_records_room_status_idx');
            $table->dropIndex('utility_records_month_status_idx');
        });

        Schema::table('residents', function (Blueprint $table) {
            $table->dropIndex('residents_room_status_idx');
            $table->dropIndex('residents_tenant_cccd_idx');
            $table->dropIndex('residents_tenant_phone_idx');
            $table->dropIndex('residents_tenant_name_idx');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropIndex('rooms_tenant_price_idx');
            $table->dropIndex('rooms_tenant_floor_idx');
            $table->dropIndex('rooms_tenant_status_idx');
            $table->dropIndex('rooms_tenant_room_number_idx');
        });
    }
};
