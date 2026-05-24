<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_updates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->foreignUuid('updated_by')->constrained('users')->restrictOnDelete();
            $table->string('personnel_name');
            $table->string('personnel_role')->nullable();
            $table->string('personnel_department')->nullable();
            $table->string('previous_status')->nullable();
            $table->string('new_status');
            $table->text('remarks')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('browser')->nullable();
            $table->timestamps();

            $table->index(['activity_id', 'created_at']);
            $table->index('updated_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_updates');
    }
};
