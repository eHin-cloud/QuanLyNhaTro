<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->string('type', 50);
            $table->string('channel', 30);
            $table->string('recipient_name')->nullable();
            $table->string('recipient_contact')->nullable();
            $table->string('subject');
            $table->text('message');
            $table->string('status', 30)->default('sent');
            $table->string('target_type')->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'type', 'created_at'], 'notification_logs_tenant_type_created_idx');
            $table->index(['tenant_id', 'channel', 'created_at'], 'notification_logs_tenant_channel_created_idx');
            $table->index(['target_type', 'target_id'], 'notification_logs_target_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
