<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            // Idempotency: prevent duplicate automation todos per blog + template combination
            $table->foreignUuid('generated_from_template_id')
                ->nullable()
                ->constrained('customer_todo_templates')
                ->nullOnDelete();

            $table->unique(
                ['blog_id', 'customer_id', 'generated_from_template_id'],
                'todos_automation_idempotency_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->dropUnique('todos_automation_idempotency_unique');
            $table->dropColumn('generated_from_template_id');
        });
    }
};
