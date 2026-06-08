<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Equipment;
use App\Models\Role;
use App\Models\Room;
use App\Models\RoomEquipment;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UtilityRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReportManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('SQLite driver is required for isolated report feature tests.');
        }

        parent::setUp();
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
    }

    public function test_landlord_can_view_equipment_and_unpaid_utility_reports(): void
    {
        $tenant = Tenant::create([
            'name' => 'SmartRoom Test',
            'email' => 'tenant@example.com',
        ]);

        $role = Role::create([
            'name' => 'Chu tro',
            'slug' => 'landlord',
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'name' => 'Admin',
            'username' => 'admin-test',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Toa A',
            'address' => 'Ha Noi',
        ]);

        $room = Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '101',
            'floor' => 1,
            'status' => 'overdue',
            'price' => 2000000,
        ]);

        $equipment = Equipment::create([
            'tenant_id' => $tenant->id,
            'code' => 'AC01',
            'name' => 'Dieu hoa',
            'unit' => 'cai',
            'total_quantity' => 5,
            'allocated_quantity' => 2,
            'version' => 1,
        ]);

        RoomEquipment::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'equipment_id' => $equipment->id,
            'quantity' => 2,
        ]);

        UtilityRecord::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'billing_month' => '2026-06',
            'old_electricity' => 10,
            'new_electricity' => 20,
            'old_water' => 5,
            'new_water' => 8,
            'electricity_price' => 3500,
            'water_price' => 15000,
            'status' => 'sent',
        ]);

        $response = $this->actingAs($user)->get(route('admin.reports.index', [
            'billing_month' => '2026-06',
        ]));

        $response->assertOk();
        $response->assertSee('Dieu hoa');
        $response->assertSee('AC01');
        $response->assertSee('Phòng 101');
        $response->assertSee('2,230,000');
    }

    public function test_invalid_billing_month_returns_not_found(): void
    {
        $tenant = Tenant::create([
            'name' => 'SmartRoom Test',
            'email' => 'tenant-invalid@example.com',
        ]);

        $role = Role::create([
            'name' => 'Chu tro',
            'slug' => 'landlord',
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'name' => 'Admin',
            'username' => 'admin-invalid-test',
            'email' => 'admin-invalid@example.com',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user)
            ->get(route('admin.reports.index', ['billing_month' => '2026-99']))
            ->assertNotFound();
    }
}
