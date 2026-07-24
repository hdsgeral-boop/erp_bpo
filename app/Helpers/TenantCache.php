<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

/**
 * TenantCache Helper — ERP Consulvolt Multi-tenant Isolation
 *
 * Garante o isolamento estrito de cache Redis por Tenant no modelo SaaS.
 * É PROIBIDO utilizar chaves globais para dados pertencentes a empresas.
 *
 * Chaves geradas: tenant:{company_id}:{key}
 */
class TenantCache
{
    /**
     * Retorna a chave formatada com o escopo do Tenant atual.
     */
    public static function key(string $key, ?int $companyId = null): string
    {
        if ($companyId === null) {
            $companyId = session('current_company_id') 
                      ?? auth()->user()?->company_id 
                      ?? 'global';
        }

        return "tenant:{$companyId}:{$key}";
    }

    /**
     * Armazena ou recupera um item do cache de forma isolada por Tenant.
     */
    public static function remember(string $key, \DateInterval|\DateTimeInterface|int $ttl, \Closure $callback, ?int $companyId = null)
    {
        $tenantKey = self::key($key, $companyId);
        $companyIdResolved = $companyId ?? session('current_company_id') ?? auth()->user()?->company_id ?? 'global';

        // Se o driver suportar tags, aplica a tag do tenant para invalidação em lote segura
        if (self::supportsTags()) {
            return Cache::tags(["tenant_{$companyIdResolved}"])->remember($tenantKey, $ttl, $callback);
        }

        return Cache::remember($tenantKey, $ttl, $callback);
    }

    /**
     * Armazena um item no cache isolado por Tenant sem expiração imediata.
     */
    public static function put(string $key, mixed $value, \DateInterval|\DateTimeInterface|int $ttl = 3600, ?int $companyId = null): bool
    {
        $tenantKey = self::key($key, $companyId);
        $companyIdResolved = $companyId ?? session('current_company_id') ?? auth()->user()?->company_id ?? 'global';

        if (self::supportsTags()) {
            return Cache::tags(["tenant_{$companyIdResolved}"])->put($tenantKey, $value, $ttl);
        }

        return Cache::put($tenantKey, $value, $ttl);
    }

    /**
     * Recupera um item do cache isolado por Tenant.
     */
    public static function get(string $key, mixed $default = null, ?int $companyId = null): mixed
    {
        $tenantKey = self::key($key, $companyId);
        $companyIdResolved = $companyId ?? session('current_company_id') ?? auth()->user()?->company_id ?? 'global';

        if (self::supportsTags()) {
            return Cache::tags(["tenant_{$companyIdResolved}"])->get($tenantKey, $default);
        }

        return Cache::get($tenantKey, $default);
    }

    /**
     * Remove um item específico do cache do Tenant.
     */
    public static function forget(string $key, ?int $companyId = null): bool
    {
        $tenantKey = self::key($key, $companyId);
        $companyIdResolved = $companyId ?? session('current_company_id') ?? auth()->user()?->company_id ?? 'global';

        if (self::supportsTags()) {
            return Cache::tags(["tenant_{$companyIdResolved}"])->forget($tenantKey);
        }

        return Cache::forget($tenantKey);
    }

    /**
     * Limpa todo o cache pertencente exclusivamente a um Tenant específico.
     */
    public static function flushTenant(?int $companyId = null): bool
    {
        $companyIdResolved = $companyId ?? session('current_company_id') ?? auth()->user()?->company_id ?? 'global';

        if (self::supportsTags()) {
            return Cache::tags(["tenant_{$companyIdResolved}"])->flush();
        }

        // Caso tags não estejam ativas, limpa a chave genérica do tenant
        return Cache::forget("tenant:{$companyIdResolved}:dashboard") && Cache::forget("tenant:{$companyIdResolved}:settings");
    }

    /**
     * Verifica se o store de cache atual suporta Cache Tags (Redis/Memcached).
     */
    protected static function supportsTags(): bool
    {
        try {
            return method_exists(Cache::getStore(), 'tags');
        } catch (\Throwable $e) {
            return false;
        }
    }
}
