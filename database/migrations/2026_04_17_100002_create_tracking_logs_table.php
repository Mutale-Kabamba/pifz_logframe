<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('logframe_item_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('recorded_by')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('actual_value');
            $table->string('evidence_link')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['logframe_item_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_logs');
    }
};
