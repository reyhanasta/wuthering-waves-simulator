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
        DB::table('attributes')->insert([
            'name' => 'spectro',
        ]);
        //Aero Element
        DB::table('attributes')->insert([
            'name' => 'aero',
        ]);
        //Glacio Element
        DB::table('attributes')->insert([
            'name' => 'glacio',
        ]);
        //Havoc Element
        DB::table('attributes')->insert([
            'name' => 'havoc',
        ]);
        //Fusion Element
        DB::table('attributes')->insert([
            'name' => 'fusion',
        ]);
        //Electro Element
        DB::table('attributes')->insert([
            'name' => 'electro',
        ]);
    }
}
