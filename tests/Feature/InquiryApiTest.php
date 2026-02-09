<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InquiryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_check_returns_success(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'API is running',
            ]);
    }

    public function test_can_create_inquiry(): void
    {
        $inquiryData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'category' => 'Trading',
            'subject' => 'Test Inquiry',
            'message' => 'This is a test inquiry message with sufficient length.',
            'priority' => 'medium',
        ];

        $response = $this->postJson('/api/inquiries', $inquiryData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Inquiry submitted successfully',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'category',
                    'subject',
                    'message',
                    'status',
                    'priority',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('inquiries', [
            'email' => 'john@example.com',
            'subject' => 'Test Inquiry',
        ]);
    }

    public function test_create_inquiry_validation_fails_with_invalid_data(): void
    {
        $invalidData = [
            'name' => '',
            'email' => 'invalid-email',
            'category' => 'InvalidCategory',
            'subject' => '',
            'message' => 'Short',
        ];

        $response = $this->postJson('/api/inquiries', $invalidData);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed',
            ])
            ->assertJsonValidationErrors(['name', 'email', 'category', 'subject', 'message']);
    }

    public function test_can_get_all_inquiries(): void
    {
        Inquiry::factory()->count(5)->create();

        $response = $this->getJson('/api/inquiries');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Inquiries retrieved successfully',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'data',
                    'meta',
                    'links',
                ],
            ]);
    }

    public function test_can_get_single_inquiry(): void
    {
        $inquiry = Inquiry::factory()->create();

        $response = $this->getJson("/api/inquiries/{$inquiry->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Inquiry retrieved successfully',
                'data' => [
                    'id' => $inquiry->id,
                    'email' => $inquiry->email,
                ],
            ]);
    }

    public function test_get_single_inquiry_returns_404_for_nonexistent_inquiry(): void
    {
        $response = $this->getJson('/api/inquiries/999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Inquiry not found',
            ]);
    }

    public function test_can_update_inquiry(): void
    {
        $inquiry = Inquiry::factory()->create([
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        $updateData = [
            'status' => 'in_progress',
            'priority' => 'high',
            'resolution_notes' => 'Working on this issue',
        ];

        $response = $this->putJson("/api/inquiries/{$inquiry->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Inquiry updated successfully',
                'data' => [
                    'status' => 'in_progress',
                    'priority' => 'high',
                ],
            ]);

        $this->assertDatabaseHas('inquiries', [
            'id' => $inquiry->id,
            'status' => 'in_progress',
            'priority' => 'high',
        ]);
    }

    public function test_can_delete_inquiry(): void
    {
        $inquiry = Inquiry::factory()->create();

        $response = $this->deleteJson("/api/inquiries/{$inquiry->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Inquiry deleted successfully',
            ]);

        $this->assertSoftDeleted('inquiries', [
            'id' => $inquiry->id,
        ]);
    }

    public function test_can_filter_inquiries_by_category(): void
    {
        Inquiry::factory()->create(['category' => 'Trading']);
        Inquiry::factory()->create(['category' => 'Market Data']);

        $response = $this->getJson('/api/inquiries?category=Trading');

        $response->assertStatus(200);
        $data = $response->json('data.data');
        
        foreach ($data as $inquiry) {
            $this->assertEquals('Trading', $inquiry['category']);
        }
    }

    public function test_can_filter_inquiries_by_status(): void
    {
        Inquiry::factory()->create(['status' => 'pending']);
        Inquiry::factory()->create(['status' => 'resolved']);

        $response = $this->getJson('/api/inquiries?status=pending');

        $response->assertStatus(200);
        $data = $response->json('data.data');
        
        foreach ($data as $inquiry) {
            $this->assertEquals('pending', $inquiry['status']);
        }
    }

    public function test_can_get_statistics(): void
    {
        Inquiry::factory()->count(3)->create(['status' => 'pending']);
        Inquiry::factory()->count(2)->create(['status' => 'resolved']);
        Inquiry::factory()->count(1)->create(['category' => 'Trading']);

        $response = $this->getJson('/api/inquiries/statistics');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Statistics retrieved successfully',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'total',
                    'by_status',
                    'by_category',
                    'by_priority',
                ],
            ]);
    }

    public function test_inquiry_status_changes_to_resolved_with_timestamp(): void
    {
        $inquiry = Inquiry::factory()->create(['status' => 'pending']);

        $updateData = [
            'status' => 'resolved',
            'resolution_notes' => 'Issue resolved successfully',
        ];

        $response = $this->putJson("/api/inquiries/{$inquiry->id}", $updateData);

        $response->assertStatus(200);

        $inquiry->refresh();
        $this->assertEquals('resolved', $inquiry->status);
        $this->assertNotNull($inquiry->resolved_at);
        $this->assertEquals('Issue resolved successfully', $inquiry->resolution_notes);
    }
}
