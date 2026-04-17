<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logframe_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('logframe_items')
                ->nullOnDelete();
            $table->string('category');
            $table->text('description');
            $table->text('indicator')->nullable();
            $table->text('means_of_verification')->nullable();
            $table->text('assumptions')->nullable();
            $table->text('target_value')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['project_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logframe_items');
    }
};
