<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentyChatbotControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('SQLite driver is required for isolated Renty chatbot tests.');
        }

        parent::setUp();
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
    }

    public function test_chatbot_returns_rule_based_room_results_without_api_key(): void
    {
        $tenant = Tenant::create([
            'name' => 'Renty Test Tenant',
            'email' => 'renty-chatbot@example.test',
        ]);
        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Renty Cầu Giấy',
            'address' => 'Dịch Vọng, Cầu Giấy, Hà Nội',
            'description' => 'Gần trường đại học, khu dân cư an ninh.',
        ]);
        Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '101',
            'floor' => 1,
            'status' => 'empty',
            'room_type' => 'studio',
            'price' => 3500000,
            'area' => 24,
            'amenities' => ['ban công', 'wc', 'thú cưng'],
            'description' => 'Phòng studio có ban công.',
        ]);

        config()->set('services.ai.api_key', null);

        $response = $this->postJson(route('renty.chatbot.chat'), [
            'prompt' => 'Tìm phòng Cầu Giấy dưới 4 triệu có ban công',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('used_ai', false)
            ->assertJsonPath('fallback_reason', 'ai_not_configured')
            ->assertJsonPath('rooms.0.title', 'Renty Cầu Giấy - Phòng 101')
            ->assertJsonPath('rooms.0.cover_image', 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1200&q=80')
            ->assertJsonPath('rooms.0.area_text', '24 m²')
            ->assertJsonPath('rooms.0.balcony_txt', 'Có');

        $this->assertStringContainsString('Renty AI tìm thấy', $response->json('response'));
    }

    public function test_chatbot_answers_renting_tips_without_api_key_or_rooms(): void
    {
        config()->set('services.ai.api_key', null);

        $response = $this->postJson(route('renty.chatbot.chat'), [
            'prompt' => 'Mẹo thuê trọ an toàn',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('used_ai', false)
            ->assertJsonPath('rooms', []);

        $this->assertStringContainsString('Mẹo thuê trọ an toàn', $response->json('response'));
    }

    public function test_chatbot_gives_consulting_response_for_unknown_keyword(): void
    {
        config()->set('services.ai.api_key', null);

        $response = $this->postJson(route('renty.chatbot.chat'), [
            'prompt' => 'aaaa',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('rooms', []);

        $this->assertStringContainsString('"aaaa"', $response->json('response'));
        $this->assertStringContainsString('khu vực + ngân sách + tiện ích', $response->json('response'));
    }

    public function test_chatbot_does_not_match_all_rooms_for_single_letter_query(): void
    {
        $tenant = Tenant::create([
            'name' => 'Renty Test Tenant B',
            'email' => 'renty-chatbot-b@example.test',
        ]);
        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Demo Cầu Giấy B',
            'address' => 'Số 88 ngõ demo Cầu Giấy',
            'description' => 'Tòa nhà demo có chữ B trong tên.',
        ]);
        Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '501',
            'floor' => 5,
            'status' => 'empty',
            'room_type' => 'studio',
            'price' => 4850000,
            'area' => 36,
            'amenities' => ['ban công', 'wc'],
            'description' => 'Phòng demo.',
        ]);

        config()->set('services.ai.api_key', null);

        $response = $this->postJson(route('renty.chatbot.chat'), [
            'prompt' => 'b',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('rooms', []);

        $this->assertStringContainsString('"b"', $response->json('response'));
        $this->assertStringNotContainsString('Demo Cầu Giấy B - Phòng 501', $response->json('response'));
    }
}
