<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'done', 'escalated'])->default('pending');
            $table->string('activity_type')->nullable(); // e.g., SMS Count, API Monitor, Server Check
            $table->text('remarks')->nullable();
            $table->date('activity_date');
            $table->foreignUuid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['activity_date', 'status']);
            $table->index(['assigned_to', 'activity_date']);
            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
