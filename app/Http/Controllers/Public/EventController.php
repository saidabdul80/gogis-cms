<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index()
    {
        return Inertia::render('Public/Events/Index', [
            'events' => Event::upcoming()
                ->get(),
            'settings' => [
                'site_name' => \App\Models\Setting::get('site_name'),
                'primary_color' => \App\Models\Setting::get('primary_color'),
                'secondary_color' => \App\Models\Setting::get('secondary_color'),
                'accent_color' => \App\Models\Setting::get('accent_color'),
            ],
        ]);
    }

    public function show(Event $event)
    {
        return Inertia::render('Public/Events/Show', [
            'event' => $event,
            'relatedEvents' => Event::upcoming()
                ->where('id', '!=', $event->id)
                ->take(3)
                ->get(),
        ]);
    }
}
