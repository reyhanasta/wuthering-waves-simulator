<?php

namespace Database\Seeders;

use App\Models\Attribute;
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
        Attribute::insert([
            'name' => 'spectro',
        ]);
        //Aero Element
        Attribute::insert([
            'name' => 'aero',
        ]);
        //Glacio Element
        Attribute::insert([
            'name' => 'glacio',
        ]);
        //Havoc Element
        Attribute::insert([
            'name' => 'havoc',
        ]);
        //Fusion Element
        Attribute::insert([
            'name' => 'fusion',
        ]);
        //Electro Element
        Attribute::insert([
            'name' => 'electro',
        ]);
    }
}
