<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements Auditable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles, \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'job_title',
        'department',
        'avatar',
        'bio',
        'password',
        'google_id',
        'google_email',
        'google_avatar',
        'provider',
        'provider_id',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }



    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_user')
            ->withPivot(['role_id', 'status', 'invited_by', 'joined_at'])
            ->withTimestamps();
    }

    public function getCompanyIdAttribute()
    {
        return session('company_id') ?? $this->companies()->first()?->id;
    }

    public function company()
    {
        return $this->belongsToMany(Company::class, 'company_user')
            ->withPivot(['role_id', 'status', 'invited_by', 'joined_at'])
            ->withTimestamps();
    }

    /**
     * Retorna a Role Spatie associada a esta empresa específica.
     */
    public function roleInCompany($companyId)
    {
        $company = $this->companies()->where('companies.id', $companyId)->first();
        if ($company && $company->pivot->role_id) {
            return \Spatie\Permission\Models\Role::find($company->pivot->role_id);
        }
        return null;
    }

    /**
     * Retorna o nome da Role nesta empresa.
     */
    public function roleNameInCompany($companyId): string
    {
        $role = $this->roleInCompany($companyId);
        if ($role) {
            return $role->name;
        }

        // Se for Super Admin
        if ($this->hasRole('Super Admin')) {
            return 'Super Admin';
        }

        return $this->roles->first()?->name ?? 'Utilizador';
    }

    public function getRelationValue($key)
    {
        if ($key === 'company') {
            $relation = parent::getRelationValue($key);
            if ($relation instanceof \Illuminate\Support\Collection) {
                return $relation->first();
            }
            return $relation;
        }
        return parent::getRelationValue($key);
    }

    public function relationsToArray()
    {
        $relations = parent::relationsToArray();
        if (isset($relations['company']) && is_array($relations['company'])) {
            $relations['company'] = reset($relations['company']) ?: null;
        }
        return $relations;
    }
}
