<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Company extends Model
{
    protected $guarded = [];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'is_master_data' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($company) {
            if (!$company->trial_ends_at) {
                $company->trial_ends_at = Carbon::now()->addDays(30);
            }
            if (!$company->subscription_status) {
                $company->subscription_status = 'trial';
            }
        });
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'company_user');
    }

    public function currentPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'current_plan_id');
    }

    public function payments()
    {
        return $this->hasMany(SubscriptionPayment::class, 'company_id');
    }

    /**
     * Retorna a data limite efetiva da licença (fim do trial ou fim da subscrição paga)
     */
    public function getEffectiveExpirationDateAttribute(): Carbon
    {
        if ($this->subscription_status === 'active' && $this->subscription_ends_at) {
            return $this->subscription_ends_at;
        }

        return $this->trial_ends_at ?? Carbon::now()->addDays(30);
    }

    /**
     * Retorna o número de dias restantes de licença
     */
    public function getRemainingDaysAttribute(): int
    {
        $expirationDate = $this->effective_expiration_date;
        $now = Carbon::now();

        if ($now->greaterThan($expirationDate)) {
            return 0;
        }

        return (int)$now->diffInDays($expirationDate, false);
    }

    /**
     * Verifica se a licença está ativa (paga ou em trial válido)
     */
    public function isLicenseActive(): bool
    {
        if ($this->is_master_data) {
            return true; // Empresa master do sistema nunca expira
        }

        if ($this->subscription_status === 'active') {
            return !$this->subscription_ends_at || Carbon::now()->lessThanOrEqualTo($this->subscription_ends_at);
        }

        if ($this->subscription_status === 'trial') {
            return $this->trial_ends_at && Carbon::now()->lessThanOrEqualTo($this->trial_ends_at);
        }

        return false;
    }

    /**
     * Verifica se deve ser exibido aviso de aviso prévio (dias restantes <= 5)
     */
    public function hasWarning(): bool
    {
        if ($this->is_master_data) {
            return false;
        }

        $days = $this->remaining_days;
        return $days <= 5 && $days >= 0;
    }
}
