<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carousel;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CarouselController extends Controller
{
    public function index()
    {
        $carousels = Carousel::orderBy('order', 'asc')->get();
        $carouselEnabled = Setting::get('carousel_enabled', 'false') === 'true';

        return Inertia::render('Admin/Carousel/Index', [
            'carousels' => $carousels,
            'carouselEnabled' => $carouselEnabled,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Carousel/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('carousel', 'public');
            $validated['image'] = '/storage/' . $path;
        }

        Carousel::create($validated);

        return redirect()->route('admin.carousel.index')->with('success', 'Carousel slide created successfully!');
    }

    public function edit(Carousel $carousel)
    {
        return Inertia::render('Admin/Carousel/Edit', [
            'carousel' => $carousel,
        ]);
    }

    public function update(Request $request, Carousel $carousel)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($carousel->image) {
                $oldPath = str_replace('/storage/', '', $carousel->image);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('image')->store('carousel', 'public');
            $validated['image'] = '/storage/' . $path;
        } else {
            // Remove image from validated data if not provided to prevent overwriting
            unset($validated['image']);
        }

        $carousel->update($validated);

        return redirect()->route('admin.carousel.index')->with('success', 'Carousel slide updated successfully!');
    }

    public function destroy(Carousel $carousel)
    {
        // Delete image
        if ($carousel->image) {
            $path = str_replace('/storage/', '', $carousel->image);
            Storage::disk('public')->delete($path);
        }

        $carousel->delete();

        return redirect()->route('admin.carousel.index')->with('success', 'Carousel slide deleted successfully!');
    }

    public function toggleCarousel(Request $request)
    {
        $enabled = $request->input('enabled', false);
        Setting::set('carousel_enabled', $enabled ? 'true' : 'false');

        return redirect()->route('admin.carousel.index')->with('success', 'Carousel ' . ($enabled ? 'enabled' : 'disabled') . ' successfully!');
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:carousels,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        foreach ($validated['items'] as $item) {
            Carousel::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return redirect()->route('admin.carousel.index')->with('success', 'Carousel order updated successfully!');
    }
}

