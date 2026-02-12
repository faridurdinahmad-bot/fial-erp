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
        Schema::table('products', function (Blueprint $table) {
            $table->uuid('brand_id')->nullable()->after('product_group_id')->index();
            $table->uuid('unit_id')->nullable()->after('brand_id')->index();
            $table->uuid('warranty_id')->nullable()->after('unit_id')->index();
        });

        $this->backfillExistingProducts();

        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE products MODIFY brand_id CHAR(36) NOT NULL, MODIFY unit_id CHAR(36) NOT NULL, MODIFY warranty_id CHAR(36) NOT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE products ALTER COLUMN brand_id SET NOT NULL, ALTER COLUMN unit_id SET NOT NULL, ALTER COLUMN warranty_id SET NOT NULL');
        }

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('brand_id')->references('id')->on('brands')->cascadeOnDelete();
            $table->foreign('unit_id')->references('id')->on('units')->cascadeOnDelete();
            $table->foreign('warranty_id')->references('id')->on('warranties')->cascadeOnDelete();
        });
    }

    /**
     * Backfill brand_id, unit_id, warranty_id for existing products so NOT NULL is safe.
     */
    private function backfillExistingProducts(): void
    {
        $companies = DB::table('products')->distinct()->pluck('company_id');

        foreach ($companies as $companyId) {
            $brandId = DB::table('brands')->where('company_id', $companyId)->value('id');
            $unitId = DB::table('units')->where('company_id', $companyId)->value('id');
            $warrantyId = DB::table('warranties')->where('company_id', $companyId)->value('id');

            if (! $brandId || ! $unitId || ! $warrantyId) {
                $brandId = $brandId ?? $this->createDefaultBrand($companyId);
                $unitId = $unitId ?? $this->createDefaultUnit($companyId);
                $warrantyId = $warrantyId ?? $this->createDefaultWarranty($companyId);
            }

            DB::table('products')->where('company_id', $companyId)->update([
                'brand_id' => $brandId,
                'unit_id' => $unitId,
                'warranty_id' => $warrantyId,
            ]);
        }
    }

    private function createDefaultBrand(string $companyId): string
    {
        $id = (string) \Illuminate\Support\Str::uuid();
        DB::table('brands')->insert([
            'id' => $id,
            'company_id' => $companyId,
            'name' => 'Default',
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $id;
    }

    private function createDefaultUnit(string $companyId): string
    {
        $id = (string) \Illuminate\Support\Str::uuid();
        DB::table('units')->insert([
            'id' => $id,
            'company_id' => $companyId,
            'name' => 'Default',
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $id;
    }

    private function createDefaultWarranty(string $companyId): string
    {
        $id = (string) \Illuminate\Support\Str::uuid();
        DB::table('warranties')->insert([
            'id' => $id,
            'company_id' => $companyId,
            'name' => 'Default',
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $id;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropForeign(['unit_id']);
            $table->dropForeign(['warranty_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['brand_id', 'unit_id', 'warranty_id']);
        });
    }
};
