<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeaponType extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public function weaponType()
    {
        return $this->hasMany(Weapon::class);
    }

    public function characterWeaponType()
    {
        return $this->hasMany(Character::class);
    }
}
