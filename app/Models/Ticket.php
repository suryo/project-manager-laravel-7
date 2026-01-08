<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{


    protected $fillable = [
        'ticket_number',
        'project_id',
        'title',
        'description',
        'type',
        'priority',
        'status',
        'current_stage',
        'requester_id',
        'assigned_to',
        'completed_at',
        'guest_name',
        'guest_email',
        'guest_department',
        'guest_phone',
        'tracking_token',
        'is_public_request'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'ticket_user')
                    ->withPivot(['assigned_at', 'removed_at'])
                    ->withTimestamps();
    }

    public function documents()
    {
        return $this->hasMany(TicketDocument::class);
    }

    public function approvals()
    {
        return $this->hasMany(TicketApproval::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(TicketStatusHistory::class)->latest();
    }

    public function stages()
    {
        return $this->hasMany(TicketStage::class)->orderBy('stage_number');
    }

    // Helper methods
    public static function generateTicketNumber()
    {
        $year = date('Y');
        $lastTicket = self::whereYear('created_at', $year)->latest()->first();
        $number = $lastTicket ? (int)substr($lastTicket->ticket_number, -4) + 1 : 1;
        return 'TKT-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function initializeStages()
    {
        $stages = [
            ['stage_number' => 1, 'stage_name' => 'Penerimaan Permintaan'],
            ['stage_number' => 2, 'stage_name' => 'Analisis Kebutuhan'],
            ['stage_number' => 3, 'stage_name' => 'Perencanaan'],
            ['stage_number' => 4, 'stage_name' => 'Pengembangan'],
            ['stage_number' => 5, 'stage_name' => 'Pengujian'],
            ['stage_number' => 6, 'stage_name' => 'Persetujuan'],
            ['stage_number' => 7, 'stage_name' => 'Serah Terima'],
            ['stage_number' => 8, 'stage_name' => 'Dokumentasi'],
        ];

        foreach ($stages as $stage) {
            $this->stages()->create($stage);
        }

        // Set first stage to in_progress
        $this->stages()->where('stage_number', 1)->update(['status' => 'in_progress', 'started_at' => now()]);
    }

    public function getMandatoryDocuments()
    {
        return ['request_form', 'user_requirements', 'functional_spec', 'project_plan', 'user_manual', 'bast'];
    }

    public function hasAllMandatoryDocuments()
    {
        $mandatory = $this->getMandatoryDocuments();
        $uploaded = $this->documents()
            ->whereIn('document_type', $mandatory)
            ->where('status', 'approved')
            ->pluck('document_type')
            ->toArray();
        return count($uploaded) === count($mandatory);
    }

    public function canProgressToNextStage()
    {
        $currentStage = $this->stages()->where('stage_number', $this->current_stage)->first();
        
        if (!$currentStage || $currentStage->status !== 'completed') {
            return false;
        }

        // Stage-specific requirements
        switch ($this->current_stage) {
            case 1: // Penerimaan - need request_form approved
                return $this->documents()
                    ->where('document_type', 'request_form')
                    ->where('status', 'approved')
                    ->exists();
            
            case 2: // Analisis - need user_requirements approved
                return $this->documents()
                    ->where('document_type', 'user_requirements')
                    ->where('status', 'approved')
                    ->exists();
            
            case 3: // Perencanaan - need functional_spec and project_plan approved
                return $this->documents()
                    ->whereIn('document_type', ['functional_spec', 'project_plan'])
                    ->where('status', 'approved')
                    ->count() === 2;
            
            case 4: // Pengembangan - need uat_report (supporting) if exists
                return true; // Can progress if stage completed
            
            case 5: // Persetujuan - need bast approved
                return $this->documents()
                    ->where('document_type', 'bast')
                    ->where('status', 'approved')
                    ->exists();
            
            case 6: // Dokumentasi - need user_manual approved
                return $this->documents()
                    ->where('document_type', 'user_manual')
                    ->where('status', 'approved')
                    ->exists();
            
            default:
                return true;
        }
    }

    public function progressToNextStage()
    {
        if ($this->current_stage >= 6) {
            return false; // Already at final stage
        }

        if (!$this->canProgressToNextStage()) {
            return false;
        }

        $nextStageNumber = $this->current_stage + 1;
        $nextStage = $this->stages()->where('stage_number', $nextStageNumber)->first();

        if ($nextStage) {
            $nextStage->update([
                'status' => 'in_progress',
                'started_at' => now()
            ]);

            $this->update(['current_stage' => $nextStageNumber]);
            return true;
        }

        return false;
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeRequestedBy($query, $userId)
    {
        return $query->where('requester_id', $userId);
    }

    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }
}
