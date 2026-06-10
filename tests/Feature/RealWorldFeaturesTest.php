<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Contract;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Resident;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RealWorldFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('SQLite driver is required for isolated real-world features tests.');
        }

        parent::setUp();
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
    }

    private function createLandlordUser(Tenant $tenant)
    {
        $role = Role::firstOrCreate(['slug' => 'landlord'], [
            'name' => 'Chu tro',
        ]);

        return User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'name' => 'Landlord Test',
            'username' => 'landlord-test',
            'email' => 'landlord@example.com',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);
    }

    /**
     * Test AI OCR Meter Reading fallback mechanism
     */
    public function test_ai_ocr_meter_reading_endpoint_fallback(): void
    {
        $tenant = Tenant::create([
            'name' => 'SmartRoom Test',
            'email' => 'tenant@example.com',
        ]);
        $user = $this->createLandlordUser($tenant);

        $response = $this->actingAs($user)->postJson(route('smartroom.admin.ai.ocr_meter'), [
            'image' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            'type' => 'electricity'
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'result' => [
                'value',
                'confidence'
            ]
        ]);
    }

    /**
     * Test Secure Contract Signing Flow via OTP
     */
    public function test_contract_otp_secure_signature_flow(): void
    {
        $tenant = Tenant::create([
            'name' => 'SmartRoom Test',
            'email' => 'tenant@example.com',
        ]);
        $user = $this->createLandlordUser($tenant);

        $building = \App\Models\Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Toà Nhà Test',
            'address' => 'Hà Nội',
        ]);

        $room = Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '101',
            'floor' => 1,
            'status' => 'overdue',
            'price' => 2000000,
        ]);

        $resident = Resident::create([
            'tenant_id' => $tenant->id,
            'name' => 'Nguoi thue A',
            'email' => 'tenant-a@example.com',
            'phone' => '0987654321',
            'status' => 'active',
            'start_date' => '2026-01-01',
        ]);

        $contract = Contract::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'resident_id' => $resident->id,
            'contract_code' => 'HD-2026-001',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'deposit' => 5000000,
            'terms' => 'Điều khoản mẫu của hợp đồng.',
            'status' => 'active',
        ]);

        // 1. Gửi OTP
        $response = $this->actingAs($user)->postJson("/smartroom/contract/{$contract->id}/send-otp");
        $response->assertOk();
        $response->assertJson(['success' => true]);

        $contract->refresh();
        $this->assertNotNull($contract->otp_code);
        $this->assertNotNull($contract->otp_expires_at);

        // 2. Ký hợp đồng với OTP sai
        $signResponse = $this->actingAs($user)->post("/smartroom/contract/{$contract->id}/sign", [
            'signature' => 'data:image/png;base64,mocksignaturedata',
            'otp_code' => '999999'
        ]);
        $signResponse->assertStatus(302);
        $signResponse->assertSessionHas('error', 'Mã OTP nhập vào không chính xác!');

        // 3. Ký hợp đồng với OTP đúng
        $signResponseCorrect = $this->actingAs($user)->post("/smartroom/contract/{$contract->id}/sign", [
            'signature' => 'data:image/png;base64,mocksignaturedata',
            'otp_code' => $contract->otp_code
        ]);
        $signResponseCorrect->assertStatus(302);
        $signResponseCorrect->assertRedirect(route('smartroom.contract.sign_view', $contract->id));
        $signResponseCorrect->assertSessionHas('success', 'Ký hợp đồng số bằng OTP thành công và có hiệu lực pháp lý!');

        $contract->refresh();
        $this->assertTrue((bool) $contract->is_signed);
        $this->assertNotNull($contract->signed_at);
        $this->assertEquals('127.0.0.1', $contract->signer_ip);
    }

    /**
     * Test Payment Webhook automatic reconciliation & manual ledger entry
     */
    public function test_payment_webhook_auto_reconciliation(): void
    {
        $tenant = Tenant::create([
            'name' => 'SmartRoom Test',
            'email' => 'tenant@example.com',
            'payment_gateway_config' => [
                'checksum_key' => 'testchecksum',
            ]
        ]);

        $building = \App\Models\Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Toà Nhà Test',
            'address' => 'Hà Nội',
        ]);

        $room = Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '101',
            'floor' => 1,
            'status' => 'overdue',
            'price' => 2000000,
        ]);

        $bill = Bill::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'billing_month' => '2026-06',
            'room_price' => 2000000,
            'total_amount' => 2500000,
            'status' => 'pending',
        ]);

        // Giả lập webhook của PayOS (thông tin đúng)
        $payload = [
            'code' => '00',
            'desc' => 'Success',
            'data' => [
                'description' => "SRM{$bill->id}",
                'amount' => 2500000,
            ],
            'signature' => hash_hmac('sha256', 'amount=2500000&description=SRM' . $bill->id, 'testchecksum')
        ];

        $response = $this->postJson('/api/webhooks/payments', $payload);
        $response->assertOk();
        $response->assertJson(['success' => true]);

        $bill->refresh();
        $this->assertEquals('paid', $bill->status);
        $this->assertNotNull($bill->payment_date);

        // Kiểm tra xem giao dịch đã được tự động ghi nhận vào Sổ Quỹ Thu Chi
        $transaction = Transaction::where('tenant_id', $tenant->id)->first();
        $this->assertNotNull($transaction);
        $this->assertEquals('income', $transaction->type);
        $this->assertEquals(2500000, $transaction->amount);
        $this->assertEquals('rental', $transaction->category);
    }

    /**
     * Test Ghi nhận thu chi thủ công vào Sổ Quỹ
     */
    public function test_financial_ledger_manual_transaction_logging(): void
    {
        $tenant = Tenant::create([
            'name' => 'SmartRoom Test',
            'email' => 'tenant@example.com',
        ]);
        $user = $this->createLandlordUser($tenant);

        // Ghi nhận chi phí sửa thiết bị
        $response = $this->actingAs($user)->post(route('admin.reports.transaction.store'), [
            'type' => 'expense',
            'amount' => 450000,
            'category' => 'Sửa thiết bị',
            'description' => 'Sửa vòi hoa sen phòng 101',
            'transaction_date' => '2026-06-10',
        ]);

        $response->assertRedirect(route('admin.reports.index'));
        
        $transaction = Transaction::where('tenant_id', $tenant->id)->first();
        $this->assertNotNull($transaction);
        $this->assertEquals('expense', $transaction->type);
        $this->assertEquals(450000, $transaction->amount);
        $this->assertEquals('Sửa thiết bị', $transaction->category);
        $this->assertEquals('Sửa vòi hoa sen phòng 101', $transaction->description);
    }
}
