<?php

namespace App\Http\Controllers\Committee;

use App\Http\Controllers\Controller;
use App\Models\Chra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommitteeChraController extends Controller
{
    public function index(Request $request)
    {
        $query = Chra::where('status', 'approved');

        // Sorting
        $sortBy = $request->get('sort_by', 'approved_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if (
            in_array($sortBy, ['approved_at', 'company_name']) &&
            in_array($sortOrder, ['asc', 'desc'])
        ) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $chras = $query->get();

        return view('committee.chra.index', compact('chras'));
    }

    public function show(Chra $chra)
    {
        $this->authorize('view', $chra);

        abort_if($chra->status !== 'approved', 403);

        $chra->load([
            'workUnits',
            'chemicals',
            'exposures.chemical',
            'exposures.riskEvaluation',
            'recommendations',
        ]);

        return view('committee.chra.show', compact('chra'));
    }

    public function downloadPdf(Chra $chra)
    {
        $this->authorize('view', $chra);

        abort_if($chra->status !== 'approved', 403);
        abort_if(!$chra->uploaded_pdf_path, 404);

        return Storage::download(
            $chra->uploaded_pdf_path,
            'CHRA_' .
            str_replace(' ', '_', $chra->company_name) .
            '.pdf'
        );
    }
}
