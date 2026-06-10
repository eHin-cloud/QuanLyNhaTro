<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(FullDemoSeeder::class);

        $defaultTenantId = Tenant::where('email', 'contact@smartroom-caugiay.vn')->value('id')
            ?? Tenant::query()->orderBy('id')->value('id');

        $adminRole = Role::where('slug', 'admin')->first();
        $residentRole = Role::where('slug', 'resident')->first();

        // 1. Seed / Update tài khoản admin (admin / admin123)
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'tenant_id' => $defaultTenantId,
                'role_id' => $adminRole ? $adminRole->id : null,
                'name' => 'Admin Hà Nội',
                'phone' => '0987654321',
                'email' => 'admin@smartroom.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'like' => 'Admin Hà Nội'
            ]
        );

        // 2. Seed / Update tài khoản tenant (tenant / password)
        User::updateOrCreate(
            ['username' => 'tenant'],
            [
                'tenant_id' => $defaultTenantId,
                'role_id' => $residentRole ? $residentRole->id : null,
                'name' => 'Khách thuê demo',
                'phone' => '0123456789',
                'email' => 'tenant@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'like' => 'Khách thuê demo'
            ]
        );

        $this->call(ContractSeeder::class);
    }
}
