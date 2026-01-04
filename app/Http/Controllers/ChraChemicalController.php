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
            'h_code'        => 'nullable|string',
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
