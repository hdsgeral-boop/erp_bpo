<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AiProvider;
use App\Models\AiAgent;
use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\AiModel;
use App\Services\AI\ProviderManager;
use Illuminate\Support\Facades\Crypt;

class AiAdminController extends Controller
{
    /**
     * Dashboard principal da Inteligência Artificial
     */
    public function dashboard()
    {
        $totalConversations = AiConversation::count();
        $totalMessages = AiMessage::count();
        $totalCost = AiMessage::sum('cost');
        $totalTokens = AiMessage::sum('tokens_used');
        $agentsCount = AiAgent::count();

        return view('ai.admin.dashboard', compact(
            'totalConversations', 
            'totalMessages', 
            'totalCost', 
            'totalTokens',
            'agentsCount'
        ));
    }

    public function agents()
    {
        $agents = AiAgent::with('provider', 'aiModel')->get();
        return view('ai.admin.agents.index', compact('agents'));
    }

    public function models()
    {
        $models = AiModel::with('provider')->get();
        return view('ai.admin.models.index', compact('models'));
    }

    public function providers()
    {
        $providers = AiProvider::orderBy('priority', 'asc')->get();
        return view('ai.admin.providers.index', compact('providers'));
    }

    public function testConnection(Request $request)
    {
        $request->validate([
            'provider_id' => 'required|exists:ai_providers,id'
        ]);

        $provider = AiProvider::findOrFail($request->provider_id);
        
        $result = ProviderManager::testConnection($provider);

        if ($result['success']) {
            return response()->json(['success' => true, 'message' => $result['message'], 'time' => $result['response_time']]);
        }

        return response()->json(['success' => false, 'message' => $result['message']], 500);
    }

    public function tools()
    {
        return view('ai.admin.tools.index');
    }

    public function conversations()
    {
        $conversations = AiConversation::with('user', 'agent')->orderBy('updated_at', 'desc')->paginate(15);
        return view('ai.admin.conversations.index', compact('conversations'));
    }
}
