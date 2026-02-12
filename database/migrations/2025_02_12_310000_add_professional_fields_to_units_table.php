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
        Schema::table('units', function (Blueprint $table) {
            if (! Schema::hasColumn('units', 'short_name')) {
                $table->string('short_name')->nullable()->after('name');
            }

            if (! Schema::hasColumn('units', 'type')) {
                $table->string('type')->nullable()->after('short_name');
            }

            if (! Schema::hasColumn('units', 'decimal_allowed')) {
                $table->boolean('decimal_allowed')->default(false)->after('type');
            }
        });

        Schema::table('units', function (Blueprint $table) {
            $table->unique(['short_name', 'company_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropUnique(['short_name', 'company_id']);
            $table->dropColumn(['short_name', 'type', 'decimal_allowed']);
        });
    }
};

