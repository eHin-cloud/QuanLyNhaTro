<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add fields and version column to residents table for optimistic locking
        Schema::table('residents', function (Blueprint $table) {
            $table->date('dob')->nullable()->after('name');
            $table->string('hometown')->nullable()->after('cccd');
            $table->enum('temporary_residence_status', ['none', 'registered', 'absent'])->default('none')->after('status');
            $table->integer('version')->default(1)->after('temporary_residence_status'); // Optimistic locking
        });

        // 2. Create resident_relatives table for relatives temporary residence
        Schema::create('resident_relatives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->constrained('residents')->onDelete('cascade');
            $table->string('name');
            $table->date('dob')->nullable();
            $table->string('cccd')->nullable();
            $table->string('phone')->nullable();
            $table->string('hometown')->nullable();
            $table->string('relationship')->nullable(); // Mối quan hệ với khách thuê
            $table->enum('temporary_residence_status', ['none', 'registered', 'absent'])->default('none');
            $table->date('start_date')->nullable(); // Ngày đến tạm trú
            $table->date('end_date')->nullable(); // Ngày đi / dự kiến đi
            $table->integer('version')->default(1); // Optimistic locking
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resident_relatives');

        Schema::table('residents', function (Blueprint $table) {
            $table->dropColumn(['dob', 'hometown', 'temporary_residence_status', 'version']);
        });
    }
};
