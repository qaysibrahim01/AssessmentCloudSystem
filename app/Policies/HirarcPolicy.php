<?php

namespace App\Policies;

use App\Models\Hirarc;
use App\Models\User;

class HirarcPolicy
{
    public function view(User $user, Hirarc $hirarc): bool
    {
        if ($user->role === 'admin') return true;

        if ($user->role === 'committee') {
            return $hirarc->status === 'approved'
                && $user->company_name
                && strcasecmp($hirarc->company_name ?? '', $user->company_name) === 0;
        }

        if ($user->role === 'assessor') {
            // Own HIRARC
            if ($hirarc->user_id === $user->id) {
                return true;
            }

            // Admin-uploaded HIRARC (read-only)
            if ($hirarc->isUploaded()) {
                return true;
            }

            return false;
        }

        return false;
    }

    public function update(User $user, Hirarc $hirarc): bool
    {
        return $user->role === 'assessor'
            && $hirarc->user_id === $user->id
            && in_array($hirarc->status, ['draft', 'rejected']);
    }

    public function review(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function requestDelete(User $user, Hirarc $hirarc): bool
    {
        return $user->id === $hirarc->user_id
            && in_array($hirarc->status, ['draft', 'rejected']);
    }
}
