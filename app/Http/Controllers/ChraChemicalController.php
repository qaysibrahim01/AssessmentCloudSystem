<?php

namespace App\Http\Controllers;

use App\Models\Chra;
use App\Models\ChraChemical;
use Illuminate\Http\Request;

class ChraChemicalController extends Controller
{
    public function store(Request $request, Chra $chra)
    {
        abort_if($chra->isLocked(), 403);

        $data = $request->validate([
            'chemical_name' => 'required|string',
            'chra_work_unit_id' => 'nullable|exists:chra_work_units,id',
            'health_hazard' => 'nullable|string',
            'h_code'        => 'nullable|string',
            'hazard_rating' => 'nullable|integer|min:1|max:5',
            'route_dermal'  => 'nullable|string|max:3',
            'route_ingestion' => 'nullable|string|max:3',
        ]);

        $chra->chemicals()->create($data);

        return back()->withFragment('section-d');
    }

    public function destroy(ChraChemical $chemical)
    {
        abort_if($chemical->chra->isLocked(), 403);

        $chemical->delete();

        return back()->withFragment('section-d');
    }
}
