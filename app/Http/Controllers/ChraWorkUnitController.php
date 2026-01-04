<?php

namespace App\Http\Controllers;

use App\Models\Chra;
use App\Models\ChraWorkUnit;
use Illuminate\Http\Request;

class ChraWorkUnitController extends Controller
{
    public function store(Request $request, Chra $chra)
    {
        abort_if($chra->isLocked(), 403);

        $data = $request->validate([
            'name'      => 'required|string',
            'work_area' => 'required|string',
        ]);

        $chra->workUnits()->create($data);

        return back()->withFragment('section-c');
    }

    public function destroy(ChraWorkUnit $unit)
    {
        abort_if($unit->chra->isLocked(), 403);

        $unit->delete();

        return back()->withFragment('section-c');
    }
}
