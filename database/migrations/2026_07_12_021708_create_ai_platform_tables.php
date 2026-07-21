<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('driver'); // openai, deepseek, gemini, etc
            $table->string('base_url')->nullable();
            $table->text('api_key')->nullable(); // encrypted
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ai_agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('ai_provider_id')->nullable()->constrained('ai_providers')->onDelete('set null');
            $table->string('name');
            $table->text('description')->nullable();
            $table->longText('system_prompt');
            $table->string('model'); // e.g. gpt-4, deepseek-chat
            $table->decimal('temperature', 3, 2)->default(0.7);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('ai_agent_id')->constrained('ai_agents')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_conversation_id')->constrained('ai_conversations')->onDelete('cascade');
            $table->string('role'); // user, assistant, system, tool
            $table->longText('content')->nullable();
            $table->json('tool_calls')->nullable();
            $table->string('tool_call_id')->nullable();
            $table->integer('tokens_used')->default(0);
            $table->decimal('cost', 10, 6)->default(0);
            $table->json('meta')->nullable(); // IP, User Agent, Time spent
            $table->timestamps();
        });

        Schema::create('ai_agent_tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_agent_id')->constrained('ai_agents')->onDelete('cascade');
            $table->string('tool_class'); // The FQCN of the tool class
            $table->timestamps();
        });

        Schema::create('ai_knowledge_bases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_agent_id')->constrained('ai_agents')->onDelete('cascade');
            $table->string('document_path');
            $table->string('status')->default('pending'); // pending, indexed, error
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_knowledge_bases');
        Schema::dropIfExists('ai_agent_tools');
        Schema::dropIfExists('ai_messages');
        Schema::dropIfExists('ai_conversations');
        Schema::dropIfExists('ai_agents');
        Schema::dropIfExists('ai_providers');
    }
};
