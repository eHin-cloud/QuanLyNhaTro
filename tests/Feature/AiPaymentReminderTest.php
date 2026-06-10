<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\NotificationLog;
use App\Models\Resident;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UtilityRecord;
use App\Services\AiReminderService;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiPaymentReminderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('SQLite driver is required for isolated AI reminder tests.');
        }

        parent::setUp();
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
    }

    public function test_ai_reminder_content_is_saved_in_notification_log(): void
    {
        [$tenant, $record] = $this->createUnpaidUtilityRecord();

        config()->set('services.ai.api_key', 'test-key');
        config()->set('services.ai.base_url', 'https://ai.example.test/v1');
        config()->set('services.ai.model', 'test-model');

        Http::fake([
            'ai.example.test/v1/chat/completions' => Http::response([
                'choices' => [[
                    'message' => [
                        'content' => json_encode([
                            'subject' => 'Nhac thanh toan phong 101',
                            'message' => 'Chao anh Nguyen Van A, hoa don phong 101 thang 2026-06 can thanh toan som. Cam on anh.',
                        ]),
                    ],
                ]],
            ]),
        ]);

        app(NotificationService::class)->sendPaymentReminders($tenant->id, '2026-06', ['sms']);

        $log = NotificationLog::first();

        $this->assertNotNull($log);
        $this->assertSame('Nhac thanh toan phong 101', $log->subject);
        $this->assertSame('Chao anh Nguyen Van A, hoa don phong 101 thang 2026-06 can thanh toan som. Cam on anh.', $log->message);
        $this->assertTrue($log->meta['ai_generated']);
        $this->assertNull($log->meta['ai_fallback_reason']);
        $this->assertSame('sent', $record->fresh()->status);
    }

    public function test_ai_reminder_falls_back_when_ai_is_not_configured(): void
    {
        [$tenant] = $this->createUnpaidUtilityRecord();

        config()->set('services.ai.api_key', null);

        app(NotificationService::class)->sendPaymentReminders($tenant->id, '2026-06', ['sms']);

        $log = NotificationLog::first();

        $this->assertNotNull($log);
        $this->assertStringContainsString('Phong 101 co hoa don thang 2026-06 chua thanh toan', $log->message);
        $this->assertFalse($log->meta['ai_generated']);
        $this->assertSame('ai_not_configured', $log->meta['ai_fallback_reason']);
    }

    public function test_ai_reminder_service_falls_back_when_ai_response_is_invalid(): void
    {
        [, $record, $room, $resident] = $this->createUnpaidUtilityRecord();

        config()->set('services.ai.api_key', 'test-key');
        config()->set('services.ai.base_url', 'https://ai.example.test/v1');
        config()->set('services.ai.model', 'test-model');

        Http::fake([
            'ai.example.test/v1/chat/completions' => Http::response([
                'choices' => [[
                    'message' => ['content' => '{}'],
                ]],
            ]),
        ]);

        $content = app(AiReminderService::class)
            ->generatePaymentReminder($record, $room, $resident, 2230000, 'zalo');

        $this->assertFalse($content['used_ai']);
        $this->assertSame('ai_failed', $content['fallback_reason']);
        $this->assertStringContainsString('Phong 101 co hoa don thang 2026-06 chua thanh toan', $content['message']);
    }

    private function createUnpaidUtilityRecord(): array
    {
        $tenant = Tenant::create([
            'name' => 'SmartRoom Test',
            'email' => 'tenant-ai@example.com',
        ]);

        $role = Role::firstOrCreate([
            'slug' => 'landlord',
        ], [
            'name' => 'Chu tro',
        ]);

        User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'name' => 'Admin',
            'username' => 'admin-ai-test',
            'email' => 'admin-ai@example.com',
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
            'status' => 'occupied',
            'price' => 2000000,
        ]);

        $resident = Resident::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'name' => 'Nguyen Van A',
            'phone' => '0900000001',
            'email' => 'resident-ai@example.com',
            'start_date' => '2026-06-01',
            'status' => 'active',
        ]);

        $record = UtilityRecord::create([
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

        return [$tenant, $record, $room, $resident];
    }
}
