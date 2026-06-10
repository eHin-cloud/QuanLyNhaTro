<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Review;
use App\Models\RoomReport;
use App\Models\ContactRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RentyFormSubmissionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Room $room;
    private Tenant $tenant;

    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('SQLite driver is required for isolated tests.');
        }

        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'Renty Test Tenant',
            'email' => 'renty-tenant@example.com',
        ]);

        $role = Role::firstOrCreate([
            'slug' => 'landlord',
        ], [
            'name' => 'Chu tro',
        ]);

        $this->user = User::create([
            'tenant_id' => $this->tenant->id,
            'role_id' => $role->id,
            'name' => 'User Renty',
            'username' => 'renty-user',
            'email' => 'user-renty@example.com',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $building = Building::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Building A',
            'address' => 'Cau Giay, Ha Noi',
        ]);

        $this->room = Room::create([
            'tenant_id' => $this->tenant->id,
            'building_id' => $building->id,
            'room_number' => '101',
            'floor' => 1,
            'status' => 'empty',
            'price' => 3000000,
        ]);
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
    }

    public function test_guest_can_submit_contact_request(): void
    {
        $response = $this->from('/renty')->post(route('renty.contact_request.store'), [
            'room_id' => $this->room->id,
            'name' => 'Guest User',
            'phone' => '0987654321',
            'message' => 'Toi muon xem phong.',
        ]);

        $response->assertRedirect('/renty');
        $this->assertDatabaseHas('contact_requests', [
            'room_id' => $this->room->id,
            'name' => 'Guest User',
            'phone' => '0987654321',
            'message' => 'Toi muon xem phong.',
            'status' => 'pending',
        ]);
    }

    public function test_auth_user_can_submit_contact_request(): void
    {
        $response = $this->actingAs($this->user)
            ->from('/renty')
            ->post(route('renty.contact_request.store'), [
                'room_id' => $this->room->id,
                'name' => $this->user->name,
                'phone' => '0987654321',
                'message' => 'Toi la nguoi dung da dang nhap.',
            ]);

        $response->assertRedirect('/renty');
        $this->assertDatabaseHas('contact_requests', [
            'room_id' => $this->room->id,
            'name' => $this->user->name,
            'phone' => '0987654321',
            'message' => 'Toi la nguoi dung da dang nhap.',
        ]);
    }

    public function test_submit_review(): void
    {
        $response = $this->from('/renty/room/' . $this->room->id)
            ->post(route('renty.room.review.store', $this->room->id), [
                'author_name' => 'Reviewer Name',
                'rating' => 5,
                'comment' => 'Phong rat dep va sach se.',
            ]);

        $response->assertRedirect('/renty/room/' . $this->room->id);
        $this->assertDatabaseHas('reviews', [
            'room_id' => $this->room->id,
            'author_name' => 'Reviewer Name',
            'rating' => 5,
            'comment' => 'Phong rat dep va sach se.',
        ]);
    }

    public function test_submit_report(): void
    {
        $response = $this->from('/renty/room/' . $this->room->id)
            ->post(route('renty.room.report.store', $this->room->id), [
                'reporter_name' => 'Reporter Name',
                'reporter_phone' => '0123456789',
                'reason' => 'fake_images',
                'description' => 'Anh khong dung thuc te nhu mo ta.',
            ]);

        $response->assertRedirect('/renty/room/' . $this->room->id);
        $this->assertDatabaseHas('room_reports', [
            'room_id' => $this->room->id,
            'reporter_name' => 'Reporter Name',
            'reporter_phone' => '0123456789',
            'reason' => 'fake_images',
            'description' => 'Anh khong dung thuc te nhu mo ta.',
            'status' => 'pending',
        ]);
    }
}
