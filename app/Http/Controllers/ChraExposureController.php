<?php

namespace App\Http\Controllers;

use App\Models\Chra;
use App\Models\ChraExposure;
use Illuminate\Http\Request;

class ChraExposureController extends Controller
{
    public function store(Request $request, Chra $chra)
    {
        abort_if($chra->isLocked(), 403);

        $data = $request->validate([
            'chra_work_unit_id' => 'required|exists:chra_work_units,id',
            'chra_chemical_id'  => 'required|exists:chra_chemicals,id',
            'exposure_route'    => 'required|string',
            'task'              => 'nullable|string',
            'exposure_frequency'=> 'nullable|string',
            'exposure_duration' => 'nullable|string',
            'control_adequacy'  => 'nullable|string',
            'exposure_rating'   => 'required|integer|min:1|max:5',
            'existing_control'  => 'nullable|string',
        ]);

        // 🚫 Prevent duplicates first
        if ($chra->exposures()
            ->where('chra_work_unit_id', $data['chra_work_unit_id'])
            ->where('chra_chemical_id', $data['chra_chemical_id'])
            ->where('exposure_route', $data['exposure_route'])
            ->exists()
        ) {
            return back()->with('error', 'This exposure assessment already exists.');
        }

        $exposure = $chra->exposures()->create($data);

        $hazardRating = $this->deriveHazardRating($exposure->chemical);
        $riskScore = $hazardRating * $data['exposure_rating'];

        $riskLevel = match (true) {
            $riskScore >= 15 => 'high',
            $riskScore >= 6  => 'moderate',
            default          => 'low',
        };

        $actionPriority = match ($riskLevel) {
            'high'     => 'AP-1',
            'moderate' => 'AP-2',
            default    => 'AP-3',
        };

        $exposure->riskEvaluation()->create([
            'hazard_rating'   => $hazardRating,
            'exposure_rating' => $data['exposure_rating'],
            'risk_score'      => $riskScore,
            'risk_level'      => $riskLevel,
            'action_priority' => $actionPriority,
        ]);

        return back()->withFragment('section-e');
    }

    private function deriveHazardRating($chemical): int
    {
        $hCode = $chemical->h_code ?? '';

        return match (true) {
            str_contains($hCode, 'H330') || str_contains($hCode, 'H314') => 5,
            str_contains($hCode, 'H331') || str_contains($hCode, 'H311') => 4,
            str_contains($hCode, 'H315') || str_contains($hCode, 'H319') => 2,
            default => 1,
        };
    }



}
