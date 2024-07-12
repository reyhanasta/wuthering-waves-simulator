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

        $weapons = [
            [
                'name' => 'Iron Sword',
                'slug' => 'iron-sword',
                'type' => 1,
                'rarity' => 3,
                'img' => '/storage/images/weapons/iron-sword.png',
                'specifications' => 'standard'
            ],
            [
                'name' => 'Marcato',
                'slug' => 'marcato',
                'type' => 1,
                'rarity' => 2,
                'img' => 'u/storage/images/weapons/marcato.png',
                'specifications' => 'standard'
            ],
            [
                'name' => 'Age of Harvest',
                'slug' => 'age-of-harvest',
                'type' => 1,
                'rarity' => 1,
                'img' => '/storage/images/weapons/age-of-harvest.png',
                'specifications' => 'limited'
            ],
            // tambahkan senjata lainnya
        ];


        foreach ($weapons as $weapon) {
            Weapon::insert([
                'name'=> $weapon['name'],
                'slug'=> $weapon['slug'],
                'img'=> $weapon['img'],
                'type'=> $weapon['type'],
                'rarity'=> $weapon['rarity'],
                'specifications'=> $weapon['specifications'],
                ]
            );
        }
    }
}
