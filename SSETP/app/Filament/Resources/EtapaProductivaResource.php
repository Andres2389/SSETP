<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EtapaProductivaResource\Pages;
use App\Models\EtapaProductiva;
use App\Models\Ficha;
use App\Models\Instructor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EtapaProductivaResource extends Resource
{
    protected static ?string $model = EtapaProductiva::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Etapa Productiva';

    protected static ?string $navigationGroup = 'Gestión Académica';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 1. Fechas Importantes
                Forms\Components\Section::make('Fechas Importantes')
                    ->schema([
                        Forms\Components\DatePicker::make('fecha_inicio_ep')
                            ->label('Fecha Inicio EP'),

                        Forms\Components\DatePicker::make('fecha_17_meses')
                            ->label('Fecha 17 Meses'),

                        Forms\Components\DatePicker::make('fecha_asignacion')
                            ->label('Fecha Asignación'),

                        Forms\Components\DatePicker::make('fecha_inicio_alternativa')
                            ->label('Fecha Inicio Alternativa'),

                        Forms\Components\DatePicker::make('fecha_fin_alternativa')
                            ->label('Fecha Fin Alternativa'),

                        Forms\Components\DatePicker::make('fecha_corte')
                            ->label('Fecha Corte'),
                    ])
                    ->columns(3),

                // 2. Ficha y Programa de Formación
                Forms\Components\Section::make('Ficha y Programa de Formación')
                    ->schema([
                        Forms\Components\Select::make('fichas_id')
                            ->label('Ficha')
                            ->options(Ficha::pluck('numero', 'id'))
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                $ficha = \App\Models\Ficha::find($state);
                                $set('programa_formacion', $ficha?->programa_formacion ?? '');
                            }),

                        Forms\Components\TextInput::make('programa_formacion')
                            ->label('Programa de Formación')
                            ->disabled(),

                        Forms\Components\Select::make('estado_ficha')
                            ->label('Estado de la ficha')
                            ->options([
                                'aplazado' => 'Aplazado',
                                'trasladado' => 'Trasladado',
                                'condicionado' => 'Condicionado',
                                'en_formacion' => 'En Formación',
                                'por_certificar' => 'Por Certificar',
                                'certificado' => 'Certificado',
                                'cancelado' => 'Cancelado',
                            ]),

                        Forms\Components\Select::make('tipo_alternativa')
                            ->label('Tipo de Alternativa')
                            ->options([
                                'Vinculo laboral' => 'Vínculo laboral',
                                'Proyecto productivo' => 'Proyecto productivo',
                                'Monitoria' => 'Monitoría',
                                'Contrato de Aprendizaje' => 'Contrato de Aprendizaje',
                                'Pasantia' => 'Pasantía',
                            ])
                            ->searchable()
                            ->placeholder('Seleccione una opción'),
                    ])
                    ->columns(2),

                // 3. Información del Aprendiz
                Forms\Components\Section::make('Información del Aprendiz')
                    ->schema([
                        Forms\Components\Select::make('tipo_documento')
                            ->label('Tipo de Documento')
                            ->options([
                                'CC' => 'Cédula de Ciudadanía',
                                'TI' => 'Tarjeta de Identidad',
                                'CE' => 'Cédula de Extranjería',
                                'PA' => 'Pasaporte',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('numero_documento')
                            ->label('Número de Documento')
                            ->required()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombres')
                            ->required()
                            ->maxLength(100),

                        Forms\Components\TextInput::make('apellidos')
                            ->label('Apellidos')
                            ->required()
                            ->maxLength(100),

                        Forms\Components\TextInput::make('celular')
                            ->label('Celular')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('correo')
                            ->label('Correo Electrónico')
                            ->email()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                // 4. Estado y Asignaciones
                Forms\Components\Section::make('Estado y Asignaciones')
                    ->schema([
                        Forms\Components\Select::make('estado_sofia')
                            ->label('Estado en SOFIA')
                            ->options([
                                'aplazado' => 'Aplazado',
                                'trasladado' => 'Trasladado',
                                'condicionado' => 'Condicionado',
                                'en_formacion' => 'En Formación',
                                'por_certificar' => 'Por Certificar',
                                'certificado' => 'Certificado',
                                'cancelado' => 'Cancelado',
                            ])
                            ->required(),

                        Forms\Components\Select::make('instructores_id')
                            ->label('Instructor Asignado')
                            ->options(Instructor::pluck('nombre_completo', 'id'))
                            ->searchable()
                            ->nullable(),

                        Forms\Components\Select::make('momentos')
                            ->label('Momento Actual')
                            ->options([
                                '1' => 'Momento 1',
                                '2' => 'Momento 2',
                                '3' => 'Momento 3',
                            ])
                            ->required(),

                        Forms\Components\Toggle::make('paz_salvo')
                            ->label('Paz y Salvo')
                            ->default(false),
                    ])
                    ->columns(2),

                // 5. Observaciones y Juicios Evaluativos
                Forms\Components\Section::make('Observaciones y Juicios Evaluativos')
                    ->schema([
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->rows(3),

                        Forms\Components\TextInput::make('juicios_evaluativos')
                            ->label('Juicios Evaluativos')
                            ->maxLength(500),
                    ])
                    ->columns(1),

                // 6. Bitácoras
                Forms\Components\Section::make('Bitácoras')
                    ->schema([
                        Forms\Components\Placeholder::make('bitacoras_info')
                            ->label('Estado de Bitácoras')
                            ->content(function ($record) {
                                if (!$record) return 'N/A - Registro nuevo';

                                $subidas = $record->bitacoras_subidas_count ?? 0;
                                $aceptadas = $record->bitacoras_aceptadas_count ?? 0;
                                $pendientes = $record->bitacoras_pendientes_count ?? 0;
                                $devueltas = $record->bitacoras_devueltas_count ?? 0;

                                return "Subidas: {$subidas} | Aceptadas: {$aceptadas} | Pendientes: {$pendientes} | Devueltas: {$devueltas}";
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Fechas Importantes
                Tables\Columns\TextColumn::make('fecha_inicio_ep')
                    ->label('Fecha Inicio EP')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_17_meses')
                    ->label('Fecha 17 Meses')
                    ->date()
                    ->sortable(),

                // 2. Ficha y Programa
                Tables\Columns\TextColumn::make('ficha.numero')
                    ->label('Ficha')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('programa_formacion')
                    ->label('Programa de Formación')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\BadgeColumn::make('estado_ficha')
                    ->label('Estado de la Ficha')
                    ->colors([
                        'warning' => 'aplazado',
                        'info' => 'trasladado',
                        'danger' => 'condicionado',
                        'primary' => 'en_formacion',
                        'warning' => 'por_certificar',
                        'success' => 'certificado',
                        'danger' => 'cancelado',
                    ]),

                // 3. Información del Aprendiz
                Tables\Columns\TextColumn::make('tipo_documento')
                    ->label('Tipo Doc.')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('numero_documento')
                    ->label('Número Documento')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nombre_completo')
                    ->label('Nombre Completo')
                    ->getStateUsing(fn ($record) => $record->nombre . ' ' . $record->apellidos)
                    ->searchable(['nombre', 'apellidos'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('celular')
                    ->label('Celular')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('correo')
                    ->label('Correo Electrónico')
                    ->toggleable(isToggledHiddenByDefault: true),

                // 4. Estado en SOFIA + Instructor
                Tables\Columns\BadgeColumn::make('estado_sofia')
                    ->label('Estado SOFIA')
                    ->colors([
                        'warning' => 'aplazado',
                        'info' => 'trasladado',
                        'danger' => 'condicionado',
                        'primary' => 'en_formacion',
                        'warning' => 'por_certificar',
                        'success' => 'certificado',
                        'danger' => 'cancelado',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('instructor.nombre_completo')
                    ->label('Instructor Asignado')
                    ->searchable(),

                // 5. Alternativa
                Tables\Columns\TextColumn::make('fecha_asignacion')
                    ->label('Fecha Asignación')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tipo_alternativa')
                    ->label('Tipo Alternativa')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('fecha_inicio_alternativa')
                    ->label('Inicio Alternativa')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_fin_alternativa')
                    ->label('Fin Alternativa')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_corte')
                    ->label('Fecha Corte')
                    ->date()
                    ->sortable(),

                // 6. Observaciones y Juicios
                Tables\Columns\TextColumn::make('observaciones')
                    ->label('Observaciones')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('juicios_evaluativos')
                    ->label('Juicios Evaluativos')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                // 7. Estado y Seguimiento
                Tables\Columns\BadgeColumn::make('momentos')
                    ->label('Momento Actual')
                    ->colors([
                        'success' => '1',
                        'warning' => '2',
                        'danger' => '3',
                    ])
                    ->sortable(),

                Tables\Columns\IconColumn::make('paz_salvo')
                    ->label('Paz y Salvo')
                    ->boolean()
                    ->alignCenter(),

                // 8. Bitácoras
                Tables\Columns\TextColumn::make('bitacoras_count')
                    ->label('Bitácoras (Aceptadas/Total)')
                    ->getStateUsing(function ($record) {
                        $total = $record->bitacoraUploads()->count();
                        $aceptadas = $record->bitacoraUploads()->where('estado_revision', 'aceptado')->count();
                        return "{$aceptadas}/{$total}";
                    })
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('fichas_id')
                    ->label('Ficha')
                    ->options(Ficha::pluck('numero', 'id')),

                Tables\Filters\SelectFilter::make('instructores_id')
                    ->label('Instructor')
                    ->options(Instructor::pluck('nombre_completo', 'id')),

                Tables\Filters\SelectFilter::make('estado_sofia')
                    ->label('Estado SOFIA')
                    ->options([
                        'aplazado' => 'Aplazado',
                        'trasladado' => 'Trasladado',
                        'condicionado' => 'Condicionado',
                        'en_formacion' => 'En Formación',
                        'por_certificar' => 'Por Certificar',
                        'certificado' => 'Certificado',
                        'cancelado' => 'Cancelado',
                    ]),

                Tables\Filters\SelectFilter::make('momentos')
                    ->label('Momento')
                    ->options([
                        '1' => 'Momento 1',
                        '2' => 'Momento 2',
                        '3' => 'Momento 3',
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
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->extremePaginationLinks()
            ->poll('30s');
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
            'index' => Pages\ListEtapaProductivas::route('/'),
            'create' => Pages\CreateEtapaProductiva::route('/create'),
            'view' => Pages\ViewEtapaProductiva::route('/{record}'),
            'edit' => Pages\EditEtapaProductiva::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_etapa_productiva') || auth()->user()->isAdmin();
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_etapa_productiva') || auth()->user()->isAdmin();
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit_etapa_productiva') || auth()->user()->isAdmin();
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_etapa_productiva') || auth()->user()->isAdmin();
    }
}
