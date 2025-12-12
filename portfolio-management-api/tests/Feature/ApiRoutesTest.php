<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Experience;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user
        $this->admin = Admin::factory()->create([
            'username' => 'testadmin',
            'password' => bcrypt('password123'),
        ]);

        // Create a token for authentication
        $this->token = $this->admin->createToken('test-token')->plainTextToken;
    }

    // ============ AUTH ROUTES ============

    public function test_login_with_valid_credentials()
    {
        $response = $this->postJson('/api/auth/login', [
            'username' => 'testadmin',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'token_type', 'admin']);
    }

    public function test_login_with_invalid_credentials()
    {
        $response = $this->postJson('/api/auth/login', [
            'username' => 'testadmin',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422);
    }

    public function test_logout()
    {
        $response = $this->withToken($this->token)
            ->postJson('/api/auth/logout');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Logged out successfully.']);
    }

    // ============ PUBLIC ROUTES ============

    public function test_get_public_portfolio()
    {
        $response = $this->getJson('/api/public/portfolio');

        $response->assertStatus(200);
    }

    // ============ PROFILE ROUTES ============

    public function test_update_profile()
    {
        $response = $this->withToken($this->token)
            ->postJson('/api/profile/update', [
                'name' => 'Updated Name',
                'bio' => 'Updated bio',
                'email' => 'updated@example.com',
            ]);

        $response->assertStatus(200);
    }

    public function test_update_profile_without_auth()
    {
        $response = $this->postJson('/api/profile/update', [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(401);
    }

    // ============ PROJECT ROUTES ============

    public function test_get_all_projects()
    {
        Project::factory(3)->create(['admin_id' => $this->admin->id]);

        $response = $this->withToken($this->token)
            ->getJson('/api/projects');

        $response->assertStatus(200);
        $response->assertJsonIsArray();
    }

    public function test_create_project()
    {
        $response = $this->withToken($this->token)
            ->postJson('/api/projects/create', [
                'title' => 'Test Project',
                'description' => 'Test Description',
                'category' => 'web',
                'status' => 'published',
            ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['id', 'title', 'description']);
    }

    public function test_get_single_project()
    {
        $project = Project::factory()->create(['admin_id' => $this->admin->id]);

        $response = $this->withToken($this->token)
            ->getJson("/api/projects/{$project->id}");

        $response->assertStatus(200);
        $response->assertJson(['id' => $project->id]);
    }

    public function test_update_project()
    {
        $project = Project::factory()->create(['admin_id' => $this->admin->id]);

        $response = $this->withToken($this->token)
            ->putJson("/api/projects/update/{$project->id}", [
                'title' => 'Updated Title',
                'description' => 'Updated Description',
            ]);

        $response->assertStatus(200);
    }

    public function test_delete_project()
    {
        $project = Project::factory()->create(['admin_id' => $this->admin->id]);

        $response = $this->withToken($this->token)
            ->deleteJson("/api/projects/delete/{$project->id}");

        $response->assertStatus(204);
    }

    public function test_project_search_filter()
    {
        Project::factory()->create([
            'admin_id' => $this->admin->id,
            'title' => 'Laravel Project',
            'status' => 'published',
        ]);

        Project::factory()->create([
            'admin_id' => $this->admin->id,
            'title' => 'Vue Project',
            'status' => 'draft',
        ]);

        $response = $this->withToken($this->token)
            ->getJson('/api/projects?search=Laravel');

        $response->assertStatus(200);
    }

    // ============ SKILL ROUTES ============

    public function test_get_all_skills()
    {
        Skill::factory(5)->create(['admin_id' => $this->admin->id]);

        $response = $this->withToken($this->token)
            ->getJson('/api/skills');

        $response->assertStatus(200);
        $response->assertJsonIsArray();
    }

    public function test_create_skill()
    {
        $response = $this->withToken($this->token)
            ->postJson('/api/skills', [
                'name' => 'PHP',
                'proficiency' => 90,
            ]);

        $response->assertStatus(201);
    }

    public function test_update_skill()
    {
        $skill = Skill::factory()->create(['admin_id' => $this->admin->id]);

        $response = $this->withToken($this->token)
            ->putJson("/api/skills/{$skill->id}", [
                'name' => 'Updated Skill',
                'proficiency' => 75,
            ]);

        $response->assertStatus(200);
    }

    public function test_delete_skill()
    {
        $skill = Skill::factory()->create(['admin_id' => $this->admin->id]);

        $response = $this->withToken($this->token)
            ->deleteJson("/api/skills/{$skill->id}");

        $response->assertStatus(204);
    }

    // ============ EXPERIENCE ROUTES ============

    public function test_get_all_experiences()
    {
        Experience::factory(3)->create(['admin_id' => $this->admin->id]);

        $response = $this->withToken($this->token)
            ->getJson('/api/experiences');

        $response->assertStatus(200);
        $response->assertJsonIsArray();
    }

    public function test_create_experience()
    {
        $response = $this->withToken($this->token)
            ->postJson('/api/experiences', [
                'company' => 'Tech Company',
                'role' => 'Developer',
                'start_date' => '2020-01-01',
                'end_date' => '2023-01-01',
            ]);

        $response->assertStatus(201);
    }

    public function test_update_experience()
    {
        $experience = Experience::factory()->create(['admin_id' => $this->admin->id]);

        $response = $this->withToken($this->token)
            ->putJson("/api/experiences/{$experience->id}", [
                'company' => 'Updated Company',
                'role' => 'Senior Developer',
                'start_date' => $experience->start_date->format('Y-m-d'),
                'end_date' => $experience->end_date?->format('Y-m-d'),
            ]);

        $response->assertStatus(200);
    }

    public function test_delete_experience()
    {
        $experience = Experience::factory()->create(['admin_id' => $this->admin->id]);

        $response = $this->withToken($this->token)
            ->deleteJson("/api/experiences/{$experience->id}");

        $response->assertStatus(204);
    }

    // ============ AUTHENTICATION TESTS ============

    public function test_protected_routes_require_authentication()
    {
        $response = $this->getJson('/api/projects');
        $response->assertStatus(401);

        $response = $this->getJson('/api/skills');
        $response->assertStatus(401);

        $response = $this->getJson('/api/experiences');
        $response->assertStatus(401);
    }
}
