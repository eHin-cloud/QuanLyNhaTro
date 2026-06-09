<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Resident;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomOccupancyLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('SQLite driver is required for isolated room occupancy tests.');
        }

        parent::setUp();
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
    }

    public function test_room_accepts_at_most_five_occupants(): void
    {
        $room = $this->createRoom();
        $resident = Resident::create([
            'tenant_id' => $room->tenant_id,
            'room_id' => $room->id,
            'name' => 'Resident',
            'phone' => '0900000000',
            'email' => 'resident@example.com',
            'start_date' => '2026-06-09',
            'status' => 'active',
        ]);

        foreach (range(1, 4) as $index) {
            $resident->relatives()->create([
                'name' => 'Relative ' . $index,
                'relationship' => 'Family',
                'temporary_residence_status' => 'registered',
                'version' => 1,
            ]);
        }

        $this->assertSame(Room::MAX_OCCUPANTS, $room->occupancyCount());
        $this->assertFalse($room->canAcceptOccupants(1));
    }

    public function test_room_reports_available_slots_below_limit(): void
    {
        $room = $this->createRoom();
        Resident::create([
            'tenant_id' => $room->tenant_id,
            'room_id' => $room->id,
            'name' => 'Resident',
            'phone' => '0900000000',
            'email' => 'resident@example.com',
            'start_date' => '2026-06-09',
            'status' => 'active',
        ]);

        $this->assertSame(4, $room->availableOccupancySlots());
        $this->assertTrue($room->canAcceptOccupants(4));
        $this->assertFalse($room->canAcceptOccupants(5));
    }

    private function createRoom(): Room
    {
        $tenant = Tenant::create([
            'name' => 'SmartRoom Test',
            'email' => 'tenant@example.com',
        ]);

        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Toa A',
            'address' => 'Ha Noi',
        ]);

        return Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '101',
            'floor' => 1,
            'status' => 'occupied',
            'price' => 2000000,
        ]);
    }
}
