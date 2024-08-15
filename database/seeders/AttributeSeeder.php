<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\CharacterAttribute;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Spectro Element
        CharacterAttribute::insert([
            'name' => 'spectro',
        ]);
        //Aero Element
        CharacterAttribute::insert([
            'name' => 'aero',
        ]);
        //Glacio Element
        CharacterAttribute::insert([
            'name' => 'glacio',
        ]);
        //Havoc Element
        CharacterAttribute::insert([
            'name' => 'havoc',
        ]);
        //Fusion Element
        CharacterAttribute::insert([
            'name' => 'fusion',
        ]);
        //Electro Element
        CharacterAttribute::insert([
            'name' => 'electro',
        ]);
    }
}
