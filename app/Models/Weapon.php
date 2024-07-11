<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Weapon extends Model
{
    use HasFactory;

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
