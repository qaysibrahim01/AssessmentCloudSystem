<?php

namespace App\Http\Controllers;

use App\Models\Nra;
use App\Models\NraWorkUnit;
use App\Models\NraChemical;
use App\Models\NraRecommendation;
use App\Models\NraDeleteRequest;
use App\Models\NraExposure;
use App\Models\NraRiskEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class NraController extends Controller
{
    public function index(Request $request)
    {
        $query = Nra::query()
            ->where(function ($q) {
                $q->where('user_id', auth()->id())   // assessor-created
                ->orWhere('source', 'uploaded');  // admin-uploaded
            })
            ->with(['deleteRequests' => function ($q) {
                $q->latest();
            }]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('sort_by')) {
            $query->orderBy(
                $request->sort_by,
                $request->get('sort_order', 'desc')
            );
        } else {
            $query->latest();
        }

        $nras = $query->get();

        return view('nra.index', compact('nras'));
    }

    public function create()
    {
        return view('nra.create');
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

        $nra = Nra::create([
            ...$validated,
            'user_id'       => Auth::id(),
            'assessor_name' => Auth::user()->name,
            'status'        => 'draft',
        ]);

        return redirect()->route('nra.edit', $nra);
    }

    public function show(Nra $nra)
    {
        $this->authorize('view', $nra);

        $nra->load([
            'workUnits',
            'chemicals.workUnit',
            'recommendations',
        ]);

        return view('nra.show', compact('nra'));
    }

    public function edit(Nra $nra)
    {
        $this->authorize('update', $nra);
        abort_if($nra->isLocked(), 403);

        return view('nra.edit', compact('nra'));
    }

    public function updateSections(Request $request, Nra $nra)
    {
        $this->authorize('update', $nra);
        abort_if($nra->isLocked(), 403);
        abort_if($nra->hasPendingDeleteRequest(), 403);

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

        $objectives = $request->has('specified_objectives')
            ? collect($request->input('specified_objectives', []))
                ->map(fn ($v) => trim($v))
                ->filter()
                ->take(5)
                ->values()
                ->all()
            : ($nra->specified_objectives ?? []);

        if (count($objectives) < 2) {
            return back()
                ->withErrors(['specified_objectives' => 'Please provide at least two specified objectives.'])
                ->withInput();
        }

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
                $updateData[$field] = $nra->$field;
            }
        }

        $updateData['specified_objectives'] = $objectives;

        $nra->update($updateData);

        return redirect()->route('nra.edit', $nra)->withFragment('section-g');
    }

    public function saveDraft(Nra $nra)
    {
        $this->authorize('update', $nra);
        abort_if($nra->status === 'approved', 403);

        $nra->update(['status' => 'draft']);

        return back()->with('success', 'Draft saved successfully.');
    }

    public function submitForApproval(Nra $nra)
    {
        $this->authorize('update', $nra);

        abort_if(!in_array($nra->status, ['draft', 'rejected']), 403);

        $errors = $nra->submissionErrors();
        if (!$nra->assessor_registration_no)
            $errors[] = 'Competent Person Registration No (DOSH) not provided';

        if (!empty($errors)) {
            return redirect()
                ->route('nra.edit', $nra)
                ->with('submit_errors', $errors)
                ->withFragment('section-a');
        }

        $nra->update([
            'overall_risk_profile' => $nra->highestRiskLevel(),
            'status'               => 'pending',
            'submitted_at'         => now(),
        ]);

        return redirect()
            ->route('nra.show', $nra)
            ->with('success', 'NRA submitted for approval.');
    }

    public function requestDelete(Request $request, Nra $nra)
    {
        $this->authorize('requestDelete', $nra);

        if ($nra->deleteRequests()->where('status', 'pending')->exists()) {
            return back()->with('error', 'Delete request already submitted.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|min:3',
        ]);

        NraDeleteRequest::create([
            'nra_id'      => $nra->id,
            'requested_by' => auth()->id(),
            'reason'       => $validated['reason'],
            'status'       => 'pending',
        ]);

        return redirect()
            ->route('nra.index')
            ->with('success', 'Delete request submitted and sent to admin.');
    }



    public function downloadPdf(Nra $nra)
    {
        $this->authorize('view', $nra);

        abort_if(!in_array($nra->status, ['pending', 'approved']), 403);

        if ($nra->isUploaded() && $nra->uploaded_pdf_path) {
            return Storage::download(
                $nra->uploaded_pdf_path,
                'NRA_' . str_replace(' ', '_', $nra->company_name) . '.pdf'
            );
        }

        $nra->load([
            'workUnits',
            'chemicals',
            'exposures.chemical',
            'exposures.riskEvaluation',
            'recommendations',
        ]);

        $pdf = Pdf::loadView('nra.pdf', compact('nra'))
            ->setPaper('a4', 'portrait');

        return $pdf->download(
            'NRA_' .
            str_replace(' ', '_', $nra->company_name) .
            '_' .
            optional($nra->assessment_date)->format('Ymd') .
            '.pdf'
        );
    }

    public function showUploaded(Nra $nra)
    {
        $this->authorize('view', $nra);

        abort_if(!$nra->isUploaded(), 404);

        return view('nra.show-uploaded', compact('nra'));
    }
}
