<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')->updateOrInsert(
            ['slug' => 'unverified_landlord'],
            [
                'name' => 'Chu tro chua xac minh',
                'description' => 'Chu tro moi dang ky, duoc vao dashboard va dang phong voi nhan chua xac minh.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'verification_status')) {
                $table->string('verification_status', 50)->default('unverified')->after('bank_account_name');
            }
            if (!Schema::hasColumn('tenants', 'onboarding_step')) {
                $table->unsignedTinyInteger('onboarding_step')->default(1)->after('verification_status');
            }
            if (!Schema::hasColumn('tenants', 'listing_badge')) {
                $table->string('listing_badge', 50)->default('unverified')->after('onboarding_step');
            }
            if (!Schema::hasColumn('tenants', 'boost_score')) {
                $table->integer('boost_score')->default(0)->after('listing_badge');
            }
            if (!Schema::hasColumn('tenants', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('boost_score');
            }
            if (!Schema::hasColumn('tenants', 'kyc_verified_at')) {
                $table->timestamp('kyc_verified_at')->nullable()->after('verified_at');
            }
            if (!Schema::hasColumn('tenants', 'premium_verified_at')) {
                $table->timestamp('premium_verified_at')->nullable()->after('kyc_verified_at');
            }
        });

        Schema::create('landlord_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('phone', 30);
            $table->string('property_name');
            $table->text('property_address');
            $table->string('status', 50)->default('unverified');
            $table->timestamps();
        });

        Schema::create('landlord_verification_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 50);
            $table->string('status', 50)->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('reject_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('landlord_verification_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verification_request_id')->constrained('landlord_verification_requests')->cascadeOnDelete();
            $table->string('document_type', 80);
            $table->string('file_path', 500);
            $table->string('status', 50)->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landlord_verification_documents');
        Schema::dropIfExists('landlord_verification_requests');
        Schema::dropIfExists('landlord_profiles');

        Schema::table('tenants', function (Blueprint $table) {
            foreach (['premium_verified_at', 'kyc_verified_at', 'verified_at', 'boost_score', 'listing_badge', 'onboarding_step', 'verification_status'] as $column) {
                if (Schema::hasColumn('tenants', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        DB::table('roles')->where('slug', 'unverified_landlord')->delete();
    }
};
