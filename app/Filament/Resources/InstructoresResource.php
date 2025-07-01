<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InstructoresResource\Pages;
use App\Models\Instructores;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class InstructoresResource extends Resource
{
    protected static ?string $model = Instructores::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function canSee(): bool
    {
        return auth()->user()->can('ver instructores');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ver instructores');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('crear instructores');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can('editar instructores');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can('eliminar instructores');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('📘 Instructor')
                    ->schema([
                        Forms\Components\TextInput::make('nombre_completo')
                            ->label('Nombre completo')
                            ->required(),
                        Forms\Components\TextInput::make('celular')
                            ->label('Celular')
                            ->required(),
                        Forms\Components\TextInput::make('correo')
                            ->label('Correo Electrónico')
                            ->email()
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
                Tables\Columns\TextColumn::make('nombre_completo')
                    ->label('Nombre Completo')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('celular')
                    ->label('Celular')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('correo')
                    ->label('Correo Electrónico')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('etapa_productiva_count')
                    ->label('Aprendices Asignados'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('ver_aprendices')
                    ->label('Ver Aprendices')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Aprendices Asignados')
                    ->modalSubheading('Lista de aprendices asignados a este instructor')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalContent(function (Model $record) {
                        return view('instructores.aprendices-modal', [
                            'aprendices' => $record->etapaProductiva,
                        ]);
                    })

                    ->color('success'),
            ])
            ->bulkActions([
                ExportBulkAction::make(),
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListInstructores::route('/'),
            'create' => Pages\CreateInstructores::route('/create'),
            'edit' => Pages\EditInstructores::route('/{record}/edit'),
        ];
    }
}
