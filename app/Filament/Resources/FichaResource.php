<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FichaResource\Pages;
use App\Models\Ficha;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Imports\FichasImport;
use Illuminate\Database\Eloquent\Builder;

class FichaResource extends Resource
{
    protected static ?string $model = Ficha::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Fichas';

    protected static ?string $navigationGroup = 'Gestión Académica';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Ficha')
                    ->schema([
                        Forms\Components\TextInput::make('numero')
                            ->label('Número de Ficha')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('programa_formacion')
                            ->label('Programa de Formación')
                            ->required()
                            ->maxLength(255),


                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero')
                    ->label('Número')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('programa_formacion')
                    ->label('Programa de Formación')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado_ficha')
                    ->label('Estado')
                    ->options([
                        'activa' => 'Activa',
                        'finalizada' => 'Finalizada',
                        'cancelada' => 'Cancelada',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'create' => Pages\CreateFicha::route('/create'),
            'view' => Pages\ViewFicha::route('/{record}'),
            'edit' => Pages\EditFicha::route('/{record}/edit'),

        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_fichas') || auth()->user()->isAdmin();
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_fichas') || auth()->user()->isAdmin();
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit_fichas') || auth()->user()->isAdmin();
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_fichas') || auth()->user()->isAdmin();
    }
}
