<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\Access;
use App\Models\Permission;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display customer dashboard with available videos
     */
    public function index(Request $request)
    {
        $customerId = auth()->id();

        // Get all active videos
        $query = Video::active()->with(['accessRequests', 'activePermissions']);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $videos = $query->latest()->paginate(9);

        // Untuk setiap video, cek status access request dan permission
        $videos->getCollection()->transform(function ($video) use ($customerId) {
            // Cek apakah user sudah pernah request
            $video->user_access_request = Access::where('customer_id', $customerId)
                ->where('video_id', $video->id)
                ->latest()
                ->first();

            // Cek apakah user punya active permission
            $video->user_permission = Permission::where('customer_id', $customerId)
                ->where('video_id', $video->id)
                ->where('is_active', true)
                ->where('expires_at', '>', now())
                ->first();

            return $video;
        });

        // Statistics
        $stats = [
            'total_videos' => Video::active()->count(),
            'my_requests' => Access::where('customer_id', $customerId)->count(),
            'pending_requests' => Access::where('customer_id', $customerId)->pending()->count(),
            'active_permissions' => Permission::where('customer_id', $customerId)->active()->count(),
        ];

        return view('customer.dashboard', compact('videos', 'stats'));
    }
}