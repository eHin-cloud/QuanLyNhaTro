<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Admin SmartRoom',
            'email' => 'admin@smartroom.com',
            'password' => \Hash::make('admin123'),
            'role' => 'admin',
            'like' => 'Chủ chung cư mini'
        ]);

        User::create([
            'name' => 'Test Tenant',
            'email' => 'tenant@example.com',
            'password' => \Hash::make('password'),
            'role' => 'user',
            'like' => 'Khách thuê phòng'
        ]);

        $this->call(SmartRoomSeeder::class);
    }
}
