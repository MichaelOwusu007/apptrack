<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityUpdate extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'activity_id',
        'updated_by',
        'personnel_name',
        'personnel_role',
        'personnel_department',
        'previous_status',
        'new_status',
        'remarks',
        'ip_address',
        'user_agent',
        'browser',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return Activity::STATUS_COLORS[$this->new_status] ?? 'text-gray-400';
    }
}
