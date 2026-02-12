<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitsDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $definitions = [
            [
                'name' => 'Kilogram',
                'short_name' => 'KG',
                'type' => 'weight',
                'decimal_allowed' => true,
            ],
            [
                'name' => 'Gram',
                'short_name' => 'GM',
                'type' => 'weight',
                'decimal_allowed' => true,
            ],
            [
                'name' => 'Piece',
                'short_name' => 'PCS',
                'type' => 'quantity',
                'decimal_allowed' => false,
            ],
            [
                'name' => 'Box',
                'short_name' => 'BOX',
                'type' => 'quantity',
                'decimal_allowed' => false,
            ],
            [
                'name' => 'Litre',
                'short_name' => 'LTR',
                'type' => 'volume',
                'decimal_allowed' => true,
            ],
            [
                'name' => 'Set',
                'short_name' => 'SET',
                'type' => 'quantity',
                'decimal_allowed' => false,
            ],
        ];

        Company::query()->chunkById(100, function ($companies) use ($definitions): void {
            foreach ($companies as $company) {
                foreach ($definitions as $def) {
                    Unit::firstOrCreate(
                        [
                            'company_id' => $company->id,
                            'short_name' => $def['short_name'],
                        ],
                        [
                            'name' => $def['name'],
                            'type' => $def['type'],
                            'decimal_allowed' => $def['decimal_allowed'],
                            'status' => true,
                        ]
                    );
                }
            }
        });
    }
}

