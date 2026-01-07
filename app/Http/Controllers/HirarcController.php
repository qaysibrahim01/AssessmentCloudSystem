<?php

namespace App\Http\Controllers;

use App\Models\Hirarc;
use App\Models\HirarcWorkUnit;
use App\Models\HirarcChemical;
use App\Models\HirarcRecommendation;
use App\Models\HirarcDeleteRequest;
use App\Models\HirarcExposure;
use App\Models\HirarcRiskEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class HirarcController extends Controller
{
    public function index(Request $request)
    {
        $query = Hirarc::query()
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

        $hirarcs = $query->get();

        return view('hirarc.index', compact('hirarcs'));
    }

    public function create()
    {
        return view('hirarc.create');
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

        $hirarc = Hirarc::create([
            ...$validated,
            'user_id'       => Auth::id(),
            'assessor_name' => Auth::user()->name,
            'status'        => 'draft',
        ]);

        return redirect()->route('hirarc.edit', $hirarc);
    }

    public function show(Hirarc $hirarc)
    {
        $this->authorize('view', $hirarc);

        $hirarc->load([
            'workUnits',
            'chemicals.workUnit',
            'recommendations',
        ]);

        return view('hirarc.show', compact('hirarc'));
    }

    public function edit(Hirarc $hirarc)
    {
        $this->authorize('update', $hirarc);
        abort_if($hirarc->isLocked(), 403);

        return view('hirarc.edit', compact('hirarc'));
    }

    public function updateSections(Request $request, Hirarc $hirarc)
    {
        $this->authorize('update', $hirarc);
        abort_if($hirarc->isLocked(), 403);
        abort_if($hirarc->hasPendingDeleteRequest(), 403);

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
            : ($hirarc->specified_objectives ?? []);

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
                $updateData[$field] = $hirarc->$field;
            }
        }

        $updateData['specified_objectives'] = $objectives;

        $hirarc->update($updateData);

        return redirect()->route('hirarc.edit', $hirarc)->withFragment('section-g');
    }

    public function saveDraft(Hirarc $hirarc)
    {
        $this->authorize('update', $hirarc);
        abort_if($hirarc->status === 'approved', 403);

        $hirarc->update(['status' => 'draft']);

        return back()->with('success', 'Draft saved successfully.');
    }

    public function submitForApproval(Hirarc $hirarc)
    {
        $this->authorize('update', $hirarc);

        abort_if(!in_array($hirarc->status, ['draft', 'rejected']), 403);

        $errors = $hirarc->submissionErrors();
        if (!$hirarc->assessor_registration_no)
            $errors[] = 'Competent Person Registration No (DOSH) not provided';

        if (!empty($errors)) {
            return redirect()
                ->route('hirarc.edit', $hirarc)
                ->with('submit_errors', $errors)
                ->withFragment('section-a');
        }

        $hirarc->update([
            'overall_risk_profile' => $hirarc->highestRiskLevel(),
            'status'               => 'pending',
            'submitted_at'         => now(),
        ]);

        return redirect()
            ->route('hirarc.show', $hirarc)
            ->with('success', 'HIRARC submitted for approval.');
    }

    public function requestDelete(Request $request, Hirarc $hirarc)
    {
        $this->authorize('requestDelete', $hirarc);

        if ($hirarc->deleteRequests()->where('status', 'pending')->exists()) {
            return back()->with('error', 'Delete request already submitted.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|min:3',
        ]);

        HirarcDeleteRequest::create([
            'hirarc_id'      => $hirarc->id,
            'requested_by' => auth()->id(),
            'reason'       => $validated['reason'],
            'status'       => 'pending',
        ]);

        return redirect()
            ->route('hirarc.index')
            ->with('success', 'Delete request submitted and sent to admin.');
    }



    public function downloadPdf(Hirarc $hirarc)
    {
        $this->authorize('view', $hirarc);

        abort_if(!in_array($hirarc->status, ['pending', 'approved']), 403);

        if ($hirarc->isUploaded() && $hirarc->uploaded_pdf_path) {
            return Storage::download(
                $hirarc->uploaded_pdf_path,
                'HIRARC_' . str_replace(' ', '_', $hirarc->company_name) . '.pdf'
            );
        }

        $hirarc->load([
            'workUnits',
            'chemicals',
            'exposures.chemical',
            'exposures.riskEvaluation',
            'recommendations',
        ]);

        $pdf = Pdf::loadView('hirarc.pdf', compact('hirarc'))
            ->setPaper('a4', 'portrait');

        return $pdf->download(
            'HIRARC_' .
            str_replace(' ', '_', $hirarc->company_name) .
            '_' .
            optional($hirarc->assessment_date)->format('Ymd') .
            '.pdf'
        );
    }

    public function showUploaded(Hirarc $hirarc)
    {
        $this->authorize('view', $hirarc);

        abort_if(!$hirarc->isUploaded(), 404);

        return view('hirarc.show-uploaded', compact('hirarc'));
    }
}

