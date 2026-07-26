<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Notifications\NotificationEngine;
use App\Models\NotificationRecipient;
use App\Models\NotificationPreference;
use Carbon\Carbon;

class NotificationController extends Controller
{
    protected NotificationEngine $engine;

    public function __construct(NotificationEngine $engine)
    {
        $this->engine = $engine;
    }

    /**
     * Retorna a contagem de notificações não lidas (Ultra-rápido para o Badge do Header).
     */
    public function unreadCount(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->user()->company_id ?? 1);
        $userId = auth()->id();

        $count = $this->engine->getUnreadCount($companyId, $userId);

        return response()->json([
            'success' => true,
            'unread_count' => $count
        ]);
    }

    /**
     * Retorna as 5 notificações mais recentes formatadas para o Dropdown do Sino no Header.
     */
    public function recent(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->user()->company_id ?? 1);
        $userId = auth()->id();

        $recipients = $this->engine->getRecentForDropdown($companyId, $userId, 5);

        $formatted = $recipients->map(function ($r) {
            $n = $r->notification;
            return [
                'id' => $r->id,
                'title' => $n->title ?? 'Notificação',
                'message' => $n->message ?? '',
                'type' => $n->type ?? 'INFO',
                'priority' => $n->priority ?? 'NORMAL',
                'category' => $n->category ?? 'GERAL',
                'action_url' => $n->action_url ?? null,
                'is_read' => $r->is_read,
                'time_ago' => $r->created_at ? $r->created_at->diffForHumans() : 'Agora',
                'created_at' => $r->created_at ? $r->created_at->toDateTimeString() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'unread_count' => $this->engine->getUnreadCount($companyId, $userId),
            'notifications' => $formatted
        ]);
    }

    /**
     * Página Principal /notifications com lista completa e filtros.
     */
    public function index(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->user()->company_id ?? 1);
        $userId = auth()->id();

        $query = NotificationRecipient::where('company_id', $companyId)
            ->where('user_id', $userId)
            ->with('notification')
            ->orderBy('created_at', 'desc');

        // Filtro por Estado (unread, read)
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->status === 'read') {
                $query->where('is_read', true);
            }
        }

        // Filtro por Categoria
        if ($request->filled('category')) {
            $query->whereHas('notification', function ($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        // Filtro por Prioridade
        if ($request->filled('priority')) {
            $query->whereHas('notification', function ($q) use ($request) {
                $q->where('priority', $request->priority);
            });
        }

        // Pesquisa de Texto
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('notification', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $recipients = $query->paginate(15)->withQueryString();
        $unreadCount = $this->engine->getUnreadCount($companyId, $userId);

        return view('notifications.index', compact('recipients', 'unreadCount'));
    }

    /**
     * Marca uma notificação específica como lida.
     */
    public function markAsRead(Request $request, $id)
    {
        $companyId = session('company_id') ?? (auth()->user()->company_id ?? 1);
        $userId = auth()->id();

        $success = $this->engine->markAsRead($companyId, $userId, (int)$id);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Notificação marcada como lida.' : 'Notificação não encontrada ou já lida.'
        ]);
    }

    /**
     * Marca todas as notificações do utilizador como lidas.
     */
    public function markAllAsRead(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->user()->company_id ?? 1);
        $userId = auth()->id();

        $count = $this->engine->markAllAsRead($companyId, $userId);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => "{$count} notificações marcadas como lidas."
            ]);
        }

        return redirect()->back()->with('success', "{$count} notificações marcadas como lidas.");
    }

    /**
     * Elimina uma notificação específica do destinatário.
     */
    public function destroy(Request $request, $id)
    {
        $companyId = session('company_id') ?? (auth()->user()->company_id ?? 1);
        $userId = auth()->id();

        $recipient = NotificationRecipient::where('company_id', $companyId)
            ->where('user_id', $userId)
            ->where('id', $id)
            ->firstOrFail();

        $recipient->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Notificação removida.']);
        }

        return redirect()->back()->with('success', 'Notificação removida com sucesso.');
    }
}
