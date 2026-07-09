<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\SocialLink;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckoutSummaryController extends Controller
{
    public function __invoke(Request $request)
    {
        $profile = Profile::getSingleton();
        $socialLinks = SocialLink::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return Inertia::render('Public/Checkout/Summary', [
            // Items loaded client-side via Pinia store
            'items' => [],
            'total' => 0,
            'profile' => $profile ? [
                'name' => $profile->name,
                'tagline' => $profile->tagline,
                'email' => $profile->email,
            ] : null,
            'socialLinks' => $socialLinks->map(fn ($link) => [
                'id' => $link->id,
                'platform' => $link->platform,
                'url' => $link->url,
            ]),
        ]);
    }
}
