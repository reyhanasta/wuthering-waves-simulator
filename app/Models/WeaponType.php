<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class WeaponType extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

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
