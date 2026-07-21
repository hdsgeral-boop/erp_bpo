<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AiProvider;
use App\Models\AiAgent;
use App\Models\AiAgentTool;
use App\Models\AiModel;
use Illuminate\Support\Facades\Crypt;

class AiPlatformSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Criar Providers
        $deepSeek = AiProvider::create([
            'name' => 'DeepSeek Code',
            'driver' => 'deepseek',
            'base_url' => 'https://api.deepseek.com/v1',
            'api_key' => env('DEEPSEEK_API_KEY', 'sk-dummy-key'),
            'priority' => 2,
            'is_active' => true,
        ]);

        $openAI = AiProvider::create([
            'name' => 'OpenAI Premium',
            'driver' => 'openai',
            'base_url' => 'https://api.openai.com/v1',
            'api_key' => env('OPENAI_API_KEY', 'sk-dummy-key'),
            'priority' => 1,
            'fallback_id' => $deepSeek->id, // OpenAI tem fallback para DeepSeek
            'is_active' => true,
        ]);

        // 2. Criar Modelos para OpenAI
        $gpt4oMini = AiModel::create([
            'ai_provider_id' => $openAI->id,
            'name' => 'GPT-4o Mini',
            'identifier' => 'gpt-4o-mini',
            'supports_chat' => true,
            'supports_function_calling' => true,
            'supports_tool_calling' => true,
            'context_window' => 128000,
            'is_active' => true,
        ]);

        $gpt4o = AiModel::create([
            'ai_provider_id' => $openAI->id,
            'name' => 'GPT-4o',
            'identifier' => 'gpt-4o',
            'supports_chat' => true,
            'supports_vision' => true,
            'supports_function_calling' => true,
            'supports_tool_calling' => true,
            'context_window' => 128000,
            'is_active' => true,
        ]);

        // 3. Criar Modelos para DeepSeek
        $deepseekChat = AiModel::create([
            'ai_provider_id' => $deepSeek->id,
            'name' => 'DeepSeek Chat',
            'identifier' => 'deepseek-chat',
            'supports_chat' => true,
            'supports_function_calling' => true, // DeepSeek supports function calling exactly like OpenAI
            'supports_tool_calling' => true,
            'context_window' => 64000,
            'is_active' => true,
        ]);

        // 4. Criar Agente Financeiro / Operacional
        $agent = AiAgent::create([
            'ai_provider_id' => $openAI->id,
            'ai_model_id' => $gpt4oMini->id,
            'name' => 'Assistente Operacional ERP',
            'description' => 'Agente inteligente capaz de responder a questões financeiras e logísticas interrogando diretamente a base de dados via ferramentas seguras.',
            'system_prompt' => 'És o Assistente Virtual do ERP Consulvolt. Responde de forma sucinta e profissional em Português de Portugal. Usa as ferramentas disponíveis para obter dados em tempo real sempre que te pedirem informações sobre Vendas, Faturação ou Stocks.',
            'temperature' => 0.4,
            'is_active' => true,
        ]);

        // 5. Atribuir Ferramentas Iniciais ao Agente
        AiAgentTool::create([
            'ai_agent_id' => $agent->id,
            'tool_class' => 'App\Services\AI\Tools\SalesConsultTool'
        ]);

        AiAgentTool::create([
            'ai_agent_id' => $agent->id,
            'tool_class' => 'App\Services\AI\Tools\StockConsultTool'
        ]);
    }
}
