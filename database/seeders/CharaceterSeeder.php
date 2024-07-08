<?php

namespace Database\Seeders;

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
        //Yangyang
        DB::table('characters')->insert([
            'name' => 'yangyang',
            'attribute' => 2,
            'weapon' => 1,
            'rarity' => 4,
        ]);
    }
}
