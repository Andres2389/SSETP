<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\Instructor;
use App\Models\EtapaProductiva;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Usuarios';

    protected static ?string $navigationGroup = 'Sistema';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de Usuario')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Roles y Permisos')
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->label('Roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                // Reset associations when roles change
                                $set('instructor_id', null);
                                $set('etapa_productiva_id', null);
                                $set('tipo_usuario', 'admin');
                            }),

                        Forms\Components\Select::make('tipo_usuario')
                            ->label('Tipo de Usuario')
                            ->options([
                                'admin' => 'Administrador',
                                'instructor' => 'Instructor',
                                'aprendiz' => 'Aprendiz',
                                'coordinador' => 'Coordinador@',
                                'apoyo coordinacion' => 'Apoyo Coordinadión',
                                'apoyo etapa productiva' => 'Apoyo etapa productiva',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state !== 'instructor') {
                                    $set('instructor_id', null);
                                }
                                if ($state !== 'aprendiz') {
                                    $set('etapa_productiva_id', null);
                                }
                            }),

                        Forms\Components\Select::make('instructor_id')
                            ->label('Instructor Asociado')
                            ->options(Instructor::pluck('nombre_completo', 'id'))
                            ->searchable()
                            ->visible(fn (Forms\Get $get) => $get('tipo_usuario') === 'instructor'),

                        Forms\Components\Select::make('etapa_productiva_id')
                            ->label('Aprendiz Asociado')
                            ->options(function () {
                                return EtapaProductiva::with('ficha')
                                    ->get()
                                    ->mapWithKeys(function ($ep) {
                                        return [$ep->id => "{$ep->nombre} {$ep->apellidos} - Ficha: {$ep->ficha->numero}"];
                                    });
                            })
                            ->searchable()
                            ->visible(fn (Forms\Get $get) => $get('tipo_usuario') === 'aprendiz'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\BadgeColumn::make('tipo_usuario')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'admin',
                        'success' => 'instructor',
                        'warning' => 'aprendiz',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'admin' => 'Admin',
                        'instructor' => 'Instructor',
                        'aprendiz' => 'Aprendiz',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->separator(','),

                Tables\Columns\TextColumn::make('instructor.nombre_completo')
                    ->label('Instructor')
                    ->toggleable()
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('etapaProductiva.nombre_completo')
                    ->label('Aprendiz')
                    ->toggleable()
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('tipo_usuario')
                    ->label('Tipo de Usuario')
                    ->options([
                        'admin' => 'Administrador',
                        'instructor' => 'Instructor',
                        'aprendiz' => 'Aprendiz',
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_users') || auth()->user()->isAdmin();
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_users') || auth()->user()->isAdmin();
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit_users') || auth()->user()->isAdmin();
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_users') || auth()->user()->isAdmin();
    }
}
