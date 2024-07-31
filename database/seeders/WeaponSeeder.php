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
                'name' => 'Sword of Night',
                'slug' => 'sword-of-night',
                'type' => 1,
                'rarity' => 3,
                'img' => '/images/weapons/iron-sword.png',
                'specifications' => 'standard'
            ],
            [
                'name' => 'Marcato',
                'slug' => 'marcato',
                'type' => 2,
                'rarity' => 2,
                'img' => '/images/weapons/marcato.png',
                'specifications' => 'standard'
            ],
            [
                'name' => 'Verdant Summit',
                'slug' => 'verdant-summit',
                'type' => 3,
                'rarity' => 1,
                'img' => '/images/weapons/ages-of-harvest.png',
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
