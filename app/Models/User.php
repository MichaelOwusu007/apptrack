<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, SoftDeletes, HasRoles;

    protected $fillable = [
        'full_name',
        'employee_id',
        'email',
        'phone_number',
        'department',
        'profile_photo_path',
        'password',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function assignedActivities()
    {
        return $this->hasMany(Activity::class, 'assigned_to');
    }

    public function createdActivities()
    {
        return $this->hasMany(Activity::class, 'created_by');
    }

    public function activityUpdates()
    {
        return $this->hasMany(ActivityUpdate::class, 'updated_by');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function appNotifications()
    {
        return $this->hasMany(AppNotification::class);
    }

    public function unreadNotifications()
    {
        return $this->hasMany(AppNotification::class)->whereNull('read_at');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo_path) {
            return asset('storage/' . $this->profile_photo_path);
        }

        $name = urlencode($this->full_name);
        return "https://ui-avatars.com/api/?name={$name}&color=FFFFFF&background=0EA5E9&size=128&bold=true";
    }

    public function getPrimaryRoleAttribute(): string
    {
        return $this->roles->first()?->name ?? 'support_staff';
    }

    public function getPrimaryRoleLabelAttribute(): string
    {
        return match($this->primary_role) {
            'admin'         => 'Administrator',
            'supervisor'    => 'Supervisor',
            'support_staff' => 'Support Staff',
            default         => ucfirst($this->primary_role),
        };
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
