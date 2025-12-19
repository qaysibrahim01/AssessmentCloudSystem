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

    /**
     * CREATE = DRAFT
     */
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
        return view('chra.show', compact('chra'));
    }

    public function edit(Chra $chra)
    {
        return view('chra.edit', compact('chra'));
    }

    /* ===========================
        SECTION C – WORK UNITS
    ============================ */
    public function addWorkUnit(Request $request, Chra $chra)
    {
        abort_if($chra->isLocked(), 403);

        $validated = $request->validate([
            'name' => 'required|string',
            'work_area' => 'required|string',
        ]);

        $chra->workUnits()->create($validated);

        return redirect()->route('chra.edit', $chra)->withFragment('section-c');
    }

    public function deleteWorkUnit(ChraWorkUnit $unit)
    {
        abort_if($unit->chra->isLocked(), 403);

        $chra = $unit->chra;
        $unit->delete();

        return redirect()->route('chra.edit', $chra)->withFragment('section-c');
    }

    /* ===========================
        SECTION D – CHEMICALS
    ============================ */
    public function addChemical(Request $request, Chra $chra)
    {
        abort_if($chra->isLocked(), 403);

        $validated = $request->validate([
            'chemical_name' => 'required|string',
            'h_code' => 'nullable|string',
        ]);

        $chra->chemicals()->create($validated);

        return redirect()->route('chra.edit', $chra)->withFragment('section-d');
    }

    public function deleteChemical(ChraChemical $chemical)
    {
        abort_if($chemical->chra->isLocked(), 403);

        $chra = $chemical->chra;
        $chemical->delete();

        return redirect()->route('chra.edit', $chra)->withFragment('section-d');
    }

    /* ===========================
        SECTION F – RECOMMENDATIONS
    ============================ */
    public function addRecommendation(Request $request, Chra $chra)
    {
        abort_if($chra->isLocked(), 403);

        $validated = $request->validate([
            'category'        => 'required|in:TC,OC,PPE,ERP,Monitoring',
            'action_priority' => 'required|in:AP-1,AP-2,AP-3',
            'recommendation'  => 'required|string',
        ]);

        $chra->recommendations()->create($validated);

        return redirect()
            ->route('chra.edit', $chra)
            ->withFragment('section-f')
            ->with('success', 'Recommendation added successfully.');
    }

    public function deleteRecommendation(ChraRecommendation $recommendation)
    {
        abort_if($recommendation->chra->isLocked(), 403);

        $chra = $recommendation->chra;
        $recommendation->delete();

        return redirect()->route('chra.edit', $chra)->withFragment('section-f');
    }

    /* ===========================
        SECTIONS A + B + G
    ============================ */
    public function updateSections(Request $request, Chra $chra)
    {
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

    public function autoSave(Request $request, Chra $chra)
    {
        abort_if($chra->isLocked(), 403);

        $chra->update(
            $request->only([
                'assessment_objective',
                'process_description',
                'work_activities',
                'chemical_usage_areas',
                'assessor_conclusion',
                'implementation_timeframe',
            ])
        );

        return response()->json(['status' => 'saved']);
    }


    /* ===========================
        GLOBAL ACTIONS
    ============================ */
    public function saveDraft(Chra $chra)
    {
        abort_if($chra->status === 'approved', 403);

        $chra->update([
            'status' => 'draft',
        ]);

        return redirect()
            ->route('chra.show', $chra)
            ->with('success', 'Draft saved successfully.');
    }

    public function submitForApproval(Chra $chra)
    {
        abort_if(!in_array($chra->status, ['draft', 'rejected']), 403);

        $errors = $chra->submissionErrors();

        if (!empty($errors)) {
            return redirect()
                ->route('chra.edit', $chra)
                ->with('submit_errors', $errors)
                ->withFragment('section-a');
        }

        // 🔒 Sync calculated summary before submission
        $chra->update([
            'overall_risk_profile' => $chra->highestRiskLevel(),
            'status'               => 'pending',
            'submitted_at'         => now(),
        ]);

        return redirect()
            ->route('chra.show', $chra)
            ->with('success', 'CHRA submitted for approval.');
    }


    /* ===========================
        DELETE REQUEST
    ============================ */
    public function requestDelete(Request $request, Chra $chra)
    {
        abort_if($chra->user_id !== Auth::id(), 403);

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

    public function storeExposure(Request $request, Chra $chra)
    {
        abort_if($chra->isLocked(), 403);

        $validated = $request->validate([
            'chra_work_unit_id' => 'required|exists:chra_work_units,id',
            'chra_chemical_id'  => 'required|exists:chra_chemicals,id',
            'exposure_route'    => 'required|in:inhalation,dermal,ingestion',
            'task'              => 'nullable|string',
            'exposure_frequency'=> 'nullable|string',
            'exposure_duration' => 'nullable|string',
            'existing_control'  => 'nullable|string',
            'control_adequacy'  => 'nullable|in:adequate,inadequate',
            'exposure_rating'   => 'required|integer|min:1|max:5',
        ]);

        $exposure = ChraExposure::create([
            'chra_id' => $chra->id,
            ...$validated,
        ]);

        // Auto-calculate risk (Form D logic)
        $hazard = $exposure->chemical->hazard_rating ?? 1;
        $score  = $hazard * $validated['exposure_rating'];

        if ($score >= 15) {
            $level = 'high'; $ap = 'AP-1';
        } elseif ($score >= 5) {
            $level = 'moderate'; $ap = 'AP-2';
        } else {
            $level = 'low'; $ap = 'AP-3';
        }

        $exposure->riskEvaluation()->create([
            'hazard_rating'   => $hazard,
            'exposure_rating' => $validated['exposure_rating'],
            'risk_score'      => $score,
            'risk_level'      => $level,
            'action_priority' => $ap,
        ]);

        return redirect()
            ->route('chra.edit', $chra)
            ->withFragment('section-e')
            ->with('success', 'Exposure assessment saved (Form A–D).');
    }


    public function storeRiskEvaluation(Request $request, ChraExposure $exposure)
    {
        abort_if($exposure->chra->isLocked(), 403);

        $validated = $request->validate([
            'exposure_rating' => 'required|integer|min:1|max:5',
        ]);

        // Hazard rating comes from chemical
        $hazardRating = $exposure->chemical->hazard_rating ?? 1;

        $riskScore = $validated['exposure_rating'] * $hazardRating;

        // Determine risk level
        if ($riskScore >= 15) {
            $riskLevel = 'high';
            $actionPriority = 'AP-1';
        } elseif ($riskScore >= 5) {
            $riskLevel = 'moderate';
            $actionPriority = 'AP-2';
        } else {
            $riskLevel = 'low';
            $actionPriority = 'AP-3';
        }

        ChraRiskEvaluation::updateOrCreate(
            ['chra_exposure_id' => $exposure->id],
            [
                'exposure_rating' => $validated['exposure_rating'],
                'hazard_rating'   => $hazardRating,
                'risk_score'      => $riskScore,
                'risk_level'      => $riskLevel,
                'action_priority' => $actionPriority,
            ]
        );

        return redirect()
            ->route('chra.edit', $exposure->chra)
            ->withFragment('section-e')
            ->with('success', 'Risk evaluation calculated and saved.');
    }

    public function downloadPdf(Chra $chra)
    {
        // Allow assessor (owner) OR admin
        abort_if(
            auth()->id() !== $chra->user_id &&
            auth()->user()->role !== 'admin',
            403
        );

        // Optional: restrict to submitted+
        abort_if(
            !in_array($chra->status, ['pending', 'approved']),
            403
        );

        // Eager load EVERYTHING used in PDF
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
