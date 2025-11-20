<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\News;
use Inertia\Inertia;

class NewsController extends Controller
{
    public function index()
    {
        return Inertia::render('Public/News/Index', [
            'news' => News::published()
                ->latest('published_at')
                ->get(),
            'settings' => [
                'site_name' => \App\Models\Setting::get('site_name'),
                'primary_color' => \App\Models\Setting::get('primary_color'),
                'secondary_color' => \App\Models\Setting::get('secondary_color'),
                'accent_color' => \App\Models\Setting::get('accent_color'),
            ],
        ]);
    }

    public function show(News $news)
    {
        return Inertia::render('Public/News/Show', [
            'news' => $news,
            'relatedNews' => News::published()
                ->where('id', '!=', $news->id)
                ->latest('published_at')
                ->take(3)
                ->get(),
        ]);
    }
}
