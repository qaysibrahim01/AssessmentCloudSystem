<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chra;
use App\Models\ChraDeleteRequest;
use App\Models\AdminDeletedChra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminChraController extends Controller
{
    public function index(Request $request)
    {
        $view   = $request->get('view', 'list');
        $status = $request->get('status');
        $type   = $request->get('type');   // system | uploaded
        $search = $request->get('search');

        // ================= COUNTS =================
        $totalChra     = Chra::count();
        $draftCount    = Chra::where('status', 'draft')->count();
        $pendingCount  = Chra::where('status', 'pending')->count();
        $approvedCount = Chra::where('status', 'approved')->count();
        $rejectedCount = Chra::where('status', 'rejected')->count();

        $pendingDeleteCount = ChraDeleteRequest::where('status', 'pending')->count();

        // ================= DELETE REQUEST VIEW =================
        if ($view === 'delete') {
            $requests = ChraDeleteRequest::with(['chra', 'requester'])
                ->where('status', 'pending')
                ->latest()
                ->get();

            return view('admin.chra.index', compact(
                'view',
                'requests',
                'pendingDeleteCount',
                'totalChra',
                'draftCount',
                'pendingCount',
                'approvedCount',
                'rejectedCount'
            ));
        }

        // ================= DELETED REGISTRY VIEW =================
        if ($view === 'deleted') {
            $deletedChras = AdminDeletedChra::with(['requester', 'deleter'])
                ->orderByDesc('deleted_at')
                ->get();

            return view('admin.chra.index', compact(
                'view',
                'deletedChras',
                'pendingDeleteCount',
                'totalChra',
                'draftCount',
                'pendingCount',
                'approvedCount',
                'rejectedCount'
            ));
        }

        // ================= MAIN CHRA LIST =================
        $query = Chra::query();

        // Status filter (matches table column)
        if ($status) {
            $query->where('status', $status);
        }

        // Report type filter
        if ($type === 'uploaded') {
            $query->whereNotNull('uploaded_pdf_path');
        }

        if ($type === 'system') {
            $query->whereNull('uploaded_pdf_path');
        }

        // Search: ID / Company / Assessor
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('assessor_name', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        $chras = $query->latest()->get();

        return view('admin.chra.index', compact(
            'view',
            'chras',
            'status',
            'type',
            'search',
            'pendingDeleteCount',
            'totalChra',
            'draftCount',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }

    public function show(Chra $chra)
    {
        $this->authorize('view', $chra);

        $chra->load([
            'workUnits',
            'chemicals',
            'exposures.workUnit',
            'exposures.chemical',
            'exposures.riskEvaluation',
            'recommendations',
        ]);

        return view('admin.chra.show', compact('chra'));
    }

    public function approve(Chra $chra)
    {
        $this->authorize('review', Chra::class);

        abort_if($chra->status !== 'pending', 403);

        $chra->update([
            'status'       => 'approved',
            'approved_by'  => Auth::id(),
            'approved_at'  => now(),
            'admin_reason' => null,
        ]);

        return back()->with('success', 'CHRA approved successfully.');
    }

    public function reject(Request $request, Chra $chra)
    {
        $this->authorize('review', Chra::class);

        abort_if($chra->status !== 'pending', 403);

        $request->validate([
            'reason' => 'required|string|min:5',
        ]);

        $chra->update([
            'status'       => 'rejected',
            'admin_reason' => $request->reason,
        ]);

        return back()->with('success', 'CHRA rejected and returned to assessor.');
    }

}
