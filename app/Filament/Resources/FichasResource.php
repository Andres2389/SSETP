<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FichasResource\Pages;
use App\Filament\Resources\FichasResource\RelationManagers;
use App\Models\Fichas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class FichasResource extends Resource
{
    protected static ?string $model = Fichas::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    // 👇 Permisos personalizados usando Spatie

    public static function canSee(): bool
    {
        return auth()->user()->can('ver fichas');
    }
    public static function canViewAny(): bool
    {
        return auth()->user()->can('ver fichas');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('crear fichas');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can('editar fichas');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can('eliminar fichas');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('📘 Ficha y Programa')
                    ->schema([
                        Forms\Components\TextInput::make('numero')
                            ->label('Número de Ficha')
                            ->required(),
                        Forms\Components\TextInput::make('programa_formacion')
                            ->label('Programa de Formación')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('numero')
                    ->label('Número de Ficha')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('programa_formacion')
                    ->label('Programa de Formación')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make(),
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
            'index' => Pages\ListFichas::route('/'),
            'create' => Pages\CreateFichas::route('/create'),
            'edit' => Pages\EditFichas::route('/{record}/edit'),
        ];
    }
}
