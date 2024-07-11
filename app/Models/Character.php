<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function attributeType()
    {
        return $this->belongsTo(Attribute::class, 'attribute');
    }
    public function weaponType()
    {
        return $this->belongsTo(WeaponType::class, 'weapon');
    }
    public function characterRarity()
    {
        return $this->belongsTo(Rarity::class, 'rarity');
    }
}
