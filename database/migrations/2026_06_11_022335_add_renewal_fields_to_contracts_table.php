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
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('renewal_status')->nullable()->after('status'); // values: 'requested', 'approved', 'declined', 'renewed'
            $table->integer('renewal_months')->nullable()->after('renewal_status');
            $table->text('renewal_note')->nullable()->after('renewal_months');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['renewal_status', 'renewal_months', 'renewal_note']);
        });
    }
};
