<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_log', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('actor_type', ['tablet', 'admin', 'system']);
            $table->string('actor_id');
            $table->string('action', 100);
            $table->uuid('target_id')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('occurred_at');
            
            // NO updated_at column
            $table->timestamp('created_at')->nullable();

            $table->index('occurred_at');
            $table->index(['actor_type', 'actor_id']);
            $table->index('action');
            $table->index('target_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};
