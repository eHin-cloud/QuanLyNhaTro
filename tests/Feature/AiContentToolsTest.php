<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Resident;
use App\Models\Review;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiContentToolsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('SQLite driver is required for isolated AI content tool tests.');
        }

        parent::setUp();
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
    }

    public function test_resident_can_analyze_maintenance_ticket_with_fallback(): void
    {
        [, , , $residentUser] = $this->createTenantRoomAndUsers();

        config()->set('services.ai.api_key', null);

        $response = $this->actingAs($residentUser)->postJson(route('smartroom.resident.tickets.analyze'), [
            'description' => 'May lanh keu to va khong lanh',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('analysis.used_ai', false)
            ->assertJsonPath('analysis.fallback_reason', 'ai_not_configured')
            ->assertJsonPath('analysis.category', 'maintenance');
    }

    public function test_admin_can_generate_room_description_with_ai(): void
    {
        [$admin] = $this->createTenantRoomAndUsers();

        $this->fakeAiResponse(['description' => 'Phong 101 dien tich 25m2, co dieu hoa, phu hop cho sinh vien.']);

        $response = $this->actingAs($admin)->postJson(route('admin.rooms.description.ai'), [
            'room_number' => '101',
            'floor' => 1,
            'room_type' => 'normal',
            'price' => 2500000,
            'area' => 25,
            'status' => 'empty',
            'amenities' => ['dieu hoa'],
        ]);

        $response->assertOk()
            ->assertJsonPath('description.used_ai', true)
            ->assertJsonPath('description.description', 'Phong 101 dien tich 25m2, co dieu hoa, phu hop cho sinh vien.');
    }

    public function test_admin_can_generate_contract_terms_for_own_tenant(): void
    {
        [$admin, $room, $resident] = $this->createTenantRoomAndUsers();

        $this->fakeAiResponse(['terms' => 'Dieu khoan hop dong AI cho phong 101.']);

        $response = $this->actingAs($admin)->postJson(route('smartroom.admin.ai.contract_terms'), [
            'room_id' => $room->id,
            'resident_id' => $resident->id,
            'start_date' => '2026-06-01',
            'end_date' => '2027-06-01',
            'deposit' => 2500000,
        ]);

        $response->assertOk()
            ->assertJsonPath('terms.used_ai', true)
            ->assertJsonPath('terms.terms', 'Dieu khoan hop dong AI cho phong 101.');
    }

    public function test_public_review_summary_uses_reviews(): void
    {
        [, $room] = $this->createTenantRoomAndUsers();

        Review::create([
            'room_id' => $room->id,
            'author_name' => 'Khach A',
            'rating' => 5,
            'comment' => 'Phong sach va yen tinh.',
        ]);

        config()->set('services.ai.api_key', null);

        $response = $this->getJson('/api/renty/rooms/' . $room->id . '/reviews/summary');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('summary.used_ai', false)
            ->assertJsonPath('summary.fallback_reason', 'ai_not_configured')
            ->assertSee('1 review', false);
    }

    private function fakeAiResponse(array $payload): void
    {
        config()->set('services.ai.api_key', 'test-key');
        config()->set('services.ai.base_url', 'https://ai.example.test/v1');
        config()->set('services.ai.model', 'test-model');

        Http::fake([
            'ai.example.test/v1/chat/completions' => Http::response([
                'choices' => [[
                    'message' => [
                        'content' => json_encode($payload),
                    ],
                ]],
            ]),
        ]);
    }

    private function createTenantRoomAndUsers(): array
    {
        $tenant = Tenant::create([
            'name' => 'SmartRoom Test',
            'email' => 'tenant-ai-tools@example.com',
        ]);

        $landlordRole = Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chu tro']);
        $residentRole = Role::firstOrCreate(['slug' => 'resident'], ['name' => 'Cu dan']);

        $admin = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $landlordRole->id,
            'name' => 'Admin',
            'username' => 'admin-ai-tools',
            'email' => 'admin-ai-tools@example.com',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $residentUser = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $residentRole->id,
            'name' => 'Nguyen Van A',
            'username' => 'resident-ai-tools',
            'email' => 'resident-ai-tools@example.com',
            'phone' => '0900000001',
            'role' => 'user',
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
            'status' => 'occupied',
            'price' => 2500000,
            'area' => 25,
            'amenities' => ['dieu hoa'],
        ]);

        $resident = Resident::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'user_id' => $residentUser->id,
            'name' => 'Nguyen Van A',
            'phone' => '0900000001',
            'email' => 'resident-ai-tools@example.com',
            'start_date' => '2026-06-01',
            'status' => 'active',
        ]);

        return [$admin, $room, $resident, $residentUser];
    }
}
