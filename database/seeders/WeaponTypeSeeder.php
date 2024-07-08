<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class WeaponTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Sword
        DB::table('weapon_types')->insert([
            'name' => 'sword',
        ]);
        //Gauntlets
        DB::table('weapon_types')->insert([
            'name' => 'gauntlet',
        ]);
        //Broadblade
        DB::table('weapon_types')->insert([
            'name' => 'broadblade',
        ]);
        //Pistols
        DB::table('weapon_types')->insert([
            'name' => 'pistols',
        ]);
        //Rectifier
        DB::table('weapon_types')->insert([
            'name' => 'rectifier',
        ]);
    }
}
