<?php

namespace App\Http\Controllers\Committee;

use App\Http\Controllers\Controller;
use App\Models\Nra;

class CommitteeNraController extends Controller
{
    public function index()
    {
        $query = Nra::where('status', 'approved');

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

        $nras = $query->get();

        return view('committee.nra.index', compact('nras'));
    }

    public function show(Nra $nra)
    {
        $this->authorize('view', $nra);

        abort_if($nra->status !== 'approved', 403);

        $nra->load([
            'workUnits',
            'chemicals',
            'exposures.chemical',
            'exposures.riskEvaluation',
            'recommendations',
        ]);

        return view('committee.nra.show', compact('nra'));
    }

    public function downloadPdf(Nra $nra)
    {
        $this->authorize('view', $nra);

        abort_if($nra->status !== 'approved', 403);
        abort_if(!$nra->uploaded_pdf_path, 404);

        return \Illuminate\Support\Facades\Storage::download(
            $nra->uploaded_pdf_path,
            'NRA_' . str_replace(' ', '_', $nra->company_name) . '.pdf'
        );
    }

    public function showUploaded(Nra $nra)
    {
        abort_if(
            auth()->user()->role === 'committee'
            && strcasecmp($nra->company_name ?? '', auth()->user()->company_name ?? '') !== 0,
            403
        );
        abort_if(!$nra->isUploaded(), 404);

        return view('committee.nra.show-uploaded', compact('nra'));
    }
}
