<?php

namespace App\Http\Controllers\Committee;

use App\Http\Controllers\Controller;
use App\Models\Hirarc;

class CommitteeHirarcController extends Controller
{
    public function index()
    {
        $query = Hirarc::where('status', 'approved');

        if (auth()->user()->role === 'committee') {
            $company = auth()->user()->company_name;
            $query->where('company_name', $company ?: '__none__');
        }

        $sortBy = request()->get('sort_by', 'approved_at');
        $sortOrder = request()->get('sort_order', 'desc');

        if (
            in_array($sortBy, ['approved_at', 'company_name']) &&
            in_array($sortOrder, ['asc', 'desc'])
        ) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $hirarcs = $query->get();

        return view('committee.hirarc.index', compact('hirarcs'));
    }

    public function show(Hirarc $hirarc)
    {
        $this->authorize('view', $hirarc);

        abort_if($hirarc->status !== 'approved', 403);

        $hirarc->load([
            'workUnits',
            'chemicals',
            'exposures.chemical',
            'exposures.riskEvaluation',
            'recommendations',
        ]);

        return view('committee.hirarc.show', compact('hirarc'));
    }

    public function downloadPdf(Hirarc $hirarc)
    {
        $this->authorize('view', $hirarc);

        abort_if($hirarc->status !== 'approved', 403);
        abort_if(!$hirarc->uploaded_pdf_path, 404);

        return \Illuminate\Support\Facades\Storage::download(
            $hirarc->uploaded_pdf_path,
            'HIRARC_' . str_replace(' ', '_', $hirarc->company_name) . '.pdf'
        );
    }

    public function showUploaded(Hirarc $hirarc)
    {
        abort_if(
            auth()->user()->role === 'committee'
            && strcasecmp($hirarc->company_name ?? '', auth()->user()->company_name ?? '') !== 0,
            403
        );
        abort_if(!$hirarc->isUploaded(), 404);

        return view('committee.hirarc.show-uploaded', compact('hirarc'));
    }
}
