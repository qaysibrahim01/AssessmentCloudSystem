<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminUploadedChraController extends Controller
{
    public function create()
    {
        return view('admin.chra.uploaded-create');
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

        $filename = 'UPLOADED_CHRA_' . now()->format('Ymd_His') . '.pdf';

        $path = $request->file('pdf')->storeAs(
            'chra_uploads',
            $filename
        );

        Chra::create([
            'company_name'      => $validated['company_name'],
            'company_address'   => $validated['company_address'],
            'assessment_date'   => $validated['assessment_date'],
            'assessor_name'     => 'Admin Upload',
            'assessment_objective' => $validated['summary'],
            'status'            => 'approved',
            'approved_by'       => Auth::id(),
            'approved_at'       => now(),
            'uploaded_pdf_path' => $path,
            'source'            => 'uploaded',
            'user_id'           => Auth::id(),
        ]);

        return redirect()
            ->route('admin.chra.index')
            ->with('success', 'Uploaded CHRA report created successfully.');
    }
}
