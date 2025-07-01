<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RolResource\Pages;
use App\Filament\Resources\RolResource\RelationManagers;
use App\Models\Rol;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions;
use Filament\Tables\Columns\TextColumn;

class RolResource extends Resource
{
    protected static ?string $model = Rol::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';
    protected static ?string $navigationLabel = 'Roles';
    protected static ?string $modelLabel = 'Rol';
    protected static ?string $pluralModelLabel = 'Roles';
    protected static ?string $navigationGroup = 'Gestión de Accesos';

    public static function canSee(): bool
    {
        return auth()->user()->can('ver roles');
    }
    public static function canViewAny(): bool
    {
        return auth()->user()->can('ver roles');
    }
    public static function canCreate(): bool
    {
        return auth()->user()->can('crear roles');
    }
   public static function canEdit(Model $record): bool
    {
        return auth()->user()->can('editar roles');
    }
    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can('eliminar roles');
    }

    public static function form(Form $form): Form
    {
        return $form
             ->schema([
                TextInput::make('name')
                    ->label('Nombre del Rol')
                    ->required(),

                CheckboxList::make('permissions')
                    ->label('Permisos')
                    ->relationship('permissions', 'name')
                    ->columns(2)
                    ->required()
                    ->searchable()
                    ->bulkToggleable()
                    ->helperText('Selecciona los permisos que deseas asignar a este rol.'),
            ]);
    }

    public static function table(Table $table): Table
    {
       return $table
            ->columns([
                TextColumn::make('name')->label('Nombre'),
                TextColumn::make('permissions.name')->label('Permisos')   ,
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label('Eliminar Seleccionados'),

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
            'index' => Pages\ListRols::route('/'),
            'create' => Pages\CreateRol::route('/create'),
            'edit' => Pages\EditRol::route('/{record}/edit'),
        ];
    }
}
