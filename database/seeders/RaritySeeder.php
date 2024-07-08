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
        //Sword
       Rarity::insert([
            'level' => '5',
            'type' => 'limited',
        ]);
       Rarity::insert([
            'level' => '5',
            'type' => 'standard',
        ]);
       Rarity::insert([
            'level' => '4',
            'type' => 'limited',
        ]);
       Rarity::insert([
            'level' => '4',
            'type' => 'standard',
        ]);
       Rarity::insert([
            'level' => '3',
            'type' => 'standard',
        ]);
    }
}
