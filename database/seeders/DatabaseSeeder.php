<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(FullDemoSeeder::class);

        $defaultTenantId = \App\Models\Tenant::where('email', 'contact@smartroom-caugiay.vn')->value('id')
            ?? \App\Models\Tenant::query()->orderBy('id')->value('id');

        if ($defaultTenantId) {
            \App\Models\User::where('username', 'admin')->update(['tenant_id' => $defaultTenantId]);
        }

        $this->call(ContractSeeder::class);
    }
}
