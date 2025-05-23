<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContributionType;

class ContributionTypeSeeder extends Seeder
{
    public function run(): void
    {
        $contributionTypes = [
            [
                'name' => 'PhilHealth',

            ],
            [
                'name' => 'GSIS',

            ],
            [
                'name' => 'Pag-IBIG',

            ],
        ];

        foreach ($contributionTypes as $type) {
            ContributionType::create($type);
        }
    }
} 