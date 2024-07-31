<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Weapon extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $guarded = ['id'];

    public function weaponRarity()
    {
        return $this->belongsTo(Rarity::class, 'rarity');
    }
    public function weaponType()
    {
        return $this->belongsTo(WeaponType::class, 'type');
    }
}
