<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AdminChraController extends Controller
{
    public function index(Request $request)
    {
        // -------------------------
        // FILTER INPUTS
        // -------------------------
        $status = $request->get('status');
        $risk   = $request->get('risk');
        $ap     = $request->get('ap');

        // -------------------------
        // BASE QUERY
        // -------------------------
        $query = Chra::query()
            ->with('riskEvaluations');

        // -------------------------
        // APPLY FILTERS
        // -------------------------
        if ($status) {
            $query->where('status', $status);
        }

        if ($risk) {
            $query->whereHas('riskEvaluations', function ($q) use ($risk) {
                $q->where('risk_level', $risk);
            });
        }

        if ($ap) {
            $query->whereHas('riskEvaluations', function ($q) use ($ap) {
                $q->where('action_priority', $ap);
            });
        }

        $chras = $query
            ->orderByDesc('updated_at')
            ->get();

        // -------------------------
        // GLOBAL STATISTICS
        // -------------------------
        $totalChra     = Chra::count();
        $draftCount    = Chra::where('status', 'draft')->count();
        $pendingCount  = Chra::where('status', 'pending')->count();
        $approvedCount = Chra::where('status', 'approved')->count();
        $rejectedCount = Chra::where('status', 'rejected')->count();

        // -------------------------
        // RISK SUMMARY (DISTINCT CHRA)
        // -------------------------
        $highRiskCount = Chra::whereHas('riskEvaluations', function ($q) {
            $q->where('risk_level', 'high');
        })->count();

        $moderateRiskCount = Chra::whereHas('riskEvaluations', function ($q) {
            $q->where('risk_level', 'moderate');
        })->count();

        $lowRiskCount = Chra::whereHas('riskEvaluations', function ($q) {
            $q->where('risk_level', 'low');
        })->count();

        return view('admin.chra.index', compact(
            'chras',
            'status',
            'risk',
            'ap',
            'totalChra',
            'draftCount',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'highRiskCount',
            'moderateRiskCount',
            'lowRiskCount'
        ));
    }

    public function show(Chra $chra)
    {
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
        if ($chra->status !== 'pending') {
            return back()->with('error', 'Only pending CHRA can be approved.');
        }

        $chra->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'admin_reason'=> null,
        ]);

        return redirect()
            ->route('admin.chra.index')
            ->with('success', 'CHRA approved successfully.');
    }

    public function reject(Request $request, Chra $chra)
    {
        if ($chra->status !== 'pending') {
            return back()->with('error', 'Only pending CHRA can be rejected.');
        }

        $request->validate([
            'reason' => 'required|string|min:5',
        ]);

        $chra->update([
            'status'       => 'rejected',
            'admin_reason' => $request->reason,
        ]);

        return redirect()
            ->route('admin.chra.index')
            ->with('success', 'CHRA rejected and returned to assessor.');
    }



}
