<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Tax::create([
            'name' => 'PPH',
            'description' => 'Pajak PPH',
            'type' => 'percentage',
            'value' => 11,
            'status' => 'active',
            'expired_date' => '2025-01-07'
        ]);

        \App\Models\Tax::create([
            'name' => 'PPN',
            'description' => 'Pajak PPN',
            'type' => 'percentage',
            'value' => 5,
            'status' => 'active',
            'expired_date' => '2024-12-31'
        ]);
    }
}
