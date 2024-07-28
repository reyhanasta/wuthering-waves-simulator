<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Rarity;
use Filament\Forms\Form;
use App\Models\Character;
use App\Models\WeaponType;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CharacterResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CharacterResource\RelationManagers;
use App\Models\Attribute as ModelsAttribute;
use Attribute;

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
                    ->autocapitalize('words')
                    ->columnSpan(2)
                    ->live(onBlur:true)
                    ->afterStateUpdated(function(string $state,Forms\Set $set){
                        $set('slug',Str::slug($state));
                    })->required(),
                    TextInput::make('slug')
                    ->translateLabel()
                    ->readOnly(),
                    Select::make('rarity')
                    ->options(Rarity::all()
                    ->pluck('level', 'id')
                    ->toArray())->required(),
                    Select::make('weapon')
                    ->options(WeaponType::all()
                    ->pluck('name', 'id')
                    ->map(function ($name) {
                        return ucwords($name);
                    })->toArray())->required(),
                    Select::make('attribute')
                    ->options(ModelsAttribute::all()
                    ->pluck('name', 'id')
                    ->map(function ($name) {
                        return ucwords($name);
                    })->toArray())->required(),
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
