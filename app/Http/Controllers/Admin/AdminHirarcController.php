<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hirarc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminHirarcController extends Controller
{
    public function index()
    {
        $view   = request()->get('view', 'list');
        $status = request()->get('status');
        $type   = request()->get('type');
        $search = request()->get('search');

        $total = Hirarc::count();
        $draftCount = Hirarc::where('status', 'draft')->count();
        $pendingCount = Hirarc::where('status', 'pending')->count();
        $approvedCount = Hirarc::where('status', 'approved')->count();
        $rejectedCount = Hirarc::where('status', 'rejected')->count();

        $pendingDeleteCount = \App\Models\HirarcDeleteRequest::where('status','pending')->count();

        if ($view === 'delete') {
            $requests = \App\Models\HirarcDeleteRequest::with(['hirarc', 'requester'])
                ->where('status', 'pending')
                ->latest()
                ->get();

            return view('admin.hirarc.index', compact(
                'view', 'requests', 'pendingDeleteCount', 'total', 'draftCount', 'pendingCount', 'approvedCount', 'rejectedCount'
            ));
        }

        if ($view === 'deleted') {
            $deleted = collect();
            return view('admin.hirarc.index', compact('view','deleted','total','draftCount','pendingCount','approvedCount','rejectedCount'));
        }

        $query = Hirarc::query();

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

        $hirarcs = $query->latest()->get();

        $pendingDeleteCount = \App\Models\HirarcDeleteRequest::where('status','pending')->count();

        return view('admin.hirarc.index', compact('hirarcs','view','status','type','search','pendingDeleteCount','total','draftCount','pendingCount','approvedCount','rejectedCount'));
    }

    public function show(Hirarc $hirarc)
    {
        $this->authorize('review', Hirarc::class);

        if ($hirarc->isUploaded()) {
            return view('admin.hirarc.show-uploaded', ['hirarc' => $hirarc]);
        }

        $hirarc->load([
            'workUnits',
            'chemicals.workUnit',
            'exposures.workUnit',
            'exposures.chemical',
            'exposures.riskEvaluation',
            'recommendations',
            'deleteRequests' => fn($q)=> $q->where('status','pending')->latest(),
        ]);

        $deleteRequest = $hirarc->deleteRequests->first();

        return view('admin.hirarc.show', compact('hirarc','deleteRequest'));
    }

    public function approve(Hirarc $hirarc)
    {
        $this->authorize('review', Hirarc::class);
        abort_if($hirarc->status !== 'pending', 403);

        $hirarc->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'admin_reason' => null,
        ]);

        return back()->with('success', 'HIRARC approved.');
    }

    public function reject(Request $request, Hirarc $hirarc)
    {
        $this->authorize('review', Hirarc::class);
        abort_if($hirarc->status !== 'pending', 403);

        $request->validate(['reason' => 'required|string|min:5']);

        $hirarc->update([
            'status'       => 'rejected',
            'admin_reason' => $request->reason,
        ]);

        return back()->with('success', 'HIRARC rejected.');
    }
}
