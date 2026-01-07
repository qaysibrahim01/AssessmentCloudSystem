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
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ChraController extends Controller
{
    public function index(Request $request)
    {
        $query = Chra::query()
            ->where(function ($q) {
                $q->where('user_id', auth()->id())   // assessor-created
                ->orWhere('source', 'uploaded');  // admin-uploaded
            })
            ->with(['deleteRequests' => function ($q) {
                $q->latest();
            }]);

        // FILTER: STATUS
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // SORTING
        if ($request->filled('sort_by')) {
            $query->orderBy(
                $request->sort_by,
                $request->get('sort_order', 'desc')
            );
        } else {
            $query->latest();
        }

        $chras = $query->get();

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

        $chra->load([
            'workUnits',
            'chemicals.workUnit',
            'recommendations',
        ]);

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
        abort_if($chra->hasPendingDeleteRequest(), 403);

        $validated = $request->validate([
            'assessor_registration_no' => 'nullable|string|max:255',

            'general_objective'        => 'nullable|string',
            'specified_objectives'     => 'array',
            'specified_objectives.*'   => 'nullable|string',
            'process_description'      => 'nullable|string',
            'work_activities'          => 'nullable|string',
            'chemical_usage_areas'     => 'nullable|string',
            'assessment_location'      => 'nullable|string',
            'methodology_team'         => 'nullable|string',
            'methodology_degree_hazard'=> 'nullable|string',
            'methodology_assess_exposure' => 'nullable|string',
            'methodology_control_adequacy' => 'nullable|string',
            'methodology_conclusion'   => 'nullable|string',
            'overall_risk_profile'     => 'nullable|in:Low,Moderate,High',
            'assessor_conclusion'      => 'nullable|string',
            'implementation_timeframe' => 'nullable|string',
            'business_nature'          => 'nullable|string|max:255',
            'assisted_by'              => 'nullable|string|max:255',
            'dosh_ref_num'             => 'nullable|string|max:255',
        ]);

        // Keep existing specified objectives unless new values are submitted
        $objectives = $request->has('specified_objectives')
            ? collect($request->input('specified_objectives', []))
                ->map(fn ($v) => trim($v))
                ->filter()
                ->take(5)
                ->values()
                ->all()
            : ($chra->specified_objectives ?? []);

        if (count($objectives) < 2) {
            return back()
                ->withErrors(['specified_objectives' => 'Please provide at least two specified objectives.'])
                ->withInput();
        }

        // Only overwrite fields that were present in the request; preserve others
        $fields = [
            'assessor_registration_no',
            'general_objective',
            'process_description',
            'work_activities',
            'chemical_usage_areas',
            'assessment_location',
            'methodology_team',
            'methodology_degree_hazard',
            'methodology_assess_exposure',
            'methodology_control_adequacy',
            'methodology_conclusion',
            'overall_risk_profile',
            'assessor_conclusion',
            'implementation_timeframe',
            'business_nature',
            'assisted_by',
            'dosh_ref_num',
        ];

        $updateData = [];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $updateData[$field] = $validated[$field] ?? null;
            } else {
                $updateData[$field] = $chra->$field;
            }
        }

        $updateData['specified_objectives'] = $objectives;

        $chra->update($updateData);

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
        if (!$chra->assessor_registration_no)
            $errors[] = 'Competent Person Registration No (DOSH) not provided';

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
            'reason' => 'required|string|min:3',
        ]);

        ChraDeleteRequest::create([
            'chra_id'      => $chra->id,
            'requested_by' => auth()->id(),
            'reason'       => $validated['reason'],
            'status'       => 'pending',
        ]);

        return redirect()
            ->route('chra.index')
            ->with('success', 'Delete request submitted and sent to admin.');
    }



    public function downloadPdf(Chra $chra)
    {
        $this->authorize('view', $chra);

        abort_if(!in_array($chra->status, ['pending', 'approved']), 403);

        // If this is an admin-uploaded CHRA with a stored PDF, return the original file
        if ($chra->isUploaded() && $chra->uploaded_pdf_path) {
            return Storage::download(
                $chra->uploaded_pdf_path,
                'CHRA_' . str_replace(' ', '_', $chra->company_name) . '.pdf'
            );
        }

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

    public function showUploaded(Chra $chra)
    {
        $this->authorize('view', $chra);

        abort_if(!$chra->isUploaded(), 404);

        return view('chra.show-uploaded', compact('chra'));
    }



}
