<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Chra extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'company_address',
        'assessor_name',
        'assessor_registration_no',
        'assessment_date',
        'completion_date',

        'assessment_objective',
        'assessment_type',
        'assessment_location',

        'process_description',
        'work_activities',
        'chemical_usage_areas',

        'overall_risk_profile',
        'assessor_conclusion',
        'implementation_timeframe',

        'status',
        'admin_reason',
        'approved_by',
        'approved_at',
        'submitted_at',

        'user_id',
        'uploaded_pdf_path',
        'source',


    ];

    protected $casts = [
        'assessment_date' => 'date',
        'completion_date' => 'date',
        'approved_at'     => 'datetime',
        'submitted_at'    => 'datetime',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    /* =========================
        RELATIONSHIPS
    ========================= */

    public function workUnits()
    {
        return $this->hasMany(ChraWorkUnit::class);
    }

    public function chemicals()
    {
        return $this->hasMany(ChraChemical::class);
    }

    public function recommendations()
    {
        return $this->hasMany(ChraRecommendation::class);
    }

    public function deleteRequests()
    {
        return $this->hasMany(
            \App\Models\ChraDeleteRequest::class,
            'chra_id',   // foreign key
            'id'         // local key
        );
    }

    public function exposures()
    {
        return $this->hasMany(ChraExposure::class);
    }

    public function riskEvaluations()
    {
        return $this->hasManyThrough(
            ChraRiskEvaluation::class,
            ChraExposure::class,
            'chra_id',
            'chra_exposure_id'
        );
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /* =========================
        STATE HELPERS
    ========================= */

    public function isLocked(): bool
    {
        return in_array($this->status, ['pending', 'approved']);
    }

    /* =========================
        RISK HELPERS
    ========================= */

    public function highestRiskLevel(): ?string
    {
        $exposures = $this->exposures()->with('riskEvaluation')->get();

        if ($exposures->isEmpty()) return null;

        if ($exposures->contains(fn ($e) =>
            optional($e->riskEvaluation)->risk_level === 'high'
        )) return 'High';

        if ($exposures->contains(fn ($e) =>
            optional($e->riskEvaluation)->risk_level === 'moderate'
        )) return 'Moderate';

        return 'Low';
    }

    public function recommendedActionPriority(): ?string
    {
        $aps = $this->riskEvaluations()->pluck('action_priority')->unique();

        if ($aps->contains('AP-1')) return 'AP-1';
        if ($aps->contains('AP-2')) return 'AP-2';
        if ($aps->contains('AP-3')) return 'AP-3';

        return null;
    }

    /* =========================
        SUBMISSION
    ========================= */

    public function submissionErrors(): array
    {
        $errors = [];

        if (!$this->assessment_objective) $errors[] = 'Section A incomplete';
        if (!$this->process_description || !$this->work_activities || !$this->chemical_usage_areas)
            $errors[] = 'Section B incomplete';
        if ($this->workUnits()->count() === 0) $errors[] = 'Section C incomplete';
        if ($this->chemicals()->count() === 0) $errors[] = 'Section D incomplete';
        if ($this->exposures()->count() === 0) $errors[] = 'Section E incomplete';
        if ($this->recommendations()->count() === 0) $errors[] = 'Section F incomplete';
        if (!$this->assessor_conclusion) $errors[] = 'Section G incomplete';

        return $errors;
    }

    public function isReadyForSubmission(): bool
    {
        return empty($this->submissionErrors());
    }

    /* =========================
        TIMELINE (SAFE)
    ========================= */

    public function timeline(): array
    {
        $events = [];

        $push = function ($label, $date, $by, $type, $note = null) use (&$events) {
            if (!$date) return;

            $events[] = [
                'label' => $label,
                'date'  => $date instanceof Carbon ? $date : Carbon::parse($date),
                'by'    => $by,
                'note'  => $note,
                'type'  => $type,
            ];
        };

        $push('CHRA created', $this->created_at, $this->assessor_name, 'created');

        if ($this->submitted_at) {
            $push('Submitted for approval', $this->submitted_at, $this->assessor_name, 'submitted');
        }

        if ($this->status === 'rejected') {
            $push('Rejected by admin', $this->updated_at, 'Admin', 'rejected', $this->admin_reason);
        }

        if ($this->status === 'approved') {
            $push('Approved by admin', $this->approved_at, optional($this->approver)->name ?? 'Admin', 'approved');
        }

        foreach ($this->deleteRequests as $req) {
            $push('Delete requested', $req->created_at, optional($req->requester)->name ?? 'Assessor', 'delete-request', $req->reason);

            if ($req->status !== 'pending' && $req->reviewed_at) {
                $push(
                    $req->status === 'approved' ? 'Delete approved' : 'Delete rejected',
                    $req->reviewed_at,
                    optional($req->reviewer)->name ?? 'Admin',
                    'delete-' . $req->status,
                    $req->admin_remark
                );
            }
        }

        return collect($events)
            ->sortBy('date')
            ->map(fn ($e) => [
                ...$e,
                'date_formatted' => $e['date']->format('d M Y · H:i'),
            ])
            ->values()
            ->toArray();
    }

    public function isSystem(): bool
    {
        return $this->source === 'system';
    }

    public function isUploaded(): bool
    {
        return $this->source === 'uploaded';
    }

    public function hasPendingDeleteRequest(): bool
    {
        return $this->deleteRequests()
            ->where('status', 'pending')
            ->exists();
    }

    public function canEdit(): bool
    {
        if ($this->isLocked()) {
            return false;
        }

        if ($this->hasPendingDeleteRequest()) {
            return false;
        }

        return true;
    }


}
