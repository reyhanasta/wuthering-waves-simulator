<?php

namespace Database\Seeders;

use App\Models\Character;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CharaceterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Yangyang 4 Star
        Character::insert([
            'name' => 'yangyang',
            'attribute' => 2,
            'weapon' => 1,
            'rarity' => 2,
            'specifications' => 'standard',
        ]);

        //Jianxin 5 Star Standard
        Character::insert([
            'name' => 'jianxin',
            'attribute' => 2,
            'weapon' => 2,
            'rarity' => 2,
            'specifications' => 'standard',
        ]);

        //Changli 5 Star Limited 
        Character::insert([
            'name' => 'yangyang',
            'attribute' => 5,
            'weapon' => 1,
            'rarity' => 1,
            'specifications' => 'standard',
        ]);
    }
}
