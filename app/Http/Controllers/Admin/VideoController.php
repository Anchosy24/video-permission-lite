<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    /**
     * Display a listing of videos
     */
    public function index(Request $request)
    {
        $query = Video::withCount(['accessRequests', 'activePermissions']);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('is_active', $request->status === 'active' ? true : false);
        }

        $videos = $query->latest()->paginate(10);

        return view('admin.videos.index', compact('videos'));
    }

    /**
     * Show the form for creating a new video
     */
    public function create()
    {
        return view('admin.videos.form', ['video' => null]);
    }

    /**
     * Store a newly created video
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'required|url',
            'duration' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ], [
            'title.required' => 'Judul video harus diisi',
            'video_url.required' => 'URL video harus diisi',
            'video_url.url' => 'Format URL tidak valid',
            'duration.required' => 'Durasi video harus diisi',
            'duration.integer' => 'Durasi harus berupa angka',
            'duration.min' => 'Durasi minimal 0 menit',
        ]);

        try {
            Video::create([
                'title' => $request->title,
                'description' => $request->description,
                'video_url' => $request->video_url,
                'duration' => $request->duration,
                'is_active' => $request->is_active,
            ]);

            return redirect()->route('admin.videos.index')
                ->with('success', 'Video berhasil ditambahkan');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the video
     */
    public function edit(Video $video)
    {
        return view('admin.videos.form', compact('video'));
    }

    /**
     * Update the video
     */
    public function update(Request $request, Video $video)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'required|url',
            'duration' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ], [
            'title.required' => 'Judul video harus diisi',
            'video_url.required' => 'URL video harus diisi',
            'video_url.url' => 'Format URL tidak valid',
            'duration.required' => 'Durasi video harus diisi',
            'duration.integer' => 'Durasi harus berupa angka',
            'duration.min' => 'Durasi minimal 0 menit',
        ]);

        try {
            $video->update([
                'title' => $request->title,
                'description' => $request->description,
                'video_url' => $request->video_url,
                'duration' => $request->duration,
                'is_active' => $request->is_active,
            ]);

            return redirect()->route('admin.videos.index')
                ->with('success', 'Video berhasil diperbarui');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the video
     */
    public function destroy(Video $video)
    {
        try {
            $title = $video->title;
            $video->delete();

            return redirect()->route('admin.videos.index')
                ->with('success', "Video \"{$title}\" berhasil dihapus");

        } catch (\Exception $e) {
            return redirect()->route('admin.videos.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}