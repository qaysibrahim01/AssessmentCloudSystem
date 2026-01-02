<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChraDeleteRequest;
use App\Models\AdminDeletedChra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminChraDeleteController extends Controller
{
    public function approve(Request $request, ChraDeleteRequest $deleteRequest)
    {
        $this->authorize('review', \App\Models\Chra::class);

        abort_if($deleteRequest->status !== 'pending', 403);

        $validated = $request->validate([
            'admin_remark' => 'required|string|min:5',
        ]);

        DB::transaction(function () use ($deleteRequest, $validated) {

            $chra = $deleteRequest->chra;

            AdminDeletedChra::create([
                'chra_id'      => $chra->id,
                'company_name' => $chra->company_name,
                'requested_by' => $deleteRequest->requested_by,
                'deleted_by'   => Auth::id(),
                'reason'       => $validated['admin_remark'],
                'deleted_at'   => now(),
            ]);

            $deleteRequest->update([
                'status'       => 'approved',
                'reviewed_by'  => Auth::id(),
                'reviewed_at'  => now(),
                'admin_remark' => $validated['admin_remark'],
            ]);

            $chra->delete();
        });

        return redirect()
            ->route('admin.chra.index', ['view' => 'deleted'])
            ->with('success', 'CHRA deleted and recorded permanently.');
    }

    public function reject(Request $request, ChraDeleteRequest $deleteRequest)
    {
        $this->authorize('review', \App\Models\Chra::class);

        abort_if($deleteRequest->status !== 'pending', 403);

        $validated = $request->validate([
            'admin_remark' => 'required|string|min:5',
        ]);

        $deleteRequest->update([
            'status'       => 'rejected',
            'reviewed_by'  => Auth::id(),
            'reviewed_at'  => now(),
            'admin_remark' => $validated['admin_remark'],
        ]);

        return back()->with('success', 'Delete request rejected.');
    }
}
