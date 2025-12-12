<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Access;
use App\Models\User;
use App\Models\Video;
use App\Models\Permission;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard with statistics and pending requests
     */
    public function index()
    {
        // Statistics
        $stats = [
            'total_customers' => User::customers()->count(),
            'active_customers' => User::customers()->active()->count(),
            'total_videos' => Video::count(),
            'active_videos' => Video::active()->count(),
            'pending_requests' => Access::pending()->count(),
            'approved_requests' => Access::approved()->count(),
            'rejected_requests' => Access::rejected()->count(),
            'active_permissions' => Permission::active()->count(),
        ];

        // Recent pending access requests
        $pendingRequests = Access::with(['customer', 'video'])
            ->pending()
            ->latest()
            ->take(5)
            ->get();

        // Recent approved requests
        $recentApprovals = Access::with(['customer', 'video', 'permission'])
            ->approved()
            ->latest()
            ->take(5)
            ->get();

        // Active permissions that will expire soon (within 1 hour)
        $expiringPermissions = Permission::with(['customer', 'video'])
            ->active()
            ->where('expires_at', '<=', now()->addHour())
            ->orderBy('expires_at', 'asc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'pendingRequests',
            'recentApprovals',
            'expiringPermissions'
        ));
    }
}