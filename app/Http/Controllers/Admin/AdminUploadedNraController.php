<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Nra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminUploadedNraController extends Controller
{
    public function create()
    {
        return view('admin.nra.uploaded-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name'    => 'required|string',
            'company_address' => 'required|string',
            'assessment_date' => 'required|date',
            'summary'         => 'required|string',
            'pdf'             => 'required|file|mimes:pdf|max:10240',
        ]);

        $filename = 'UPLOADED_NRA_' . now()->format('Ymd_His') . '.pdf';

        $path = $request->file('pdf')->storeAs(
            'nra_uploads',
            $filename,
            'public'
        );

        Nra::create([
            'company_name'         => $validated['company_name'],
            'company_address'      => $validated['company_address'],
            'assessment_date'      => $validated['assessment_date'],
            'assessor_name'        => 'Admin Upload',
            'assessment_objective' => $validated['summary'],
            'status'               => 'approved',
            'approved_by'          => Auth::id(),
            'approved_at'          => now(),
            'uploaded_pdf_path'    => $path,
            'source'               => 'uploaded',
            'user_id'              => Auth::id(),
        ]);

        return redirect()
            ->route('admin.nra.index')
            ->with('success', 'Uploaded NRA report created successfully.');
    }

    public function destroy(Nra $nra)
    {
        abort_if($nra->source !== 'uploaded', 403);

        if ($nra->uploaded_pdf_path) {
            Storage::delete($nra->uploaded_pdf_path);
        }

        $nra->delete();

        return redirect()
            ->route('admin.nra.index')
            ->with('success', 'Uploaded NRA deleted successfully.');
    }
}
