<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Cv;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Skill;
use App\Models\SocialLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SoftDeletesTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleted_skills_can_be_recovered(): void
    {
        $skill = Skill::factory()->create();
        $skillId = $skill->id;

        $skill->delete();

        $this->assertSoftDeleted('skills', ['id' => $skillId]);

        // Recover
        $recovered = Skill::withTrashed()->find($skillId);
        $recovered->restore();

        $this->assertDatabaseHas('skills', ['id' => $skillId]);
    }

    public function test_deleted_experiences_can_be_recovered(): void
    {
        $experience = Experience::factory()->create();
        $experienceId = $experience->id;

        $experience->delete();

        $this->assertSoftDeleted('experiences', ['id' => $experienceId]);

        // Recover
        $recovered = Experience::withTrashed()->find($experienceId);
        $recovered->restore();

        $this->assertDatabaseHas('experiences', ['id' => $experienceId]);
    }

    public function test_deleted_projects_can_be_recovered(): void
    {
        $project = Project::factory()->create();
        $projectId = $project->id;

        $project->delete();

        $this->assertSoftDeleted('projects', ['id' => $projectId]);

        // Recover
        $recovered = Project::withTrashed()->find($projectId);
        $recovered->restore();

        $this->assertDatabaseHas('projects', ['id' => $projectId]);
    }

    public function test_deleted_certificates_can_be_recovered(): void
    {
        $certificate = Certificate::factory()->create();
        $certificateId = $certificate->id;

        $certificate->delete();

        $this->assertSoftDeleted('certificates', ['id' => $certificateId]);

        // Recover
        $recovered = Certificate::withTrashed()->find($certificateId);
        $recovered->restore();

        $this->assertDatabaseHas('certificates', ['id' => $certificateId]);
    }

    public function test_deleted_cvs_can_be_recovered(): void
    {
        $cv = Cv::factory()->create();
        $cvId = $cv->id;

        $cv->delete();

        $this->assertSoftDeleted('cvs', ['id' => $cvId]);

        // Recover
        $recovered = Cv::withTrashed()->find($cvId);
        $recovered->restore();

        $this->assertDatabaseHas('cvs', ['id' => $cvId]);
    }

    public function test_deleted_social_links_can_be_recovered(): void
    {
        $socialLink = SocialLink::factory()->create();
        $socialLinkId = $socialLink->id;

        $socialLink->delete();

        $this->assertSoftDeleted('social_links', ['id' => $socialLinkId]);

        // Recover
        $recovered = SocialLink::withTrashed()->find($socialLinkId);
        $recovered->restore();

        $this->assertDatabaseHas('social_links', ['id' => $socialLinkId]);
    }
}
