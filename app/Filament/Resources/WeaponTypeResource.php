<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\WeaponType;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\WeaponTypeResource\Pages;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Filament\Resources\WeaponTypeResource\RelationManagers;

class WeaponTypeResource extends Resource
{
    protected static ?string $model = WeaponType::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube-transparent';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationGroup = 'Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                SpatieMediaLibraryFileUpload::make('weaponType')
                    ->disk('weaponType')
                    ->directory('weaponType')
                    ->label('Upload Images')
                    ->preserveFilenames()
                    ->responsiveImages()
                    ->collection('weaponType')
                    ->conversion('thumb')
                    ->columnSpan(3)->required(),
                TextInput::make('name')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                SpatieMediaLibraryImageColumn::make('icon')
                    ->collection('weaponType')
                    ->conversion('thumb')
                    ->disk('weaponType'),
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
            'index' => Pages\ListWeaponTypes::route('/'),
            'create' => Pages\CreateWeaponType::route('/create'),
            'edit' => Pages\EditWeaponType::route('/{record}/edit'),
        ];
    }
}
