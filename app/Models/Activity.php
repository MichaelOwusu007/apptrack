<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'activity_type',
        'remarks',
        'activity_date',
        'assigned_to',
        'created_by',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
            'completed_at'  => 'datetime',
        ];
    }

    const PRIORITY_COLORS = [
        'low'      => 'text-emerald-400 bg-emerald-400/10 border-emerald-400/20',
        'medium'   => 'text-amber-400 bg-amber-400/10 border-amber-400/20',
        'high'     => 'text-orange-400 bg-orange-400/10 border-orange-400/20',
        'critical' => 'text-red-400 bg-red-400/10 border-red-400/20',
    ];

    const STATUS_COLORS = [
        'pending'     => 'text-amber-400 bg-amber-400/10 border-amber-400/20',
        'in_progress' => 'text-blue-400 bg-blue-400/10 border-blue-400/20',
        'done'        => 'text-emerald-400 bg-emerald-400/10 border-emerald-400/20',
        'escalated'   => 'text-red-400 bg-red-400/10 border-red-400/20',
    ];

    const ACTIVITY_TYPES = [
        'sms_count'       => 'Daily SMS Count',
        'api_monitor'     => 'API Monitoring',
        'server_uptime'   => 'Server Uptime Check',
        'failed_txn'      => 'Failed Transaction Monitor',
        'db_backup'       => 'Database Backup Verification',
        'error_log'       => 'Error Log Review',
        'network_check'   => 'Network Connectivity Check',
        'security_scan'   => 'Security Scan',
        'other'           => 'Other',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updates()
    {
        return $this->hasMany(ActivityUpdate::class)->orderBy('created_at', 'desc');
    }

    public function latestUpdate()
    {
        return $this->hasOne(ActivityUpdate::class)->orderByDesc('created_at');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getPriorityColorAttribute(): string
    {
        return self::PRIORITY_COLORS[$this->priority] ?? 'text-gray-400';
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'text-gray-400';
    }

    public function getActivityTypeLabelAttribute(): string
    {
        return self::ACTIVITY_TYPES[$this->activity_type] ?? $this->activity_type ?? 'General';
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== 'done'
            && $this->activity_date->isPast()
            && !$this->activity_date->isToday();
    }

    public function getIsFromPreviousShiftAttribute(): bool
    {
        return $this->status !== 'done'
            && $this->activity_date->lt(now()->startOfDay());
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeToday($query)
    {
        return $query->whereDate('activity_date', today());
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'done');
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('activity_date', $date);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }
}
