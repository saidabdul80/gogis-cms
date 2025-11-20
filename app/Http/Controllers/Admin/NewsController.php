<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Services\SocialMediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class NewsController extends Controller
{
    protected $socialMediaService;

    public function __construct(SocialMediaService $socialMediaService)
    {
        $this->socialMediaService = $socialMediaService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = News::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Filter by published status
        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'published') {
                $query->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
            } elseif ($request->status === 'draft') {
                $query->where(function ($q) {
                    $q->whereNull('published_at')
                        ->orWhere('published_at', '>', now());
                });
            }
        }

        $news = $query->latest('created_at')->paginate(15);

        return Inertia::render('Admin/News/Index', [
            'news' => $news,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/News/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'published_at' => 'nullable|date',
            'post_to_social_media' => 'boolean',
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['title']);

        // Ensure unique slug
        $originalSlug = $validated['slug'];
        $count = 1;
        while (News::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count;
            $count++;
        }

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('news', 'public');
            $validated['cover_image'] = '/storage/' . $path;
        }

        $news = News::create($validated);

        // Post to social media if requested and published
        if ($request->post_to_social_media && $news->published_at && $news->published_at <= now()) {
            $this->socialMediaService->postNews($news);
        }

        return redirect()->route('admin.news.index')->with('success', 'News created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(News $news)
    {
        return Inertia::render('Admin/News/Show', [
            'news' => $news,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(News $news)
    {
        return Inertia::render('Admin/News/Edit', [
            'news' => $news,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'published_at' => 'nullable|date',
            'post_to_social_media' => 'boolean',
        ]);

        // Update slug if title changed
        if ($validated['title'] !== $news->title) {
            $validated['slug'] = Str::slug($validated['title']);

            // Ensure unique slug
            $originalSlug = $validated['slug'];
            $count = 1;
            while (News::where('slug', $validated['slug'])->where('id', '!=', $news->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count;
                $count++;
            }
        }

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            // Delete old image
            if ($news->cover_image) {
                $oldPath = str_replace('/storage/', '', $news->cover_image);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('cover_image')->store('news', 'public');
            $validated['cover_image'] = '/storage/' . $path;
        } else {
            // Remove cover_image from validated data if not provided
            unset($validated['cover_image']);
        }

        $wasUnpublished = !$news->published_at || $news->published_at > now();

        $news->update($validated);

        // Post to social media if requested and newly published
        if ($request->post_to_social_media && $news->published_at && $news->published_at <= now() && $wasUnpublished) {
            $this->socialMediaService->postNews($news);
        }

        return redirect()->route('admin.news.index')->with('success', 'News updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news)
    {
        // Delete cover image
        if ($news->cover_image) {
            $path = str_replace('/storage/', '', $news->cover_image);
            Storage::disk('public')->delete($path);
        }

        $news->delete();

        return redirect()->route('admin.news.index')->with('success', 'News deleted successfully!');
    }
}
