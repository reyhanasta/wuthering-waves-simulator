<?php

namespace Database\Seeders;

use App\Models\Rarity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RaritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

       Rarity::insert([
            'level' => 'SSR',
            'drop_rates' => 0.8
        ]);
       Rarity::insert([
            'level' => 'SR',
            'drop_rates' => 6.0
        ]);

       Rarity::insert([
            'level' => 'R',
            'drop_rates' => 93.2
        ]);
    }
}
