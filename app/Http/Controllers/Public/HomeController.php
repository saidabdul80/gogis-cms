<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Carousel;
use App\Models\Event;
use App\Models\News;
use App\Models\Setting;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $carouselEnabled = Setting::get('carousel_enabled', 'false') === 'true';

        return Inertia::render('Public/Home', [
            'latestNews' => News::published()
                ->latest('published_at')
                ->take(6)
                ->get(),
            'upcomingEvents' => Event::upcoming()
                ->take(4)
                ->get(),
            'carouselEnabled' => $carouselEnabled,
            'carouselSlides' => $carouselEnabled ? Carousel::active()->get() : [],
            'settings' => [
                'site_name' => Setting::get('site_name'),
                'site_tagline' => Setting::get('site_tagline'),
                'site_description' => Setting::get('site_description'),
                'primary_color' => Setting::get('primary_color'),
                'secondary_color' => Setting::get('secondary_color'),
                'accent_color' => Setting::get('accent_color'),
            ],
        ]);
    }

    public function about()
    {
        return Inertia::render('Public/About', [
            'page' => [
                'title' => 'About Us',
                'content' => Setting::get('about_us'),
            ],
            'settings' => [
                'site_name' => Setting::get('site_name'),
                'primary_color' => Setting::get('primary_color'),
                'secondary_color' => Setting::get('secondary_color'),
                'accent_color' => Setting::get('accent_color'),
                'dg_name' => Setting::get('dg_name'),
                'dg_title' => Setting::get('dg_title'),
                'dg_bio' => Setting::get('dg_bio'),
                'dg_image' => Setting::get('dg_image'),
            ],
        ]);
    }
}
