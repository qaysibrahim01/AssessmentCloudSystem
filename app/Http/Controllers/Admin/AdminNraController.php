<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Nra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminNraController extends Controller
{
    public function index()
    {
        $view   = request()->get('view', 'list');
        $status = request()->get('status');
        $type   = request()->get('type');
        $search = request()->get('search');

        $total = Nra::count();
        $draftCount = Nra::where('status', 'draft')->count();
        $pendingCount = Nra::where('status', 'pending')->count();
        $approvedCount = Nra::where('status', 'approved')->count();
        $rejectedCount = Nra::where('status', 'rejected')->count();

        $pendingDeleteCount = \App\Models\NraDeleteRequest::where('status','pending')->count();

        if ($view === 'delete') {
            $requests = \App\Models\NraDeleteRequest::with(['nra', 'requester'])
                ->where('status', 'pending')
                ->latest()
                ->get();

            return view('admin.nra.index', compact(
                'view', 'requests', 'pendingDeleteCount', 'total', 'draftCount', 'pendingCount', 'approvedCount', 'rejectedCount'
            ));
        }

        if ($view === 'deleted') {
            $deleted = collect();
            return view('admin.nra.index', compact('view','deleted','total','draftCount','pendingCount','approvedCount','rejectedCount'));
        }

        $query = Nra::query();

        if ($status) $query->where('status', $status);
        if ($type === 'uploaded') $query->whereNotNull('uploaded_pdf_path');
        if ($type === 'system') $query->whereNull('uploaded_pdf_path');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('company_name','like',"%{$search}%")
                  ->orWhere('assessor_name','like',"%{$search}%")
                  ->orWhere('id',$search);
            });
        }

        $nras = $query->latest()->get();

        $pendingDeleteCount = \App\Models\NraDeleteRequest::where('status','pending')->count();

        return view('admin.nra.index', compact('nras','view','status','type','search','pendingDeleteCount','total','draftCount','pendingCount','approvedCount','rejectedCount'));
    }

    public function show(Nra $nra)
    {
        $this->authorize('review', Nra::class);

        if ($nra->isUploaded()) {
            return view('admin.nra.show-uploaded', ['nra' => $nra]);
        }

        $nra->load([
            'workUnits',
            'chemicals.workUnit',
            'exposures.workUnit',
            'exposures.chemical',
            'exposures.riskEvaluation',
            'recommendations',
            'deleteRequests' => fn($q)=> $q->where('status','pending')->latest(),
        ]);

        $deleteRequest = $nra->deleteRequests->first();

        return view('admin.nra.show', compact('nra','deleteRequest'));
    }

    public function approve(Nra $nra)
    {
        $this->authorize('review', Nra::class);
        abort_if($nra->status !== 'pending', 403);

        $nra->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'admin_reason' => null,
        ]);

        return back()->with('success', 'NRA approved.');
    }

    public function reject(Request $request, Nra $nra)
    {
        $this->authorize('review', Nra::class);
        abort_if($nra->status !== 'pending', 403);

        $request->validate(['reason' => 'required|string|min:5']);

        $nra->update([
            'status'       => 'rejected',
            'admin_reason' => $request->reason,
        ]);

        return back()->with('success', 'NRA rejected.');
    }
}
