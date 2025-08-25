<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InstructorResource\Pages;
use App\Models\Instructor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InstructorResource extends Resource
{
    protected static ?string $model = Instructor::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Instructores';

    protected static ?string $pluralModelLabel = 'Instructores';

    protected static ?string $navigationGroup = 'Gestión Académica';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Instructor')
                    ->schema([
                        Forms\Components\TextInput::make('nombre_completo')
                            ->label('Nombre Completo')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('correo')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('celular')
                            ->label('Celular')
                            ->tel()
                            ->maxLength(20),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_completo')
                    ->label('Nombre Completo')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('correo')
                    ->label('Correo')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('celular')
                    ->label('Celular')
                    ->searchable(),

                Tables\Columns\TextColumn::make('aprendices_asignados_count')
                    ->label('Aprendices Asignados')
                    ->getStateUsing(fn ($record) => $record->aprendices_asignados_count ?? 0)
                    ->alignCenter(),


                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('ver_aprendices')
                    ->label('Ver Aprendices')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Aprendices Asignados')
                    ->modalSubheading('Lista de aprendices asignados a este instructor')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                   ->modalContent(function (Instructor $record) {
                    return view('instructores.aprendices-modal', [
                        'etapas' => $record->etapaProductivas()->with('ficha')->get(),
                    ]);
                })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nombre_completo');
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
            'index' => Pages\ListInstructors::route('/'),
            'create' => Pages\CreateInstructor::route('/create'),
            'view' => Pages\ViewInstructor::route('/{record}'),
            'edit' => Pages\EditInstructor::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_instructores') || auth()->user()->isAdmin();
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_instructores') || auth()->user()->isAdmin();
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit_instructores') || auth()->user()->isAdmin();
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_instructores') || auth()->user()->isAdmin();
    }
}
