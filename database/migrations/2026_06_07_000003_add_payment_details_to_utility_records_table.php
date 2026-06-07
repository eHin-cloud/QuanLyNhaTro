<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('utility_records', function (Blueprint $table) {
            if (!Schema::hasColumn('utility_records', 'payment_date')) {
                $table->timestamp('payment_date')->nullable()->after('status');
            }

            if (!Schema::hasColumn('utility_records', 'payment_method')) {
                $table->string('payment_method', 50)->nullable()->after('payment_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('utility_records', function (Blueprint $table) {
            if (Schema::hasColumn('utility_records', 'payment_method')) {
                $table->dropColumn('payment_method');
            }

            if (Schema::hasColumn('utility_records', 'payment_date')) {
                $table->dropColumn('payment_date');
            }
        });
    }
};
