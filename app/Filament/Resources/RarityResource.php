<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RarityResource\Pages;
use App\Filament\Resources\RarityResource\RelationManagers;
use App\Models\Rarity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RarityResource extends Resource
{
    protected static ?string $model = Rarity::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = 'Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('level')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'SSR' => 'super-rare',
                        'SR' => 'rare',
                        'R' => 'primary',
                    }),
                TextColumn::make('drop_rates')->numeric()->suffix('%')->color('gray'),
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
            'index' => Pages\ListRarities::route('/'),
            'create' => Pages\CreateRarity::route('/create'),
            'edit' => Pages\EditRarity::route('/{record}/edit'),
        ];
    }
}
