<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function user_can_fetch_tasks_with_pagination_and_filters()
    {
        $user = User::factory()->create();
        Task::factory()->count(10)->for($user)->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/tasks?per_page=5');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
            ]);

        $this->assertCount(5, $response->json('data'));
    }

    #[Test]
    public function user_can_create_task()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'title' => 'New Task',
            'description' => 'Task description',
            'status' => 'pending',
        ];

        $response = $this->postJson('/api/v1/tasks', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'New Task']);

        $this->assertDatabaseHas('tasks', [
            'title' => 'New Task',
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function user_can_update_their_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        Sanctum::actingAs($user);

        $payload = ['title' => 'Updated Task'];

        $response = $this->putJson("/api/v1/tasks/{$task->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Updated Task']);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Updated Task']);
    }

    #[Test]
    public function user_can_delete_their_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/v1/tasks/{$task->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    #[Test]
    public function user_cannot_update_others_task()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->for($otherUser)->create();

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/v1/tasks/{$task->id}", ['title' => 'Hack']);

        $response->assertStatus(403); // Forbidden
    }
}
