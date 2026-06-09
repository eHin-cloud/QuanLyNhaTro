<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')->updateOrInsert(
            ['slug' => 'admin'],
            [
                'name' => 'Admin he thong',
                'description' => 'Dieu hanh website va cap quyen tai khoan.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $adminRoleId = DB::table('roles')->where('slug', 'admin')->value('id');

        DB::table('users')->updateOrInsert(
            ['username' => 'admin'],
            [
                'tenant_id' => null,
                'role_id' => $adminRoleId,
                'name' => 'Admin He Thong',
                'phone' => '0900000000',
                'email' => 'admin@smartroom.local',
                'password' => Hash::make('password'),
                'like' => 'Dieu hanh nen tang',
                'role' => 'admin',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('users')->where('username', 'admin')->delete();
        DB::table('roles')->where('slug', 'admin')->delete();
    }
};
