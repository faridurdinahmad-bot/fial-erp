<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('brands', 'brand_code')) {
            Schema::table('brands', function (Blueprint $table) {
                $table->string('brand_code')->nullable()->after('id');
            });
        }

        $this->deduplicateBrandNames();
        $this->backfillBrandCodes();

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE brands MODIFY brand_code VARCHAR(255) NOT NULL');
        } elseif (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE brands ALTER COLUMN brand_code SET NOT NULL');
        }

        Schema::table('brands', function (Blueprint $table) {
            $table->unique(['name', 'company_id']);
            $table->unique(['brand_code', 'company_id']);
        });
    }

    /**
     * Ensure unique (name, company_id) by appending (2), (3) to duplicates.
     */
    private function deduplicateBrandNames(): void
    {
        $duplicates = DB::table('brands')
            ->select('name', 'company_id', DB::raw('COUNT(*) as cnt'), DB::raw('MIN(id) as keep_id'))
            ->groupBy('name', 'company_id')
            ->having('cnt', '>', 1)
            ->get();

        foreach ($duplicates as $row) {
            $brands = DB::table('brands')
                ->where('name', $row->name)
                ->where('company_id', $row->company_id)
                ->orderBy('created_at')
                ->get();
            $suffix = 2;
            foreach ($brands as $brand) {
                if ($brand->id === $row->keep_id) {
                    continue;
                }
                $newName = $row->name . ' (' . $suffix . ')';
                DB::table('brands')->where('id', $brand->id)->update(['name' => $newName]);
                $suffix++;
            }
        }
    }

    /**
     * Assign BR-0001, BR-0002, ... per company for existing brands.
     */
    private function backfillBrandCodes(): void
    {
        $companies = DB::table('brands')->distinct()->pluck('company_id');

        foreach ($companies as $companyId) {
            $brands = DB::table('brands')
                ->where('company_id', $companyId)
                ->orderBy('created_at')
                ->get();

            $num = 1;
            foreach ($brands as $brand) {
                $code = 'BR-' . str_pad((string) $num, 4, '0', STR_PAD_LEFT);
                DB::table('brands')->where('id', $brand->id)->update(['brand_code' => $code]);
                $num++;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropUnique(['name', 'company_id']);
            $table->dropUnique(['brand_code', 'company_id']);
            $table->dropColumn('brand_code');
        });
    }
};
