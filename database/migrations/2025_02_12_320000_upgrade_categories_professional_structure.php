<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            if (! Schema::hasColumn('categories', 'code')) {
                $table->string('code')->nullable()->after('company_id');
            }

            if (! Schema::hasColumn('categories', 'slug')) {
                $table->string('slug')->nullable()->after('name');
            }

            if (! Schema::hasColumn('categories', 'description')) {
                $table->text('description')->nullable()->after('slug');
            }

            if (! Schema::hasColumn('categories', 'image')) {
                $table->string('image')->nullable()->after('description');
            }

            // sort_order already exists from original migration

            if (! Schema::hasColumn('categories', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('sort_order');
            }

            if (! Schema::hasColumn('categories', 'show_in_menu')) {
                $table->boolean('show_in_menu')->default(true)->after('is_featured');
            }

            if (! Schema::hasColumn('categories', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('show_in_menu');
            }

            if (! Schema::hasColumn('categories', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
        });

        // Backfill code and slug for existing rows
        $this->backfillCodesAndSlugs();

        // Make code non-nullable and add unique constraint per company
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE categories MODIFY code VARCHAR(255) NOT NULL");
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE categories ALTER COLUMN code SET NOT NULL');
        }

        Schema::table('categories', function (Blueprint $table): void {
            $table->unique(['code', 'company_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->dropUnique(['code', 'company_id']);
            $table->dropColumn([
                'code',
                'slug',
                'description',
                'image',
                'is_featured',
                'show_in_menu',
                'meta_title',
                'meta_description',
            ]);
        });
    }

    private function backfillCodesAndSlugs(): void
    {
        $categories = DB::table('categories')
            ->select('id', 'company_id', 'name', 'code', 'slug')
            ->orderBy('company_id')
            ->orderBy('created_at')
            ->get()
            ->groupBy('company_id');

        foreach ($categories as $companyId => $rows) {
            $num = 1;
            foreach ($rows as $row) {
                $code = $row->code ?: 'CAT-' . str_pad((string) $num, 4, '0', STR_PAD_LEFT);
                $slug = $row->slug ?: Str::slug($row->name ?? '');

                DB::table('categories')
                    ->where('id', $row->id)
                    ->update([
                        'code' => $code,
                        'slug' => $slug,
                    ]);

                $num++;
            }
        }
    }
};

