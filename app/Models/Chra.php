<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

        // Section A
        'assessment_objective',
        'assessment_type',
        'assessment_location',

        // Section B
        'process_description',
        'work_activities',
        'chemical_usage_areas',

        // Section G
        'overall_risk_profile',
        'assessor_conclusion',
        'implementation_timeframe',

        // Status & admin
        'status',
        'admin_reason',
        'approved_by',
        'approved_at',

        'user_id',
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
        return $this->hasMany(ChraDeleteRequest::class);
    }

    public function exposures()
    {
        return $this->hasMany(ChraExposure::class);
    }

    /**
     * CHRA → Risk Evaluations (THROUGH exposures)
     */
    public function riskEvaluations()
    {
        return $this->hasManyThrough(
            ChraRiskEvaluation::class,
            ChraExposure::class,
            'chra_id',          // FK on chra_exposures
            'chra_exposure_id', // FK on chra_risk_evaluations
            'id',               // PK on chras
            'id'                // PK on chra_exposures
        );
    }

    /* =========================
        STATE HELPERS
    ========================= */

    public function isLocked(): bool
    {
        return in_array($this->status, ['pending', 'approved']);
    }

    public function canEdit(): bool
    {
        return ! $this->isLocked();
    }

    public function hasDeleteRequestPending(): bool
    {
        return $this->deleteRequests()
            ->whereNull('resolved_at')
            ->exists();
    }

    /* =========================
        RISK HELPERS
    ========================= */

    public function highestRiskLevel(): ?string
    {
        $exposures = $this->exposures()->with('riskEvaluation')->get();

        if ($exposures->isEmpty()) {
            return null;
        }

        if ($exposures->contains(fn ($e) =>
            optional($e->riskEvaluation)->risk_level === 'high'
        )) {
            return 'High';
        }

        if ($exposures->contains(fn ($e) =>
            optional($e->riskEvaluation)->risk_level === 'moderate'
        )) {
            return 'Moderate';
        }

        return 'Low';
    }


    public function recommendedActionPriority(): ?string
    {
        $aps = $this->riskEvaluations()
            ->pluck('action_priority')
            ->unique();

        if ($aps->contains('AP-1')) return 'AP-1';
        if ($aps->contains('AP-2')) return 'AP-2';
        if ($aps->contains('AP-3')) return 'AP-3';

        return null;
    }

    /* =========================
        SUBMISSION VALIDATION
    ========================= */

    public function submissionErrors(): array
    {
        $errors = [];

        if (empty($this->assessment_objective)) {
            $errors[] = 'Section A: Assessment objective is required.';
        }

        if (
            empty($this->process_description) ||
            empty($this->work_activities) ||
            empty($this->chemical_usage_areas)
        ) {
            $errors[] = 'Section B: Process details are incomplete.';
        }

        if ($this->workUnits()->count() === 0) {
            $errors[] = 'Section C: At least one work unit is required.';
        }

        if ($this->chemicals()->count() === 0) {
            $errors[] = 'Section D: At least one chemical is required.';
        }

        if (
            $this->exposures()->count() === 0 ||
            $this->riskEvaluations()->count() === 0
        ) {
            $errors[] = 'Section E: Exposure and risk evaluation incomplete.';
        }

        if ($this->recommendations()->count() === 0) {
            $errors[] = 'Section F: At least one recommendation is required.';
        }

        if (empty($this->assessor_conclusion)) {
            $errors[] = 'Section G: Assessor conclusion is required.';
        }

        return $errors;
    }

    public function isReadyForSubmission(): bool
    {
        return empty($this->submissionErrors());
    }

    public function adminChecklistComplete(): bool
    {
        return
            $this->admin_checked_sections &&
            $this->admin_checked_chemicals &&
            $this->admin_checked_risk &&
            $this->admin_checked_recommendations &&
            $this->admin_checked_conclusion;
    }
}
