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

        ]);
       Rarity::insert([
            'level' => 'SR',

        ]);

       Rarity::insert([
            'level' => 'R',
        ]);
    }
}
