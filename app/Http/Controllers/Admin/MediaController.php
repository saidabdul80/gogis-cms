<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MediaController extends Controller
{
    public function index()
    {
        $media = MediaGallery::latest()->paginate(24);

        return Inertia::render('Admin/Media/Index', [
            'media' => $media,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:10240',
            'type' => 'required|in:image,document',
        ]);

        $path = $request->file('file')->store('media', 'public');

        MediaGallery::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_path' => '/storage/' . $path,
            'file_type' => $validated['type'],
        ]);

        return redirect()->route('admin.media.index')->with('success', 'Media uploaded successfully!');
    }

    public function destroy($id)
    {
        $media = MediaGallery::findOrFail($id);
        
        // Delete file from storage
        $filePath = str_replace('/storage/', '', $media->file_path);
        Storage::disk('public')->delete($filePath);
        
        $media->delete();

        return redirect()->route('admin.media.index')->with('success', 'Media deleted successfully!');
    }
}

