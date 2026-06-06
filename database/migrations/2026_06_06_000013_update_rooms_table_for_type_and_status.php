<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Cập nhật enum status thêm trạng thái maintenance (đang sửa chữa)
            $table->enum('status', ['empty', 'occupied', 'overdue', 'maintenance'])->default('empty')->change();
            
            // Thêm cột room_type (loại phòng) mặc định là normal
            $table->string('room_type')->default('normal')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('room_type');
            $table->enum('status', ['empty', 'occupied', 'overdue'])->default('empty')->change();
        });
    }
};
