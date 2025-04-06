<?php

declare(strict_types=1);

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
        Schema::create('an_bias', function (Blueprint $table): void {
            $table->id();
            $table->string('biasable1_type', 191);
            $table->string('biasable1_id', 191);
            $table->string('biasable1_route_name')->nullable();
            $table->string('biasable2_type', 191);
            $table->string('biasable2_id', 191);
            $table->string('biasable2_route_name')->nullable();
            $table->unsignedInteger('bias')->default(0);
            $table->string('last_session_id')->nullable();
            $table->timestamps();

            $table->index(['biasable1_type', 'biasable1_id']);
            $table->index(['biasable2_type', 'biasable2_id']);
            $table->unique(['biasable1_id', 'biasable1_type', 'biasable2_id', 'biasable2_type'], 'biasable_unique');
            $table->index('last_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('an_bias');
    }
};
