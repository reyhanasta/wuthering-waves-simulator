<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rarity extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public function weaponRarity()
    {
        return $this->hasMany(Weapon::class);
    }
    public function characterRarity()
    {
        return $this->hasMany(Character::class);
    }
}
