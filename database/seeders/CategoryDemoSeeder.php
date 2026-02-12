<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Company;
use Illuminate\Database\Seeder;

class CategoryDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Resets demo categories per company using BaseTenantModel currentCompany binding.
     */
    public function run(): void
    {
        Company::query()->chunkById(100, function ($companies): void {
            foreach ($companies as $company) {
                app()->instance('currentCompany', $company);

                // 1) Delete all categories for this company (main + sub), bypassing scopes and soft deletes
                Category::withoutGlobalScopes()
                    ->where('company_id', $company->id)
                    ->forceDelete();

                // 2) Create 3 main categories: Category 0, 1, 2
                $mains = [];
                for ($i = 0; $i < 3; $i++) {
                    $mains[$i] = Category::create([
                        'name'              => 'Category ' . $i,
                        'parent_id'         => null,
                        'description'       => null,
                        'status'            => true,
                        'sort_order'        => $i,
                        'is_featured'       => false,
                        'show_in_menu'      => true,
                        'meta_title'        => null,
                        'meta_description'  => null,
                        // code, slug, company_id, id, image handled by model/events
                    ]);
                }

                // 3) For each main, create exactly 3 subcategories: Category X - Sub 0..2
                foreach ($mains as $i => $main) {
                    for ($j = 0; $j < 3; $j++) {
                        Category::create([
                            'name'              => 'Category ' . $i . ' - Sub ' . $j,
                            'parent_id'         => $main->id,
                            'description'       => null,
                            'status'            => true,
                            'sort_order'        => $j,
                            'is_featured'       => false,
                            'show_in_menu'      => true,
                            'meta_title'        => null,
                            'meta_description'  => null,
                            // code, slug, company_id, id, image handled automatically
                        ]);
                    }
                }

                app()->forgetInstance('currentCompany');
            }
        });
    }
}
