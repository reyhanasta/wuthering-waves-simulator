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
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use App\Filament\Resources\CharacterResource\Pages;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class CharacterResource extends Resource
{
    protected static ?string $model = Character::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                TextInput::make('name')
                    ->translateLabel()
                    ->columnSpan(2)
                    ->live(debounce: 500)
                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state, ?string $old) {
                        if (($get('slug') ?? '') !== Str::slug($old)) {
                            return;
                        }
                        $set('slug', Str::slug($state));
                    })
                    ->dehydrateStateUsing(fn($state) => ucwords($state))
                    ->required(),
                TextInput::make('slug')
                    ->readOnly()
                    ->filled()
                    // ->unique()
                    ->validationMessages([
                        // 'unique' => 'The :attribute has already been registered.',
                        'filled' => 'Harus di isi'
                    ]),
                Select::make('rarity')
                    ->options(Rarity::all()
                        ->pluck('level', 'id')
                        ->toArray())->live()->required(),
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
                ImageColumn::make('weaponType.name')
                    ->label('Weapon Image')
                    ->getStateUsing(function (Character $record) {
                        return $record->weaponType->getFirstMediaUrl('weaponType', 'weaponType'); // Mengambil URL gambar pertama dari WeaponType
                    })->label('Weapon Type'),
                TextColumn::make('name')->searchable(),
                ImageColumn::make('attributeType.name')
                    ->label('Attribute')
                    ->getStateUsing(function (Character $record) {
                        return $record->attributeType->getFirstMediaUrl('attribute', 'attribute'); // Mengambil URL gambar pertama dari WeaponType
                    }),
            ])
            ->filters([
                //
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
