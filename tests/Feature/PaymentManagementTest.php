<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Resident;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UtilityRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PaymentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('SQLite driver is required for isolated payment feature tests.');
        }

        parent::setUp();
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
    }

    public function test_admin_can_view_payment_statuses_and_draft_rooms(): void
    {
        [$user, $room] = $this->createAdminRoom();

        UtilityRecord::create([
            'tenant_id' => $user->tenant_id,
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

        $draftRoom = Room::create([
            'tenant_id' => $user->tenant_id,
            'building_id' => $room->building_id,
            'room_number' => '102',
            'floor' => 1,
            'status' => 'occupied',
            'price' => 1800000,
        ]);

        Resident::create([
            'tenant_id' => $user->tenant_id,
            'room_id' => $draftRoom->id,
            'name' => 'Nguyen Van B',
            'phone' => '0900000002',
            'start_date' => '2026-06-01',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('admin.payments.index', [
            'billing_month' => '2026-06',
        ]));

        $response->assertOk();
        $response->assertSee('Đã gửi');
        $response->assertSee('Chưa gửi');
        $response->assertSee('Phòng 101');
        $response->assertSee('Phòng 102');
        $response->assertSee('2,230,000');
    }

    public function test_admin_can_record_payment_date_and_method(): void
    {
        [$user, $room] = $this->createAdminRoom();

        $record = UtilityRecord::create([
            'tenant_id' => $user->tenant_id,
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

        $this->actingAs($user)->post(route('admin.payments.update', $record), [
            'status' => 'paid',
            'payment_date' => '2026-06-07',
            'payment_method' => 'bank_transfer',
        ])->assertRedirect();

        $this->assertDatabaseHas('utility_records', [
            'id' => $record->id,
            'status' => 'paid',
            'payment_method' => 'bank_transfer',
        ]);

        $this->assertSame('occupied', $room->fresh()->status);
    }

    public function test_admin_can_export_revenue_by_quarter(): void
    {
        [$user, $room] = $this->createAdminRoom();

        foreach (['2026-04', '2026-05'] as $month) {
            UtilityRecord::create([
                'tenant_id' => $user->tenant_id,
                'room_id' => $room->id,
                'billing_month' => $month,
                'old_electricity' => 10,
                'new_electricity' => 20,
                'old_water' => 5,
                'new_water' => 8,
                'electricity_price' => 3500,
                'water_price' => 15000,
                'status' => 'paid',
                'payment_date' => '2026-06-07',
                'payment_method' => 'cash',
            ]);
        }

        $response = $this->actingAs($user)->get(route('admin.payments.export', [
            'from_month' => '2026-04',
            'to_month' => '2026-06',
            'report_period' => 'quarter',
        ]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Quý 2/2026', $response->streamedContent());
        $this->assertStringContainsString('4460000', $response->streamedContent());
    }

    private function createAdminRoom(): array
    {
        $tenant = Tenant::create([
            'name' => 'SmartRoom Test',
            'email' => 'tenant-payment@example.com',
        ]);

        $role = Role::create([
            'name' => 'Chu tro',
            'slug' => 'landlord',
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'name' => 'Admin',
            'username' => 'admin-payment-test',
            'email' => 'admin-payment@example.com',
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

        Resident::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'name' => 'Nguyen Van A',
            'phone' => '0900000001',
            'start_date' => '2026-06-01',
            'status' => 'active',
        ]);

        return [$user, $room];
    }
}
