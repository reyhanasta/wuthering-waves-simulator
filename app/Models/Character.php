<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use App\Models\CharacterAttribute;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Character extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = ['id'];

    public function attributeType()
    {
        return $this->belongsTo(CharacterAttribute::class, 'attribute');
    }

    public function weaponType()
    {
        return $this->belongsTo(WeaponType::class, 'weapon');
    }

    public function characterRarity()
    {
        return $this->belongsTo(Rarity::class, 'rarity');
    }

    public function getImageUrl()
    {
        $media = $this->getFirstMedia();
        return $media ? $media->getUrl() : null;
    }
}
