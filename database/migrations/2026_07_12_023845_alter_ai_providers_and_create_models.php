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
        // Alter ai_providers table
        Schema::table('ai_providers', function (Blueprint $table) {
            $table->integer('priority')->default(1)->after('api_key');
            $table->unsignedBigInteger('fallback_id')->nullable()->after('priority');
            $table->decimal('temperature', 3, 2)->default(0.7)->after('fallback_id');
            $table->integer('max_tokens')->default(2000)->after('temperature');
            $table->integer('timeout')->default(60)->after('max_tokens');
            $table->boolean('stream')->default(false)->after('timeout');

            // Set up fallback self-referencing foreign key if needed
            $table->foreign('fallback_id')->references('id')->on('ai_providers')->onDelete('set null');
        });

        // Create ai_models table
        Schema::create('ai_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_provider_id')->constrained('ai_providers')->onDelete('cascade');
            $table->string('name');
            $table->string('identifier'); // e.g. gpt-4o, gemini-1.5-pro
            
            // Capabilities
            $table->boolean('supports_chat')->default(true);
            $table->boolean('supports_streaming')->default(false);
            $table->boolean('supports_vision')->default(false);
            $table->boolean('supports_function_calling')->default(false);
            $table->boolean('supports_tool_calling')->default(false);
            $table->boolean('supports_embeddings')->default(false);
            $table->boolean('supports_json_mode')->default(false);
            
            $table->integer('context_window')->nullable();
            $table->integer('max_tokens')->nullable();
            $table->decimal('max_temperature', 3, 2)->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Modify ai_agents to remove string 'model' and use foreign key 'ai_model_id'
        Schema::table('ai_agents', function (Blueprint $table) {
            $table->dropColumn('model');
            $table->foreignId('ai_model_id')->nullable()->after('ai_provider_id')->constrained('ai_models')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_agents', function (Blueprint $table) {
            $table->dropForeign(['ai_model_id']);
            $table->dropColumn('ai_model_id');
            $table->string('model')->after('system_prompt');
        });

        Schema::dropIfExists('ai_models');

        Schema::table('ai_providers', function (Blueprint $table) {
            $table->dropForeign(['fallback_id']);
            $table->dropColumn(['priority', 'fallback_id', 'temperature', 'max_tokens', 'timeout', 'stream']);
        });
    }
};
