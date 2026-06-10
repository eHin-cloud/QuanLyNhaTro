<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $roles = [
            ['name' => 'Chu tro', 'slug' => 'landlord', 'description' => 'Toan quyen quan tri nha tro.'],
            ['name' => 'Nhan vien quan ly', 'slug' => 'manager', 'description' => 'Xu ly van hanh, phong, cu dan, hoa don va thiet bi.'],
            ['name' => 'Cu dan', 'slug' => 'resident', 'description' => 'Xem hoa don, hop dong, QR thanh toan va gui yeu cau sua chua.'],
            ['name' => 'Khach xem phong', 'slug' => 'guest', 'description' => 'Chi xem danh sach phong cong khai va gui lien he.'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['slug' => $role['slug']],
                [
                    'name' => $role['name'],
                    'description' => $role['description'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('roles')
            ->whereIn('slug', ['manager'])
            ->delete();
    }
};
