<?php

namespace App\Http\Controllers;

use App\Models\Nra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NraController extends Controller
{
    public function index(Request $request)
    {
        $query = Nra::where('user_id', auth()->id());

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

        $nras = $query->get();

        return view('nra.index', compact('nras'));
    }

    public function create()
    {
        return view('nra.create');
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

        $nra = Nra::create([
            ...$validated,
            'assessor_name' => Auth::user()->name,
            'user_id'       => Auth::id(),
            'status'        => 'draft',
        ]);

        return redirect()->route('nra.edit', $nra);
    }

    public function edit(Nra $nra)
    {
        abort_unless($nra->isOwnedBy(auth()->id()), 403);

        return view('nra.edit', compact('nra'));
    }
}
