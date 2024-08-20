<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Rarity;
use App\Models\Weapon;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\WeaponType;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\WeaponResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\WeaponResource\RelationManagers;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class WeaponResource extends Resource
{
    protected static ?string $model = Weapon::class;

    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                TextInput::make('name')
                    ->translateLabel()
                    ->autocapitalize('words')
                    ->columnSpan(2)
                    ->dehydrateStateUsing(fn($state) => ucwords($state))
                    ->live(debounce: 1000)
                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state, ?string $old) {
                        if (($get('slug') ?? '') !== Str::slug($old)) {
                            return;
                        }
                        $set('slug', Str::slug($state));
                    })->required(),
                TextInput::make('slug')
                    ->translateLabel()
                    ->readOnly(),
                Select::make('rarity')
                    ->options(Rarity::all()
                        ->pluck('level', 'id')
                        ->toArray())->live()->required(),
                Select::make('type')
                    ->options(WeaponType::all()
                        ->pluck('name', 'id')
                        ->map(fn($name) => ucwords($name))
                        ->toArray())->live()->required(),
                SpatieMediaLibraryFileUpload::make('img')
                    ->disk('gacha')
                    ->directory('weapons')
                    ->label('Upload Images')
                    ->preserveFilenames()
                    ->responsiveImages()
                    ->collection('gacha')
                    ->conversion('thumb')
                    ->columnSpan(3)->required(),
                // FileUpload::make('img')
                // ->label('Upload Images')
                // ->directory('images/weapons')
                // ->preserveFilenames()
                // ->columnSpan(3)->required(),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                SpatieMediaLibraryImageColumn::make('img')
                    ->circular()
                    ->collection('gacha')
                    ->conversion('thumb')
                    ->disk('gacha'),
                TextColumn::make('name')->searchable(),
                TextColumn::make('weaponRarity.level')->sortable()->badge()->color(fn(string $state): string => match ($state) {
                    'SSR' => 'danger',
                    'SR' => 'warning',
                    'R' => 'gray',
                }),
                TextColumn::make('weaponType.name')->sortable(),
            ])
            ->filters([
                //
                SelectFilter::make('type')
                    ->options(
                        WeaponType::all()
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
            'index' => Pages\ListWeapons::route('/'),
            'create' => Pages\CreateWeapon::route('/create'),
            'edit' => Pages\EditWeapon::route('/{record}/edit'),
        ];
    }
}
