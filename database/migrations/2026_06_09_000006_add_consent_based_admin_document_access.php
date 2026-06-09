<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landlord_verification_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('landlord_verification_requests', 'admin_review_consent_given')) {
                $table->boolean('admin_review_consent_given')->default(false)->after('cccd_number_blind_index');
            }
            if (!Schema::hasColumn('landlord_verification_requests', 'admin_review_consent_at')) {
                $table->timestamp('admin_review_consent_at')->nullable()->after('admin_review_consent_given');
            }
            if (!Schema::hasColumn('landlord_verification_requests', 'admin_review_consent_ip')) {
                $table->string('admin_review_consent_ip', 45)->nullable()->after('admin_review_consent_at');
            }
            if (!Schema::hasColumn('landlord_verification_requests', 'default_document_access_revoked_at')) {
                $table->timestamp('default_document_access_revoked_at')->nullable()->after('admin_review_consent_ip');
            }
        });

        Schema::create('admin_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('target_landlord_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('verification_request_id')->nullable()->constrained('landlord_verification_requests')->nullOnDelete();
            $table->foreignId('document_id')->nullable()->constrained('landlord_verification_documents')->nullOnDelete();
            $table->string('document_type', 80);
            $table->string('access_type', 40);
            $table->text('reason');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('presigned_url_expires_at')->nullable();
            $table->string('prev_hash', 64)->nullable();
            $table->string('row_hash', 64);
            $table->timestamp('created_at')->useCurrent();

            $table->index(['admin_user_id', 'created_at'], 'admin_access_logs_admin_created_idx');
            $table->index(['target_landlord_id', 'created_at'], 'admin_access_logs_landlord_created_idx');
            $table->index(['document_id', 'created_at'], 'admin_access_logs_document_created_idx');
            $table->index(['tenant_id', 'created_at'], 'admin_access_logs_tenant_created_idx');
        });

        $this->createAppendOnlyTriggers();
    }

    public function down(): void
    {
        $this->dropAppendOnlyTriggers();
        Schema::dropIfExists('admin_access_logs');

        Schema::table('landlord_verification_requests', function (Blueprint $table) {
            foreach ([
                'admin_review_consent_given',
                'admin_review_consent_at',
                'admin_review_consent_ip',
                'default_document_access_revoked_at',
            ] as $column) {
                if (Schema::hasColumn('landlord_verification_requests', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    private function createAppendOnlyTriggers(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared("CREATE TRIGGER admin_access_logs_no_update BEFORE UPDATE ON admin_access_logs FOR EACH ROW BEGIN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'admin_access_logs is append-only'; END");
        DB::unprepared("CREATE TRIGGER admin_access_logs_no_delete BEFORE DELETE ON admin_access_logs FOR EACH ROW BEGIN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'admin_access_logs is append-only'; END");
    }

    private function dropAppendOnlyTriggers(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP TRIGGER IF EXISTS admin_access_logs_no_update');
        DB::unprepared('DROP TRIGGER IF EXISTS admin_access_logs_no_delete');
    }
};
