<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->dropLegacyIndexes();
        $this->widenSensitiveColumns();

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone_blind_index')) {
                $table->string('phone_blind_index', 64)->nullable()->after('phone');
                $table->unique('phone_blind_index', 'users_phone_blind_unique');
            }
        });

        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'phone_blind_index')) {
                $table->string('phone_blind_index', 64)->nullable()->after('phone')->index('tenants_phone_blind_idx');
            }
            if (!Schema::hasColumn('tenants', 'bank_account_no_blind_index')) {
                $table->string('bank_account_no_blind_index', 64)->nullable()->after('bank_account_no')->index('tenants_bank_account_blind_idx');
            }
        });

        Schema::table('residents', function (Blueprint $table) {
            if (!Schema::hasColumn('residents', 'phone_blind_index')) {
                $table->string('phone_blind_index', 64)->nullable()->after('phone');
                $table->index(['tenant_id', 'phone_blind_index'], 'residents_tenant_phone_blind_idx');
            }
            if (!Schema::hasColumn('residents', 'cccd_blind_index')) {
                $table->string('cccd_blind_index', 64)->nullable()->after('cccd');
                $table->index(['tenant_id', 'cccd_blind_index'], 'residents_tenant_cccd_blind_idx');
            }
        });

        Schema::table('resident_relatives', function (Blueprint $table) {
            if (!Schema::hasColumn('resident_relatives', 'phone_blind_index')) {
                $table->string('phone_blind_index', 64)->nullable()->after('phone')->index('relatives_phone_blind_idx');
            }
            if (!Schema::hasColumn('resident_relatives', 'cccd_blind_index')) {
                $table->string('cccd_blind_index', 64)->nullable()->after('cccd')->index('relatives_cccd_blind_idx');
            }
        });

        Schema::table('landlord_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('landlord_profiles', 'phone_blind_index')) {
                $table->string('phone_blind_index', 64)->nullable()->after('phone')->index('landlord_profiles_phone_blind_idx');
            }
        });

        Schema::table('landlord_verification_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('landlord_verification_requests', 'cccd_number')) {
                $table->text('cccd_number')->nullable()->after('type');
            }
            if (!Schema::hasColumn('landlord_verification_requests', 'cccd_number_blind_index')) {
                $table->string('cccd_number_blind_index', 64)->nullable()->after('cccd_number')->index('verification_cccd_blind_idx');
            }
        });

        Schema::table('landlord_verification_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('landlord_verification_documents', 'disk')) {
                $table->string('disk', 80)->default('private_documents')->after('document_type');
            }
            if (!Schema::hasColumn('landlord_verification_documents', 'original_filename')) {
                $table->string('original_filename')->nullable()->after('file_path');
            }
            if (!Schema::hasColumn('landlord_verification_documents', 'mime_type')) {
                $table->string('mime_type', 120)->nullable()->after('original_filename');
            }
            if (!Schema::hasColumn('landlord_verification_documents', 'size_bytes')) {
                $table->unsignedBigInteger('size_bytes')->nullable()->after('mime_type');
            }
            if (!Schema::hasColumn('landlord_verification_documents', 'sha256_checksum')) {
                $table->string('sha256_checksum', 64)->nullable()->after('size_bytes');
            }
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 120);
            $table->string('resource_type', 120);
            $table->string('resource_id', 80);
            $table->json('sensitive_fields')->nullable();
            $table->text('reason')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->uuid('request_id');
            $table->string('prev_hash', 64)->nullable();
            $table->string('row_hash', 64);
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tenant_id', 'created_at'], 'audit_logs_tenant_created_idx');
            $table->index(['actor_user_id', 'created_at'], 'audit_logs_actor_created_idx');
            $table->index(['resource_type', 'resource_id'], 'audit_logs_resource_idx');
        });

        $this->createAuditLogImmutabilityTriggers();
    }

    public function down(): void
    {
        $this->dropAuditLogImmutabilityTriggers();
        Schema::dropIfExists('audit_logs');

        $this->dropColumnsIfPresent('landlord_verification_documents', [
            'disk',
            'original_filename',
            'mime_type',
            'size_bytes',
            'sha256_checksum',
        ]);

        $this->dropColumnsIfPresent('landlord_verification_requests', [
            'cccd_number',
            'cccd_number_blind_index',
        ]);

        $this->dropColumnsIfPresent('landlord_profiles', ['phone_blind_index']);
        $this->dropColumnsIfPresent('resident_relatives', ['phone_blind_index', 'cccd_blind_index']);
        $this->dropColumnsIfPresent('residents', ['phone_blind_index', 'cccd_blind_index']);
        $this->dropColumnsIfPresent('tenants', ['phone_blind_index', 'bank_account_no_blind_index']);
        $this->dropColumnsIfPresent('users', ['phone_blind_index']);
    }

    private function dropLegacyIndexes(): void
    {
        foreach ([
            ['users', 'users_phone_unique'],
            ['residents', 'residents_tenant_phone_idx'],
            ['residents', 'residents_tenant_cccd_idx'],
        ] as [$table, $index]) {
            try {
                Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropIndex($index));
            } catch (\Throwable) {
                try {
                    Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropUnique($index));
                } catch (\Throwable) {
                    // Index may not exist in older local databases.
                }
            }
        }
    }

    private function widenSensitiveColumns(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        foreach ([
            'ALTER TABLE users MODIFY phone TEXT NULL',
            'ALTER TABLE tenants MODIFY phone TEXT NULL',
            'ALTER TABLE tenants MODIFY bank_account_no TEXT NULL',
            'ALTER TABLE residents MODIFY phone TEXT NULL',
            'ALTER TABLE residents MODIFY cccd TEXT NULL',
            'ALTER TABLE resident_relatives MODIFY phone TEXT NULL',
            'ALTER TABLE resident_relatives MODIFY cccd TEXT NULL',
            'ALTER TABLE landlord_profiles MODIFY phone TEXT NULL',
        ] as $statement) {
            DB::statement($statement);
        }
    }

    private function createAuditLogImmutabilityTriggers(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared("CREATE TRIGGER audit_logs_no_update BEFORE UPDATE ON audit_logs FOR EACH ROW BEGIN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'audit_logs is append-only'; END");
        DB::unprepared("CREATE TRIGGER audit_logs_no_delete BEFORE DELETE ON audit_logs FOR EACH ROW BEGIN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'audit_logs is append-only'; END");
    }

    private function dropAuditLogImmutabilityTriggers(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP TRIGGER IF EXISTS audit_logs_no_update');
        DB::unprepared('DROP TRIGGER IF EXISTS audit_logs_no_delete');
    }

    /**
     * @param array<int, string> $columns
     */
    private function dropColumnsIfPresent(string $table, array $columns): void
    {
        $existing = array_values(array_filter(
            $columns,
            fn (string $column): bool => Schema::hasColumn($table, $column)
        ));

        if ($existing === []) {
            return;
        }

        Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropColumn($existing));
    }
};
