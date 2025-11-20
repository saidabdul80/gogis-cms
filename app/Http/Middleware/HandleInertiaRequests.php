<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'currentRoute' => $request->route()->getName(),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'csrf_token' => csrf_token(),
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'warning' => $request->session()->get('warning'),
                'info' => $request->session()->get('info'),
                'paymentData' => $request->session()->get('paymentData'),
            ],
            // Global settings for dynamic theming and site info
            'appSettings' => [
                'siteName' => Setting::get('site_name', 'GOGIS'),
                'siteTagline' => Setting::get('site_tagline'),
                'logo' => Setting::get('logo'),
                'primaryColor' => Setting::get('primary_color', '#1c6434'),
                'secondaryColor' => Setting::get('secondary_color', '#fecd07'),
                'accentColor' => Setting::get('accent_color', '#c39913'),
                'contactEmail' => Setting::get('contact_email'),
                'contactPhone' => Setting::get('contact_phone'),
                'address' => Setting::get('address'),
                'fbLink' => Setting::get('fb_link'),
                'instagramLink' => Setting::get('instagram_link'),
                'twitterLink' => Setting::get('twitter_link'),
                'linkedinLink' => Setting::get('linkedin_link'),
                'footerText' => Setting::get('footer_text'),
                // About GOGIS section
                'aboutBackground' => Setting::get('about_background'),
                'aboutObjective' => Setting::get('about_objective'),
                'aboutTimeline' => Setting::get('about_timeline'),
            ],
        ];
    }
}
