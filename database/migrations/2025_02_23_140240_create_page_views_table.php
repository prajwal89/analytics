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
        Schema::create('an_page_views', function (Blueprint $table): void {
            $table->id();
            $table->string('path');
            $table->string('query_string')->json()->nullable();
            $table->string('route_name')->nullable();
            $table->string('viewable_id', 64)->nullable();
            $table->string('viewable_type', 255)->nullable();
            $table->ipAddress('ip_address');
            $table->string('country_code', 10)->nullable();
            $table->text('user_agent')->nullable();

            $table->string('device')->nullable();
            $table->string('platform')->nullable();
            $table->string('browser')->nullable();

            $table->text('referrer_url')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('exit_at')->nullable();
            $table->integer('time_on_page')->nullable(); // In seconds
            $table->integer('scroll_depth')->nullable(); // Percentage (0-100)
            $table->integer('viewport_height')->nullable();
            $table->integer('viewport_width')->nullable();
            $table->string('session_id')->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->index(['viewable_id', 'viewable_type']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('an_page_views');
    }
};
