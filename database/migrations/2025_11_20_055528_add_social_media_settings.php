<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add social media auto-posting settings
        $settings = [
            'social_media_auto_post' => 'false',
            'facebook_auto_post' => 'false',
            'facebook_page_id' => '',
            'facebook_access_token' => '',
            'twitter_auto_post' => 'false',
            'twitter_api_key' => '',
            'twitter_api_secret' => '',
            'twitter_access_token' => '',
            'twitter_access_token_secret' => '',
            'linkedin_auto_post' => 'false',
            'linkedin_access_token' => '',
            'linkedin_organization_id' => '',
        ];

        foreach ($settings as $key => $value) {
            Setting::create([
                'key' => $key,
                'value' => $value,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $keys = [
            'social_media_auto_post',
            'facebook_auto_post',
            'facebook_page_id',
            'facebook_access_token',
            'twitter_auto_post',
            'twitter_api_key',
            'twitter_api_secret',
            'twitter_access_token',
            'twitter_access_token_secret',
            'linkedin_auto_post',
            'linkedin_access_token',
            'linkedin_organization_id',
        ];

        Setting::whereIn('key', $keys)->delete();
    }
};
