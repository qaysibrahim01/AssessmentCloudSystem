<?php

namespace App\Policies;

use App\Models\Chra;
use App\Models\User;

class ChraPolicy
{
    /**
     * Admin can view all
     * Committee can view approved only
     * Assessor can view own only
     */
    public function view(User $user, Chra $chra): bool
    {
        // Admin sees everything
        if ($user->role === 'admin') {
            return true;
        }

        // Committee: APPROVED only (system OR uploaded)
        if ($user->role === 'committee') {
            return $chra->status === 'approved';
        }

        // Assessor
        if ($user->role === 'assessor') {

            // Own CHRA
            if ($chra->user_id === $user->id) {
                return true;
            }

            // Admin-uploaded CHRA (read-only)
            if ($chra->isUploaded()) {
                return true;
            }
        }

        return false;
    }



    /**
     * Only assessor can edit own CHRA in draft / rejected
     */
    public function update(User $user, Chra $chra): bool
    {
        return $user->role === 'assessor'
            && $chra->user_id === $user->id
            && in_array($chra->status, ['draft', 'rejected']);
    }

    /**
     * Only admin can approve/reject
     */
    public function review(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Only assessor can request delete
     */
    public function requestDelete(User $user, Chra $chra): bool
    {
        return $user->id === $chra->user_id
            && in_array($chra->status, ['draft', 'rejected']);
    }

}
