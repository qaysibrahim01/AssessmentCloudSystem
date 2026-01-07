<?php

namespace App\Policies;

use App\Models\Nra;
use App\Models\User;

class NraPolicy
{
    public function view(User $user, Nra $nra): bool
    {
        if ($user->role === 'admin') return true;

        if ($user->role === 'committee') {
            return $nra->status === 'approved'
                && $user->company_name
                && strcasecmp($nra->company_name ?? '', $user->company_name) === 0;
        }

        if ($user->role === 'assessor') {
            // Own NRA
            if ($nra->user_id === $user->id) {
                return true;
            }

            // Admin-uploaded NRA (read-only)
            if ($nra->isUploaded()) {
                return true;
            }

            return false;
        }

        return false;
    }

    public function update(User $user, Nra $nra): bool
    {
        return $user->role === 'assessor'
            && $nra->user_id === $user->id
            && in_array($nra->status, ['draft', 'rejected']);
    }

    public function review(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function requestDelete(User $user, Nra $nra): bool
    {
        return $user->id === $nra->user_id
            && in_array($nra->status, ['draft', 'rejected']);
    }
}
