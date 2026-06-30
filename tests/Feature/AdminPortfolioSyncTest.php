<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Cv;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Skill;
use App\Models\SocialLink;
use App\Models\Stat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPortfolioSyncTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->admin()->create();
    }

    // ─── PROFILE SYNC TESTS ───────────────────────────────────────────────────

    public function test_profile_update_reflects_in_database(): void
    {
        $profile = Profile::getSingleton();

        $response = $this->actingAs($this->user)->patch('/admin/profile', [
            'name' => 'Updated Name',
            'tagline' => 'Updated Tagline',
            'bio' => 'Updated Bio',
            'email' => $profile->email,
            'university' => 'Updated University',
            'major' => 'Updated Major',
            'semester' => '7',
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('profiles', [
            'name' => 'Updated Name',
            'tagline' => 'Updated Tagline',
            'bio' => 'Updated Bio',
        ]);
    }

    // ─── STATS SYNC TESTS ────────────────────────────────────────────────────

    public function test_stats_update_reflects_in_database(): void
    {
        Stat::getSingleton();

        $response = $this->actingAs($this->user)->patch('/admin/stats', [
            'projects_count' => 42,
            'semesters_count' => 8,
            'experiences_count' => 5,
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('stats', [
            'projects_count' => 42,
            'semesters_count' => 8,
            'experiences_count' => 5,
        ]);
    }

    // ─── SKILLS SYNC TESTS ──────────────────────────────────────────────────

    public function test_creating_skill_stores_in_database(): void
    {
        $response = $this->actingAs($this->user)->post('/admin/skills', [
            'category_number' => '01',
            'category_label' => 'Programming',
            'category_title' => 'Backend Development',
            'tags' => ['PHP', 'Laravel'],
            'is_active' => true,
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('skills', [
            'category_title' => 'Backend Development',
            'category_label' => 'Programming',
            'is_active' => true,
        ]);
    }

    public function test_toggling_skill_updates_database(): void
    {
        $skill = Skill::factory()->create(['is_active' => false]);

        $response = $this->actingAs($this->user)->patch("/admin/skills/{$skill->id}/toggle");
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('skills', [
            'id' => $skill->id,
            'is_active' => true,
        ]);
    }

    public function test_deactivating_skill_updates_database(): void
    {
        $skill = Skill::factory()->create(['is_active' => true]);

        $this->actingAs($this->user)->patch("/admin/skills/{$skill->id}/toggle");

        $this->assertDatabaseHas('skills', [
            'id' => $skill->id,
            'is_active' => false,
        ]);
    }

    // ─── EXPERIENCES SYNC TESTS ─────────────────────────────────────────────

    public function test_creating_experience_stores_in_database(): void
    {
        $response = $this->actingAs($this->user)->post('/admin/experiences', [
            'period' => '2024 - Present',
            'role' => 'Senior Developer',
            'organization' => 'Tech Company',
            'description' => 'Building awesome stuff',
            'is_active' => true,
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('experiences', [
            'role' => 'Senior Developer',
            'organization' => 'Tech Company',
            'is_active' => true,
        ]);
    }

    public function test_deactivating_experience_updates_database(): void
    {
        $experience = Experience::factory()->create(['is_active' => true]);

        $this->actingAs($this->user)->patch("/admin/experiences/{$experience->id}/toggle");

        $this->assertDatabaseHas('experiences', [
            'id' => $experience->id,
            'is_active' => false,
        ]);
    }

    // ─── PROJECTS SYNC TESTS ────────────────────────────────────────────────

    public function test_creating_project_stores_in_database(): void
    {
        $response = $this->actingAs($this->user)->post('/admin/projects', [
            'title' => 'New Project',
            'description' => 'A new project',
            'tags' => ['Vue', 'Laravel'],
            'repo_status' => 'available',
            'is_active' => true,
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('projects', [
            'title' => 'New Project',
            'is_active' => true,
        ]);
    }

    public function test_deactivating_project_updates_database(): void
    {
        $project = Project::factory()->create(['is_active' => true]);

        $this->actingAs($this->user)->patch("/admin/projects/{$project->id}/toggle");

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'is_active' => false,
        ]);
    }

    // ─── CERTIFICATES SYNC TESTS ────────────────────────────────────────────

    public function test_creating_certificate_stores_in_database(): void
    {
        $response = $this->actingAs($this->user)->post('/admin/certificates', [
            'title' => 'AWS Certificate',
            'issuer' => 'Amazon',
            'issued_date' => '2024-01-01',
            'file' => \Illuminate\Http\Testing\File::create('certificate.pdf', 100),
            'is_active' => true,
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('certificates', [
            'title' => 'AWS Certificate',
            'issuer' => 'Amazon',
            'is_active' => true,
        ]);
    }

    public function test_deactivating_certificate_updates_database(): void
    {
        $certificate = Certificate::factory()->create(['is_active' => true]);

        $this->actingAs($this->user)->patch("/admin/certificates/{$certificate->id}/toggle");

        $this->assertDatabaseHas('certificates', [
            'id' => $certificate->id,
            'is_active' => false,
        ]);
    }

    // ─── SOCIAL LINKS SYNC TESTS ────────────────────────────────────────────

    public function test_updating_social_links_stores_in_database(): void
    {
        $link = SocialLink::factory()->create([
            'platform' => 'github',
            'url' => 'https://github.com/old-username',
            'label' => 'GitHub',
        ]);

        $response = $this->actingAs($this->user)->put("/admin/social-links/{$link->id}", [
            'platform' => 'github',
            'label' => 'GitHub',
            'url' => 'https://github.com/new-username',
            'is_active' => true,
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('social_links', [
            'platform' => 'github',
            'url' => 'https://github.com/new-username',
        ]);
    }

    // ─── CV SYNC TESTS ─────────────────────────────────────────────────────

    public function test_activating_cv_updates_database(): void
    {
        $cv = Cv::factory()->create(['is_active' => false]);

        $this->actingAs($this->user)->patch("/admin/cv/{$cv->id}/activate");

        $this->assertDatabaseHas('cvs', [
            'id' => $cv->id,
            'is_active' => true,
        ]);
    }

    public function test_only_one_cv_can_be_active(): void
    {
        $cv1 = Cv::factory()->create(['is_active' => true]);
        $cv2 = Cv::factory()->create(['is_active' => false]);

        $this->actingAs($this->user)->patch("/admin/cv/{$cv2->id}/activate");

        $cv1->refresh();
        $cv2->refresh();

        $this->assertFalse($cv1->is_active);
        $this->assertTrue($cv2->is_active);
    }

    // ─── DISPLAY ORDER TESTS ────────────────────────────────────────────────

    public function test_skills_respect_display_order(): void
    {
        Skill::factory()->create(['category_title' => 'First', 'display_order' => 1, 'is_active' => true]);
        Skill::factory()->create(['category_title' => 'Second', 'display_order' => 2, 'is_active' => true]);

        $skills = Skill::active()->get();

        $this->assertEquals('First', $skills[0]->category_title);
        $this->assertEquals('Second', $skills[1]->category_title);
    }

    public function test_experiences_respect_display_order(): void
    {
        Experience::factory()->create(['role' => 'First Role', 'display_order' => 1, 'is_active' => true]);
        Experience::factory()->create(['role' => 'Second Role', 'display_order' => 2, 'is_active' => true]);

        $experiences = Experience::active()->get();

        $this->assertEquals('First Role', $experiences[0]->role);
        $this->assertEquals('Second Role', $experiences[1]->role);
    }

    public function test_featured_projects_appear_first(): void
    {
        Project::factory()->create(['title' => 'Regular', 'is_featured' => false, 'is_active' => true, 'display_order' => 1]);
        Project::factory()->create(['title' => 'Featured', 'is_featured' => true, 'is_active' => true, 'display_order' => 2]);

        $projects = Project::active()->get();

        $this->assertEquals('Featured', $projects[0]->title);
        $this->assertEquals('Regular', $projects[1]->title);
    }
}
