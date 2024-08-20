<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\CharacterAttribute;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Filament\Resources\CharacterAttributeResource\Pages;
use App\Filament\Resources\CharacterAttributeResource\RelationManagers;

class CharacterAttributeResource extends Resource
{
    protected static ?string $model = CharacterAttribute::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                TextInput::make('name'),
                SpatieMediaLibraryFileUpload::make('icon')
                ->disk('attribute')
                ->directory('weapons')
                ->label('Upload Images')
                ->preserveFilenames()
                ->responsiveImages()
                ->collection('attribute')
                ->conversion('thumb')
                ->columnSpan(3)->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                SpatieMediaLibraryImageColumn::make('icon')
                    ->circular()
                    ->collection('attribute')
                    ->conversion('thumb')
                    ->disk('attribute'),
                TextColumn::make('name')->searchable()
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
            'index' => Pages\ListCharacterAttributes::route('/'),
            'create' => Pages\CreateCharacterAttribute::route('/create'),
            'edit' => Pages\EditCharacterAttribute::route('/{record}/edit'),
        ];
    }
}
