<?php

namespace Database\Seeders;

use App\Models\WeaponType;
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
        WeaponType::insert([
            'name' => 'sword',
        ]);
        //Gauntlets
        WeaponType::insert([
            'name' => 'gauntlet',
        ]);
        //Broadblade
        WeaponType::insert([
            'name' => 'broadblade',
        ]);
        //Pistols
        WeaponType::insert([
            'name' => 'pistols',
        ]);
        //Rectifier
        WeaponType::insert([
            'name' => 'rectifier',
        ]);
    }
}
