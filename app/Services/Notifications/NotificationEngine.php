<?php

namespace App\Services\Notifications;

use App\Models\Company;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class NotificationEngine
{
    /**
     * Dispara uma notificação para um ou múltiplos destinatários com isolamento por Tenant.
     *
     * @param int $companyId ID da Empresa (Tenant Context)
     * @param string $eventType Ex: ProductStockLow, InvoiceCreated, TaskAssigned
     * @param string $title Título da Notificação
     * @param string $message Mensagem Resumida
     * @param array $options [type, priority, category, module, entity_type, entity_id, action_url, created_by]
     * @param mixed $recipients ID de utilizador, Array de IDs, Coleção de utilizadores, Nome de Permissão ou null (todos do tenant)
     */
    public function dispatch(
        int $companyId,
        string $eventType,
        string $title,
        string $message,
        array $options = [],
        $recipients = null
    ): ?Notification {
        return DB::transaction(function () use ($companyId, $eventType, $title, $message, $options, $recipients) {
            // 1. Criar o registo mestre da Notificação
            $notification = Notification::create([
                'company_id' => $companyId,
                'type' => $options['type'] ?? 'INFO',
                'priority' => $options['priority'] ?? 'NORMAL',
                'category' => $options['category'] ?? 'GERAL',
                'module' => $options['module'] ?? null,
                'event_type' => $eventType,
                'title' => $title,
                'message' => $message,
                'entity_type' => $options['entity_type'] ?? null,
                'entity_id' => $options['entity_id'] ?? null,
                'action_url' => $options['action_url'] ?? null,
                'created_by' => $options['created_by'] ?? (auth()->id() ?? null),
            ]);

            // 2. Resolver os Utilizadores Destinatários pertencentes exclusivamente ao Tenant
            $targetUserIds = $this->resolveRecipientUserIds($companyId, $recipients);

            if (empty($targetUserIds)) {
                return $notification;
            }

            // 3. Filtrar por Preferências do Utilizador
            $disabledUserIds = NotificationPreference::where('company_id', $companyId)
                ->whereIn('user_id', $targetUserIds)
                ->where('event_type', $eventType)
                ->where('in_app', false)
                ->pluck('user_id')
                ->toArray();

            // Notificações CRITICAL ignoram desativações de preferência
            if (($options['priority'] ?? 'NORMAL') !== 'CRITICAL') {
                $targetUserIds = array_diff($targetUserIds, $disabledUserIds);
            }

            // 4. Criar os registos de Destinatário (notification_recipients)
            $now = Carbon::now();
            $recipientRecords = [];
            foreach ($targetUserIds as $userId) {
                $recipientRecords[] = [
                    'company_id' => $companyId,
                    'notification_id' => $notification->id,
                    'user_id' => $userId,
                    'is_read' => false,
                    'read_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                // Invalidação de Cache Redis por Utilizador
                Cache::forget("unread_count_{$companyId}_{$userId}");
            }

            if (!empty($recipientRecords)) {
                NotificationRecipient::insert($recipientRecords);
            }

            return $notification;
        });
    }

    /**
     * Resolve os IDs dos utilizadores autorizados a receber a notificação no contexto do Tenant.
     */
    protected function resolveRecipientUserIds(int $companyId, $recipients): array
    {
        $companyUsersQuery = User::whereHas('companies', function ($q) use ($companyId) {
            $q->where('companies.id', $companyId);
        });

        // Se for nulo ou 'ALL', notificar todos os utilizadores da empresa
        if ($recipients === null || $recipients === 'ALL') {
            return $companyUsersQuery->pluck('id')->toArray();
        }

        // Se for um único número ID
        if (is_numeric($recipients)) {
            return $companyUsersQuery->where('id', (int)$recipients)->pluck('id')->toArray();
        }

        // Se for um array de IDs
        if (is_array($recipients) && !empty($recipients) && is_numeric(reset($recipients))) {
            return $companyUsersQuery->whereIn('id', $recipients)->pluck('id')->toArray();
        }

        // Se for uma string representando uma Permissão (ex: 'pos.access', 'sales.view')
        if (is_string($recipients)) {
            $permissionName = $recipients;
            $allUsers = $companyUsersQuery->get();
            $filteredIds = [];
            foreach ($allUsers as $user) {
                if ($user->hasPermissionTo($permissionName) || $user->hasRole('Super Admin')) {
                    $filteredIds[] = $user->id;
                }
            }
            return $filteredIds;
        }

        // Se for uma Coleção de utilizadores
        if ($recipients instanceof Collection) {
            return $recipients->pluck('id')->toArray();
        }

        return $companyUsersQuery->pluck('id')->toArray();
    }

    /**
     * Obtém o número de notificações não lidas (com suporte a Cache Redis de alta velocidade).
     */
    public function getUnreadCount(int $companyId, int $userId): int
    {
        return Cache::remember("unread_count_{$companyId}_{$userId}", 60, function () use ($companyId, $userId) {
            return NotificationRecipient::where('company_id', $companyId)
                ->where('user_id', $userId)
                ->where('is_read', false)
                ->count();
        });
    }

    /**
     * Obtém as notificações recentes para o dropdown do Header.
     */
    public function getRecentForDropdown(int $companyId, int $userId, int $limit = 5): Collection
    {
        return NotificationRecipient::where('company_id', $companyId)
            ->where('user_id', $userId)
            ->with('notification')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Marca uma notificação individual como lida com validação estrita de segurança Tenant + User.
     */
    public function markAsRead(int $companyId, int $userId, int $recipientId): bool
    {
        $recipient = NotificationRecipient::where('company_id', $companyId)
            ->where('user_id', $userId)
            ->where('id', $recipientId)
            ->first();

        if ($recipient && !$recipient->is_read) {
            $recipient->update([
                'is_read' => true,
                'read_at' => Carbon::now(),
            ]);

            Cache::forget("unread_count_{$companyId}_{$userId}");
            return true;
        }

        return false;
    }

    /**
     * Marca todas as notificações do utilizador no tenant como lidas.
     */
    public function markAllAsRead(int $companyId, int $userId): int
    {
        $count = NotificationRecipient::where('company_id', $companyId)
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => Carbon::now(),
            ]);

        Cache::forget("unread_count_{$companyId}_{$userId}");
        return $count;
    }
}
