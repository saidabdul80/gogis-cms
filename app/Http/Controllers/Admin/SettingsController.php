<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string',
            'primary_color' => 'required|string|max:20',
            'secondary_color' => 'required|string|max:20',
            'accent_color' => 'required|string|max:20',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string',
            'address' => 'required|string',
            'fb_link' => 'nullable|url',
            'twitter_link' => 'nullable|url',
            'instagram_link' => 'nullable|url',
            'linkedin_link' => 'nullable|url',
            'about_us' => 'nullable|string',
            'dg_name' => 'nullable|string',
            'dg_title' => 'nullable|string',
            'dg_bio' => 'nullable|string',
            // Social Media API Settings
            'social_media_auto_post' => 'boolean',
            'facebook_auto_post' => 'boolean',
            'facebook_page_id' => 'nullable|string',
            'facebook_access_token' => 'nullable|string',
            'twitter_auto_post' => 'boolean',
            'twitter_api_key' => 'nullable|string',
            'twitter_api_secret' => 'nullable|string',
            'twitter_access_token' => 'nullable|string',
            'twitter_access_token_secret' => 'nullable|string',
            'linkedin_auto_post' => 'boolean',
            'linkedin_access_token' => 'nullable|string',
            'linkedin_organization_id' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            // Convert boolean values to string for storage
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            Setting::set($key, $value);
        }

        // Handle logo upload if present
        if ($request->hasFile('logo')) {
            $request->validate([
                'logo' => 'image|mimes:jpeg,png,jpg,svg|max:2048',
            ]);

            $path = $request->file('logo')->store('logos', 'public');
            Setting::set('logo', '/storage/' . $path);
        }

        // Handle DG image upload if present
        if ($request->hasFile('dg_image')) {
            $request->validate([
                'dg_image' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $path = $request->file('dg_image')->store('dg', 'public');
            Setting::set('dg_image', '/storage/' . $path);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated successfully!');
    }
}

