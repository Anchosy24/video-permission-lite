<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Access;
use Illuminate\Http\Request;

class AccessController extends Controller
{
    /**
     * Display a listing of access requests
     */
    public function index(Request $request)
    {
        $query = Access::with(['customer', 'video', 'permission']);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        } else {
            // Default tampilkan pending dulu
            $query->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')");
        }

        // Search by customer name or video title
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('video', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        $accesses = $query->latest()->paginate(15);

        // Statistics for badges
        $stats = [
            'pending' => Access::pending()->count(),
            'approved' => Access::approved()->count(),
            'rejected' => Access::rejected()->count(),
        ];

        return view('admin.accesses.index', compact('accesses', 'stats'));
    }

    /**
     * Approve access request
     */
    public function approve(Request $request, Access $access)
    {
        // Validasi status
        if (!$access->isPending()) {
            return back()->with('error', 'Request ini sudah diproses sebelumnya');
        }

        $request->validate([
            'duration_hours' => 'required|integer|min:1|max:168', // max 1 minggu
        ], [
            'duration_hours.required' => 'Durasi akses harus diisi',
            'duration_hours.integer' => 'Durasi harus berupa angka',
            'duration_hours.min' => 'Durasi minimal 1 jam',
            'duration_hours.max' => 'Durasi maksimal 168 jam (1 minggu)',
        ]);

        try {
            // Approve dan buat permission
            $permission = $access->approve($request->duration_hours);

            return back()->with('success', 
                "Request dari {$access->customer->name} untuk video \"{$access->video->title}\" telah disetujui. " .
                "Akses berlaku selama {$request->duration_hours} jam."
            );

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reject access request
     */
    public function reject(Request $request, Access $access)
    {
        // Validasi status
        if (!$access->isPending()) {
            return back()->with('error', 'Request ini sudah diproses sebelumnya');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ], [
            'reason.required' => 'Alasan penolakan harus diisi',
            'reason.max' => 'Alasan maksimal 500 karakter',
        ]);

        try {
            $access->update([
                'status' => 'rejected',
                'reason' => $request->reason,
            ]);

            return back()->with('success', 
                "Request dari {$access->customer->name} untuk video \"{$access->video->title}\" telah ditolak."
            );

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}