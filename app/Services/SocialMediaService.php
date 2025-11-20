<?php

namespace App\Services;

use App\Models\News;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocialMediaService
{
    /**
     * Post news to all enabled social media platforms
     */
    public function postNews(News $news): array
    {
        $results = [];

        // Check if social media posting is enabled
        if (Setting::get('social_media_auto_post') !== 'true') {
            return ['status' => 'disabled', 'message' => 'Social media auto-posting is disabled'];
        }

        // Prepare the post content
        $postContent = $this->preparePostContent($news);
        $postUrl = url('/news/' . $news->slug);

        // Post to Facebook
        if (Setting::get('facebook_auto_post') === 'true') {
            $results['facebook'] = $this->postToFacebook($postContent, $postUrl, $news->cover_image);
        }

        // Post to Twitter/X
        if (Setting::get('twitter_auto_post') === 'true') {
            $results['twitter'] = $this->postToTwitter($postContent, $postUrl);
        }

        // Post to LinkedIn
        if (Setting::get('linkedin_auto_post') === 'true') {
            $results['linkedin'] = $this->postToLinkedIn($postContent, $postUrl, $news->cover_image);
        }

        // Log the results
        Log::info('Social media posting results for news: ' . $news->title, $results);

        return $results;
    }

    /**
     * Prepare post content from news
     */
    protected function preparePostContent(News $news): string
    {
        // Strip HTML tags and get first 200 characters
        $content = strip_tags($news->content);
        $excerpt = strlen($content) > 200 ? substr($content, 0, 200) . '...' : $content;
        
        return $news->title . "\n\n" . $excerpt;
    }

    /**
     * Post to Facebook Page
     */
    protected function postToFacebook(string $content, string $url, ?string $image): array
    {
        try {
            $pageId = Setting::get('facebook_page_id');
            $accessToken = Setting::get('facebook_access_token');

            if (!$pageId || !$accessToken) {
                return ['success' => false, 'error' => 'Facebook credentials not configured'];
            }

            $data = [
                'message' => $content . "\n\nRead more: " . $url,
                'access_token' => $accessToken,
            ];

            if ($image) {
                $data['link'] = $url;
            }

            $response = Http::post("https://graph.facebook.com/v18.0/{$pageId}/feed", $data);

            if ($response->successful()) {
                return ['success' => true, 'post_id' => $response->json('id')];
            }

            return ['success' => false, 'error' => $response->json('error.message', 'Unknown error')];
        } catch (\Exception $e) {
            Log::error('Facebook posting error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Post to Twitter/X
     */
    protected function postToTwitter(string $content, string $url): array
    {
        try {
            $apiKey = Setting::get('twitter_api_key');
            $apiSecret = Setting::get('twitter_api_secret');
            $accessToken = Setting::get('twitter_access_token');
            $accessTokenSecret = Setting::get('twitter_access_token_secret');

            if (!$apiKey || !$apiSecret || !$accessToken || !$accessTokenSecret) {
                return ['success' => false, 'error' => 'Twitter credentials not configured'];
            }

            // Twitter API v2 requires OAuth 1.0a
            // For simplicity, we'll use a basic implementation
            // In production, consider using a package like abraham/twitteroauth
            
            $tweet = substr($content, 0, 250) . "\n\n" . $url;

            // Note: This is a placeholder. Actual Twitter API implementation requires OAuth 1.0a signing
            return ['success' => false, 'error' => 'Twitter API integration requires OAuth 1.0a implementation'];
        } catch (\Exception $e) {
            Log::error('Twitter posting error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Post to LinkedIn
     */
    protected function postToLinkedIn(string $content, string $url, ?string $image): array
    {
        try {
            $accessToken = Setting::get('linkedin_access_token');
            $organizationId = Setting::get('linkedin_organization_id');

            if (!$accessToken || !$organizationId) {
                return ['success' => false, 'error' => 'LinkedIn credentials not configured'];
            }

            $postData = [
                'author' => 'urn:li:organization:' . $organizationId,
                'lifecycleState' => 'PUBLISHED',
                'specificContent' => [
                    'com.linkedin.ugc.ShareContent' => [
                        'shareCommentary' => [
                            'text' => $content . "\n\nRead more: " . $url
                        ],
                        'shareMediaCategory' => 'NONE'
                    ]
                ],
                'visibility' => [
                    'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC'
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
                'X-Restli-Protocol-Version' => '2.0.0'
            ])->post('https://api.linkedin.com/v2/ugcPosts', $postData);

            if ($response->successful()) {
                return ['success' => true, 'post_id' => $response->json('id')];
            }

            return ['success' => false, 'error' => $response->json('message', 'Unknown error')];
        } catch (\Exception $e) {
            Log::error('LinkedIn posting error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

