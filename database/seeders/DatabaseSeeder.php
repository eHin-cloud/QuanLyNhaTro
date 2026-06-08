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

        User::updateOrCreate([
            'username' => 'admin',
        ], [
            'name' => 'Admin SmartRoom',
            'phone' => '0987654321',
            'email' => 'admin@smartroom.com',
            'password' => \Hash::make('admin123'),
            'role' => 'admin',
            'like' => 'Chủ chung cư mini'
        ]);

        User::updateOrCreate([
            'username' => 'tenant',
        ], [
            'name' => 'Test Tenant',
            'phone' => '0123456789',
            'email' => 'tenant@example.com',
            'password' => \Hash::make('password'),
            'role' => 'user',
            'like' => 'Khách thuê phòng'
        ]);
        $this->call(SmartRoomSeeder::class);
        $this->call(RoomSeeder::class);
        $this->call(EquipmentSeeder::class);
        $this->call(SmartAlertSeeder::class);
        $this->call(AdminActivityLogSeeder::class);
    }
}
