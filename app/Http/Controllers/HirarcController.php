<?php

namespace App\Http\Controllers;

use App\Models\Hirarc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HirarcController extends Controller
{
    public function index(Request $request)
    {
        $query = Hirarc::where('user_id', auth()->id());

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
            'company_name'      => 'required|string',
            'company_address'   => 'required|string',
            'assessment_type'   => 'required|string',
            'assessment_date'   => 'required|date',
            'assessment_scope'  => 'nullable|string',
        ]);

        $hirarc = Hirarc::create([
            ...$validated,
            'assessor_name' => Auth::user()->name,
            'user_id'       => Auth::id(),
            'status'        => 'draft',
        ]);

        return redirect()->route('hirarc.edit', $hirarc);
    }

    public function edit(Hirarc $hirarc)
    {
        abort_unless($hirarc->isOwnedBy(auth()->id()), 403);

        return view('hirarc.edit', compact('hirarc'));
    }
}
