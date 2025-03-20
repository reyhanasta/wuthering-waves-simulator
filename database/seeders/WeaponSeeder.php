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
                'name' => 'Marcato',
                'slug' => 'marcato',
                'type' => 2,
                'rarity' => 2,
                'img' => '1/T_Luckdraw21040024_UI.png',
                'specifications' => 'standard'
            ],
            [
                'name' => 'Sword of Night',
                'slug' => 'sword-of-night',
                'type' => 1,
                'rarity' => 3,
                'img' => '/',
                'specifications' => 'standard'
            ],
            [
                'name' => 'Verdant Summit',
                'slug' => 'verdant-summit',
                'type' => 3,
                'rarity' => 1,
                'img' => '/',
                'specifications' => 'limited'
            ],
            // tambahkan senjata lainnya
        ];


        foreach ($weapons as $weapon) {
            $newWeapon = Weapon::create([
                'name'=> $weapon['name'],
                'slug'=> $weapon['slug'],
                'img'=> $weapon['img'],
                'type'=> $weapon['type'],
                'rarity'=> $weapon['rarity'],
                'specifications'=> $weapon['specifications'],
                ]
            );

               // Path ke file di public/storage/icons/gacha/
               $imagePath = asset('storage/icons/gacha/'.$weapon['img']);
               

            if (file_exists($imagePath)) {
                $newWeapon->addMedia($imagePath)
                          ->preservingOriginal()
                          ->toMediaCollection('gacha', 'gacha'); // Sesuai dengan konfigurasi disk
            }
        }
    }
}
