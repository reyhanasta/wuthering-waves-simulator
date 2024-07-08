<?php

namespace Database\Seeders;

use App\Models\Weapon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WeaponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //3 Star Weapon
        Weapon::insert([
            'name' => 'iron-sword',
            'type' => 1,
            'rarity' => 5,
        ]);
        //4 Star Weapon
        Weapon::insert([
            'name' => 'marcato',
            'type' => 2,
            'rarity' => 4,
        ]);
        //5 Star Weapon
        Weapon::insert([
            'name' => 'autumn-harvest',
            'type' => 1,
            'rarity' => 1,
        ]);
    }
}
