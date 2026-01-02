<?php

namespace App\Http\Controllers;

use App\Models\ChraDeleteRequest;
use Illuminate\Support\Facades\Auth;

class ChraDeleteHistoryController extends Controller
{
    public function index()
    {
        $deleteRequests = ChraDeleteRequest::with(['chra', 'reviewer'])
            ->where('requested_by', Auth::id())
            ->latest()
            ->get();

        return view('chra.delete-history', compact('deleteRequests'));
    }
}
