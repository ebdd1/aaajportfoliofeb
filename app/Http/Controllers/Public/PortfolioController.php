<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactMessageRequest;
use App\Http\Resources\Blog\BlogPostResource;
use App\Mail\NewContactMessage;
use App\Models\Blog\Post;
use App\Models\Certificate;
use App\Models\Cv;
use App\Models\Experience;
use App\Models\Message;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Skill;
use App\Models\SiteSetting;
use App\Models\SocialLink;
use App\Models\Stat;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PortfolioController extends Controller
{
    public function index(): Response
    {
        $siteSetting = SiteSetting::getSingleton();

        return Inertia::render('Public/Portfolio', [
            'profile' => Profile::getSingleton(),
            'stats' => Stat::getSingleton(),
            'skills' => Skill::active()->get(),
            'experiences' => Experience::active()->get(),
            'projects' => Project::active()->get(),
            'certificates' => Certificate::active()->get(),
            'socialLinks' => SocialLink::active()->get(),
            'hasActiveCv' => Cv::getActive() !== null,
            'latestPosts' => BlogPostResource::collection(
                Post::published()
                    ->with(['author', 'categories'])
                    ->orderByDesc('published_at')
                    ->limit(3)
                    ->get()
            ),
            'settings' => [
                'hero_background_url' => $siteSetting->hero_background_url,
                'hero_opacity' => $siteSetting->hero_opacity ?? 30,
                'hero_title_font' => $siteSetting->hero_title_font ?? 'font-serif',
                'hero_title_color' => $siteSetting->hero_title_color ?? '#3D3929',
                'hero_title_size' => $siteSetting->hero_title_size ?? 'text-6xl',
                'hero_subtitle_font' => $siteSetting->hero_subtitle_font ?? 'font-sans',
                'hero_subtitle_color' => $siteSetting->hero_subtitle_color ?? '#8C8273',
                'hero_subtitle_size' => $siteSetting->hero_subtitle_size ?? 'text-2xl',
                'hero_stat_value_font' => $siteSetting->hero_stat_value_font ?? 'font-mono',
                'hero_stat_value_color' => $siteSetting->hero_stat_value_color ?? '#3D3929',
                'hero_stat_label_font' => $siteSetting->hero_stat_label_font ?? 'font-sans',
                'hero_stat_label_color' => $siteSetting->hero_stat_label_color ?? '#8C8273',
                'hero_stat_card_bg_color' => $siteSetting->hero_stat_card_bg_color ?? '#F5F1E8',
                'hero_stat_card_opacity' => $siteSetting->hero_stat_card_opacity ?? 100,
                'hero_stat_card_border' => $siteSetting->hero_stat_card_border ?? false,
                'hero_stat_card_border_color' => $siteSetting->hero_stat_card_border_color ?? '#E8E2D3',
                'hero_stat_card_backdrop_blur' => $siteSetting->hero_stat_card_backdrop_blur ?? false,
            ],
        ]);
    }

    public function sendMessage(ContactMessageRequest $request)
    {
        $message = Message::create([
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        Mail::to(config('mail.admin_notification_email', env('ADMIN_NOTIFICATION_EMAIL')))
            ->queue(new NewContactMessage($message));

        return back()->with('success', 'Pesan berhasil terkirim. Terima kasih!');
    }

    public function downloadCv(): BinaryFileResponse
    {
        $cv = Cv::getActive();

        abort_if(!$cv, 404, 'CV tidak tersedia saat ini.');

        return response()->download(
            storage_path('app/public/' . $cv->file_path),
            $cv->original_filename
        );
    }

    public function showCertificate(Certificate $certificate): Response
    {
        abort_if(!$certificate->is_active, 404);

        return Inertia::render('Public/Certificate', [
            'certificate' => $certificate,
        ]);
    }
}
