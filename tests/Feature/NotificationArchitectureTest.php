<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotificationArchitectureTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected NotificationService $notificationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'full_name' => 'كمال الأحمدي',
            'email' => 'kamal@tabibi.test',
        ]);

        $this->notificationService = app(NotificationService::class);
    }

    public function test_user_can_retrieve_notifications_list_and_unread_count(): void
    {
        // Dispatch test notifications
        $this->notificationService->sendNotification($this->user, 'APPOINTMENT_CONFIRMED', [
            'title' => 'تم تأكيد موعدك الطبي',
            'message' => 'تم تأكيد الموعد الطبي لليوم الساعة 10:00 صباحاً',
        ]);

        $this->notificationService->sendNotification($this->user, 'NEW_MESSAGE', [
            'title' => 'رسالة جديدة من الطبيب',
            'message' => 'أهلاً بك، يرجى إرفاق نتائج الفحوصات',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/notifications');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('unread_count', 2);

        $unreadRes = $this->actingAs($this->user)
            ->getJson('/api/v1/notifications/unread-count');

        $unreadRes->assertStatus(200)
            ->assertJsonPath('unread_count', 2);
    }

    public function test_user_can_mark_notification_as_read_and_mark_all_as_read(): void
    {
        $notification = $this->notificationService->sendNotification($this->user, 'PRESCRIPTION_CREATED', [
            'title' => 'تم إصدار وصفة إلكترونية جديدة',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/v1/notifications/{$notification->id}/read");

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $this->assertNotNull($notification->fresh()->read_at);

        // Test Mark All as Read
        $this->notificationService->sendNotification($this->user, 'PAYMENT_RECEIPT', [
            'title' => 'إيصال دفع استشارة',
        ]);

        $markAllRes = $this->actingAs($this->user)
            ->postJson('/api/v1/notifications/read-all');

        $markAllRes->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $this->assertEquals(0, $this->notificationService->getUnreadCount($this->user));
    }

    public function test_user_can_register_and_deactivate_fcm_device_push_token(): void
    {
        $deviceToken = 'fcm_token_sample_123456789_test';

        $storeRes = $this->actingAs($this->user)
            ->postJson('/api/v1/devices/push-token', [
                'device_token' => $deviceToken,
                'platform' => 'android',
                'app_version' => '1.0.0',
            ]);

        $storeRes->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.device_token', $deviceToken);

        $this->assertDatabaseHas('push_tokens', [
            'user_id' => $this->user->id,
            'device_token' => $deviceToken,
            'is_active' => true,
        ]);

        // Deactivate token on logout
        $deleteRes = $this->actingAs($this->user)
            ->deleteJson('/api/v1/devices/push-token', [
                'device_token' => $deviceToken,
            ]);

        $deleteRes->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('push_tokens', [
            'user_id' => $this->user->id,
            'device_token' => $deviceToken,
            'is_active' => false,
        ]);
    }

    public function test_idempotency_prevents_duplicate_notification_dispatch(): void
    {
        $idempotencyKey = 'idempotent_event_key_'.Str::random(10);

        $firstDispatch = $this->notificationService->sendNotification(
            $this->user,
            'APPOINTMENT_REMINDER',
            ['title' => 'تذكير بموعد استشارة غداً'],
            $idempotencyKey
        );

        $this->assertNotNull($firstDispatch);

        // Duplicate dispatch attempt with same idempotency key
        $secondDispatch = $this->notificationService->sendNotification(
            $this->user,
            'APPOINTMENT_REMINDER',
            ['title' => 'تذكير بموعد استشارة غداً'],
            $idempotencyKey
        );

        $this->assertNull($secondDispatch);
        $this->assertEquals(1, Notification::where('user_id', $this->user->id)->count());
    }
}
