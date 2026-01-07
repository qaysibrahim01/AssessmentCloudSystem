<?php

namespace App\Http\Controllers;

use App\Models\Chra;
use Illuminate\Http\Request;

class ChraAutosaveController extends Controller
{
    public function store(Request $request, Chra $chra)
    {
        abort_if($chra->isLocked(), 403);

        $chra->update(
            [
                ...$request->only([
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
                    'assessor_conclusion',
                    'implementation_timeframe',
                    'business_nature',
                    'assisted_by',
                    'dosh_ref_num',
                ]),
                'specified_objectives' => collect($request->input('specified_objectives', []))
                    ->map(fn ($v) => trim($v))
                    ->filter()
                    ->take(5)
                    ->values()
                    ->all(),
            ]
        );


        return response()->json(['saved' => true]);
    }
}
