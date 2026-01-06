<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\AccountApprovedMail;

class AdminUserApprovalController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'users');
        $search = $request->get('search');

        $filter = function ($query) use ($search) {
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%");
                });
            }
        };

        $users = User::query()
            ->where('is_approved', true)
            ->tap($filter)
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'users_page')
            ->withQueryString();

        $pending = User::query()
            ->where(function ($q) {
                $q->where('is_approved', false)
                  ->orWhereNull('is_approved');
            })
            ->tap($filter)
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'pending_page')
            ->withQueryString();

        return view('admin.users.index', compact('users', 'pending', 'tab', 'search'));
    }

    public function approve(User $user)
    {
        $wasApproved = $user->is_approved;
        $emailAlreadySent = !is_null($user->approval_email_sent_at);

        $user->forceFill([
            'approval_status' => 'approved',
            'is_approved'     => true,
            'approved_at'     => $user->approved_at ?? now(),
        ])->save();

        // Send email once when first approved
        if (!$wasApproved && !$emailAlreadySent) {
            try {
                Mail::to($user->email)->send(new AccountApprovedMail($user));
                $user->forceFill(['approval_email_sent_at' => now()])->save();
            } catch (\Throwable $e) {
                Log::error('Failed to send approval email', [
                    'user_id' => $user->id,
                    'email'   => $user->email,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        return redirect()
            ->route('admin.users.index', ['tab' => 'requests'])
            ->with('success', "{$user->name} has been approved.");
    }

    public function reject(User $user)
    {
        $user->update([
            'approval_status' => 'rejected',
            'is_approved'     => false,
            'approved_at'     => null,
            'approval_email_sent_at' => null,
        ]);

        return redirect()
            ->route('admin.users.index', ['tab' => 'requests'])
            ->with('success', "{$user->name} has been rejected.");
    }

    public function storeAdmin(Request $request)
    {
        $masterAdminEmail = 'admin@example.com';

        abort_unless(
            $request->user() && $request->user()->email === $masterAdminEmail,
            403,
            'Only the master admin can create new admins.'
        );

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create([
            'name'            => $data['name'],
            'email'           => $data['email'],
            'password'        => Hash::make($data['password']),
            'role'            => 'admin',
            'company_name'    => null,
            'approval_status' => 'approved',
            'approved_at'     => now(),
        ]);

        return back()->with('success', 'New admin created and approved.');
    }
}
