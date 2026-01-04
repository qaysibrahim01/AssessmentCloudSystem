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
            $request->only([
                'assessor_registration_no',
                'assessment_objective',
                'process_description',
                'work_activities',
                'chemical_usage_areas',
                'assessor_conclusion',
                'implementation_timeframe',
            ])
        );


        return response()->json(['saved' => true]);
    }
}
