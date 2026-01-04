<?php

namespace App\Http\Controllers;

use App\Models\Chra;
use App\Models\ChraRecommendation;
use Illuminate\Http\Request;

class ChraRecommendationController extends Controller
{
    public function store(Request $request, Chra $chra)
    {
        abort_if($chra->isLocked(), 403);

        $data = $request->validate([
            'category'        => 'required|string',
            'action_priority' => 'required|string',
            'recommendation'  => 'required|string',
        ]);

        $chra->recommendations()->create($data);

        return back()->withFragment('section-f');
    }

    public function destroy(ChraRecommendation $rec)
    {
        abort_if($rec->chra->isLocked(), 403);

        $rec->delete();

        return back()->withFragment('section-f');
    }
}
