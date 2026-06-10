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
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiManagementAssistantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('SQLite driver is required for isolated AI management tests.');
        }

        parent::setUp();
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
    }

    public function test_dashboard_ai_insight_has_rule_based_fallback_without_api_key(): void
    {
        [$user] = $this->createTenantWithUnpaidRoom('101', 'Nguyen Van A');

        config()->set('services.ai.api_key', null);

        $response = $this->actingAs($user)
            ->postJson(route('smartroom.admin.ai.dashboard_insight'));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('insight.used_ai', false)
            ->assertJsonPath('insight.fallback_reason', 'ai_not_configured');
    }

    public function test_ai_assistant_context_only_contains_current_tenant_data(): void
    {
        [$user] = $this->createTenantWithUnpaidRoom('101', 'Nguyen Van A');
        $this->createTenantWithUnpaidRoom('999', 'Tran Tenant Khac');

        config()->set('services.ai.api_key', 'test-key');
        config()->set('services.ai.base_url', 'https://ai.example.test/v1');
        config()->set('services.ai.model', 'test-model');

        Http::fake([
            'ai.example.test/v1/chat/completions' => Http::response([
                'choices' => [[
                    'message' => [
                        'content' => json_encode([
                            'answer' => 'Phong 101 cua Nguyen Van A chua thanh toan.',
                        ]),
                    ],
                ]],
            ]),
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('smartroom.admin.ai.assistant'), [
                'question' => 'Phong nao chua dong tien thang nay?',
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('answer.used_ai', true)
            ->assertJsonPath('answer.answer', 'Phong 101 cua Nguyen Van A chua thanh toan.');

        Http::assertSent(function ($request) {
            $body = json_encode($request->data(), JSON_UNESCAPED_UNICODE);

            return str_contains($body, '101')
                && str_contains($body, 'Nguyen Van A')
                && !str_contains($body, '999')
                && !str_contains($body, 'Tran Tenant Khac');
        });
    }

    public function test_ai_assistant_fallback_answers_unpaid_question_without_api_key(): void
    {
        [$user] = $this->createTenantWithUnpaidRoom('101', 'Nguyen Van A');

        config()->set('services.ai.api_key', null);

        $response = $this->actingAs($user)
            ->postJson(route('smartroom.admin.ai.assistant'), [
                'question' => 'Phong nao chua dong tien?',
            ]);

        $response->assertOk()
            ->assertJsonPath('answer.used_ai', false)
            ->assertJsonPath('answer.fallback_reason', 'ai_not_configured')
            ->assertSee('Phong 101', false)
            ->assertSee('Nguyen Van A', false);
    }

    private function createTenantWithUnpaidRoom(string $roomNumber, string $residentName): array
    {
        $tenant = Tenant::create([
            'name' => 'Tenant ' . $roomNumber,
            'email' => 'tenant-' . $roomNumber . '@example.com',
        ]);

        $role = Role::firstOrCreate([
            'slug' => 'landlord',
        ], [
            'name' => 'Chu tro',
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'name' => 'Admin ' . $roomNumber,
            'username' => 'admin-ai-management-' . $roomNumber,
            'email' => 'admin-ai-management-' . $roomNumber . '@example.com',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Toa ' . $roomNumber,
            'address' => 'Ha Noi',
        ]);

        $room = Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => $roomNumber,
            'floor' => 1,
            'status' => 'overdue',
            'price' => 2000000,
        ]);

        Resident::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'name' => $residentName,
            'phone' => '0900000' . $roomNumber,
            'start_date' => '2026-06-01',
            'status' => 'active',
        ]);

        UtilityRecord::create([
            'tenant_id' => null,
            'room_id' => $room->id,
            'billing_month' => now()->format('Y-m'),
            'old_electricity' => 10,
            'new_electricity' => 20,
            'old_water' => 5,
            'new_water' => 8,
            'electricity_price' => 3500,
            'water_price' => 15000,
            'status' => 'sent',
        ]);

        return [$user, $tenant, $room];
    }
}
