<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermisoResource\Pages;
use App\Filament\Resources\PermisoResource\RelationManagers;
use App\Models\Permiso;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\CheckboxList;

class PermisoResource extends Resource
{
    protected static ?string $model = Permiso::class;

    protected static ?string $navigationIcon = 'heroicon-o-finger-print';
      //protected static ?string $model = Permiso::class;
    protected static ?string $navigationLabel = 'Permisos';
    protected static ?string $modelLabel = 'Permiso';
    protected static ?string $pluralModelLabel = 'Permisos';
    protected static ?string $navigationGroup = 'Gestión de Accesos';

    public static function canSee(): bool
    {
        return auth()->user()->can('ver permiso');
    }
    public static function canViewAny(): bool
    {
        return auth()->user()->can('ver permiso');
    }
    public static function canCreate(): bool
    {
        return auth()->user()->can('crear permiso');
    }
    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can('editar permiso'); // ✅ CORRECTO
    }
    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can('eliminar permiso');
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre del Permiso')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
         return $table
            ->columns([
                TextColumn::make('name')->label('Nombre'),
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
            'index' => Pages\ListPermisos::route('/'),
            'create' => Pages\CreatePermiso::route('/create'),
            'edit' => Pages\EditPermiso::route('/{record}/edit'),
        ];
    }
}
