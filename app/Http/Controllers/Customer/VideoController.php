<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\Permission;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    /**
     * Display video player with access validation
     */
    public function play(Video $video)
    {
        $customerId = auth()->id();

        // Cek apakah customer punya permission yang masih valid
        $permission = Permission::where('customer_id', $customerId)
            ->where('video_id', $video->id)
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->first();

        // Jika tidak ada permission atau sudah expired
        if (!$permission) {
            return redirect()->route('customer.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk menonton video ini atau akses Anda sudah habis.');
        }

        // Update: deactivate expired permissions
        Permission::checkAndDeactivateExpired();

        return view('customer.player', compact('video', 'permission'));
    }
}