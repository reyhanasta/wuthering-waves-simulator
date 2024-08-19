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
            'star' => '5',
            'drop_rates' => 0.8
        ]);
       Rarity::insert([
            'level' => 'SR',
            'star' => '4',
            'drop_rates' => 6.0
        ]);

       Rarity::insert([
            'level' => 'R',
            'star' => '3',
            'drop_rates' => 93.2
        ]);
    }
}
