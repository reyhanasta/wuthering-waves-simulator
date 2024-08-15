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
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\CharacterResource\Pages;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Models\CharacterAttribute;

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
                    ->live(debounce: 1000)
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
                Select::make('weapon')
                    ->options(WeaponType::all()
                        ->pluck('name', 'id')
                        ->map(function ($name) {
                            return ucwords($name);
                        })->toArray())->live()->required(),
                Select::make('attribute')
                    ->options(CharacterAttribute::all()
                        ->pluck('name', 'id')
                        ->map(function ($name) {
                            return ucwords($name);
                        })->toArray())->live()->required(),
                SpatieMediaLibraryFileUpload::make('icon')
                    ->disk('character')
                    ->directory('charaters')
                    ->label('Upload Images')
                    ->preserveFilenames()
                    ->responsiveImages()
                    ->collection('character')
                    ->conversion('thumb')
                    ->columnSpan(3)->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('name')
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
