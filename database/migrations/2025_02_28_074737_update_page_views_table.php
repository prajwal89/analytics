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
        Schema::table('an_page_views', function (Blueprint $table): void {
            $table->dropColumn(['viewed_at', 'exit_at']);
            $table->decimal('latitude', 10, 8)->nullable()->after('country_code');
            $table->decimal('longitude', 11, 8)->nullable()->after('country_code');
            $table->string('city')->nullable()->after('country_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
