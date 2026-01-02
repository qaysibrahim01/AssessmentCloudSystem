<?php

namespace App\Http\Controllers;

use App\Models\Chra;
use App\Models\ChraWorkUnit;
use App\Models\ChraChemical;
use App\Models\ChraRecommendation;
use App\Models\ChraDeleteRequest;
use App\Models\ChraExposure;
use App\Models\ChraRiskEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ChraController extends Controller
{
    public function index(Request $request)
    {
        $query = Chra::where('user_id', Auth::id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $chras = $query->latest()->get();

        return view('chra.index', compact('chras'));
    }

    public function create()
    {
        return view('chra.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name'    => 'required|string',
            'company_address' => 'required|string',
            'assessment_type' => 'required|string',
            'assessment_date' => 'required|date',
            'description'     => 'nullable|string',
        ]);

        $chra = Chra::create([
            ...$validated,
            'user_id'       => Auth::id(),
            'assessor_name' => Auth::user()->name,
            'status'        => 'draft',
        ]);

        return redirect()->route('chra.edit', $chra);
    }

    public function show(Chra $chra)
    {
        $this->authorize('view', $chra);

        return view('chra.show', compact('chra'));
    }

    public function edit(Chra $chra)
    {
        $this->authorize('update', $chra);
        abort_if($chra->isLocked(), 403);

        return view('chra.edit', compact('chra'));
    }

    public function updateSections(Request $request, Chra $chra)
    {
        $this->authorize('update', $chra);
        abort_if($chra->isLocked(), 403);

        $validated = $request->validate([
            'assessment_objective'     => 'nullable|string',
            'process_description'      => 'nullable|string',
            'work_activities'          => 'nullable|string',
            'chemical_usage_areas'     => 'nullable|string',
            'overall_risk_profile'     => 'nullable|in:Low,Moderate,High',
            'assessor_conclusion'      => 'nullable|string',
            'implementation_timeframe' => 'nullable|string',
        ]);

        $chra->update($validated);

        return redirect()->route('chra.edit', $chra)->withFragment('section-g');
    }

    public function saveDraft(Chra $chra)
    {
        $this->authorize('update', $chra);
        abort_if($chra->status === 'approved', 403);

        $chra->update(['status' => 'draft']);

        return back()->with('success', 'Draft saved successfully.');
    }

    public function submitForApproval(Chra $chra)
    {
        $this->authorize('update', $chra);

        abort_if(!in_array($chra->status, ['draft', 'rejected']), 403);

        $errors = $chra->submissionErrors();
        if (!empty($errors)) {
            return redirect()
                ->route('chra.edit', $chra)
                ->with('submit_errors', $errors)
                ->withFragment('section-a');
        }

        $chra->update([
            'overall_risk_profile' => $chra->highestRiskLevel(),
            'status'               => 'pending',
            'submitted_at'         => now(),
        ]);

        return redirect()
            ->route('chra.show', $chra)
            ->with('success', 'CHRA submitted for approval.');
    }

    public function requestDelete(Request $request, Chra $chra)
    {
        $this->authorize('requestDelete', $chra);

        if ($chra->deleteRequests()->where('status', 'pending')->exists()) {
            return back()->with('error', 'Delete request already submitted.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|min:5',
        ]);

        ChraDeleteRequest::create([
            'chra_id'      => $chra->id,
            'requested_by' => Auth::id(),
            'reason'       => $validated['reason'],
            'status'       => 'pending',
        ]);

        return back()->with('success', 'Delete request submitted to admin.');
    }

    public function downloadPdf(Chra $chra)
    {
        $this->authorize('view', $chra);

        abort_if(!in_array($chra->status, ['pending', 'approved']), 403);

        $chra->load([
            'workUnits',
            'chemicals',
            'exposures.chemical',
            'exposures.riskEvaluation',
            'recommendations',
        ]);

        $pdf = Pdf::loadView('chra.pdf', compact('chra'))
            ->setPaper('a4', 'portrait');

        return $pdf->download(
            'CHRA_' .
            str_replace(' ', '_', $chra->company_name) .
            '_' .
            optional($chra->assessment_date)->format('Ymd') .
            '.pdf'
        );
    }
}
