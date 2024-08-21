<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Rarity;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Character;
use App\Models\WeaponType;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use App\Models\CharacterAttribute;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Resources\CharacterResource\Pages;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class CharacterResource extends Resource
{
    protected static ?string $model = Character::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Main Item';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() == 0 ? 'danger' : 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                TextInput::make('name')
                    ->translateLabel()
                    ->columnSpan(3)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state, ?string $old) {
                        if (($get('slug') ?? '') !== Str::slug($old)) {
                            return;
                        }
                        $set('slug', Str::slug($state));
                    })
                    ->dehydrateStateUsing(fn($state) => ucwords($state))
                    ->required(),
                Hidden::make('slug'),
                Select::make('rarity')->relationship('characterRarity', 'level')->required(),
                Select::make('weapon')->relationship('weaponType', 'name')->required(),
                Select::make('attribute')->relationship('attributeType', 'name')->required(),
                SpatieMediaLibraryFileUpload::make('icon')
                    ->disk('gacha')
                    ->directory('charaters')
                    ->label('Upload Images')
                    ->preserveFilenames()
                    ->responsiveImages()
                    ->collection('gacha')
                    ->conversion('thumb')
                    ->columnSpan(3)->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                SpatieMediaLibraryImageColumn::make('image')
                    ->circular()
                    ->collection('gacha')
                    ->conversion('thumb')
                    ->disk('gacha')
                    ->size(70),
                TextColumn::make('name')->searchable(),
                TextColumn::make('characterRarity.level')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'SSR' => 'super-rare',
                        'SR' => 'rare',
                        'R' => 'primary',
                    })->alignment(Alignment::Center),
                ImageColumn::make('weaponType.name')
                    ->label('Weapon Image')
                    ->getStateUsing(function (Character $record) {
                        return $record->weaponType->getFirstMediaUrl('weaponType', 'weaponType'); // Mengambil URL gambar pertama dari WeaponType
                    })->label('Weapon Type')->alignment(Alignment::Center),
                ImageColumn::make('attributeType.name')
                    ->label('Attribute')
                    ->getStateUsing(function (Character $record) {
                        return $record->attributeType->getFirstMediaUrl('attribute', 'attribute'); // Mengambil URL gambar pertama dari WeaponType
                    })->alignment(Alignment::Center),
            ])
            ->filters([
                SelectFilter::make('weapon')
                    ->options(
                        WeaponType::all()
                            ->pluck('name', 'id')
                            ->map(function ($name) {
                                return ucwords($name);
                            })
                            ->toArray()
                    ),
                SelectFilter::make('rarity')
                    ->options(
                        Rarity::all()
                            ->pluck('level', 'id')
                            ->map(function ($name) {
                                return ucwords($name);
                            })
                            ->toArray()
                    ),
                SelectFilter::make('attribute')
                    ->options(
                        CharacterAttribute::all()
                            ->pluck('name', 'id')
                            ->map(function ($name) {
                                return ucwords($name);
                            })
                            ->toArray()
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCharacters::route('/'),
            'create' => Pages\CreateCharacter::route('/create'),
            'edit' => Pages\EditCharacter::route('/{record}/edit'),
        ];
    }
}
