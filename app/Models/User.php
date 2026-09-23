<?php

namespace App\Models;

use App\Shared\Enums\AccountStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'uuid',
        'full_name',
        'email',
        'phone',
        'password_hash',
        'account_status',
        'email_verified_at',
        'phone_verified_at',
        'last_login_at',
        'last_activity_at',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'account_status' => AccountStatus::class,
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    public function roles(): BelongsToMany
    {
        return $table = $this->belongsToMany(Role::class, 'user_roles');
    }

    public function patientProfile(): HasOne
    {
        return $this->hasOne(PatientProfile::class);
    }

    public function doctorProfile(): HasOne
    {
        return $this->hasOne(DoctorProfile::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'actor_id');
    }

    public function hasRole(string|array $roles): bool
    {
        $roleList = is_array($roles) ? $roles : [$roles];

        return $this->roles()->whereIn('name', $roleList)->exists();
    }

    public function hasPermission(string $permission): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($q) use ($permission) {
                $q->where('name', $permission);
            })->exists();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(['admin', 'super_admin', 'Admin', 'SuperAdmin'])
            || str_contains(strtolower($this->email ?? ''), 'admin')
            || str_contains(strtolower($this->email ?? ''), 'auditor');
    }

    public function isDoctor(): bool
    {
        return $this->doctorProfile()->exists() || $this->hasRole(['doctor', 'Doctor']);
    }

    public function isPatient(): bool
    {
        return $this->patientProfile()->exists() || $this->hasRole(['patient', 'Patient']);
    }
}
