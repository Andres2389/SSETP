<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EtapaProductivaResource\Pages;
use App\Models\EtapaProductiva;
use App\Models\Fichas;
use App\Models\Instructores;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\Radio;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class EtapaProductivaResource extends Resource
{
    protected static ?string $model = EtapaProductiva::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Etapa Productiva';

    public static function getEloquentQuery(): Builder
    {
        return EtapaProductiva::query()->with(['fichas', 'instructores']);
    }


    // 👇 Permisos personalizados usando Spatie
    public static function canSee(): bool
    {
        return auth()->user()->can('ver etapaproductiva');
    }
    public static function canViewAny(): bool
    {
        return auth()->user()->can('ver etapaproductiva');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('crear etapaproductiva');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can('editar etapaproductiva');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can('eliminar etapaproductiva');
    }
    public static function form(Form $form): Form
    {
        return $form->schema([
            // 📅 Fechas
            Forms\Components\Fieldset::make('📅 Fechas')->schema([
                Forms\Components\DatePicker::make('fecha_inicio_ep')
                    ->label('Fecha Inicio EP')
                    ->displayFormat('d/m/Y')
                    ->required(),

                Forms\Components\DatePicker::make('fecha_17_meses')
                    ->label('Fecha 17 Meses')
                    ->displayFormat('d/m/Y')
                    ->required(),
            ])->columns(2),

            // 📘 Ficha y Programa
            Forms\Components\Fieldset::make('📘 Ficha y Programa')->schema([
                Forms\Components\Select::make('fichas_id')
                    ->label('Ficha')
                    ->relationship('fichas', 'numero')
                    ->searchable()
                    ->required()
                    ->placeholder('Seleccione una ficha')
                    ->preload()
                    ->afterStateUpdated(function (callable $set, $state) {
                        $ficha = Fichas::find($state);
                        $set('programa_formacion', $ficha?->programa_formacion);
                    }),

                Forms\Components\TextInput::make('programa_formacion')
                    ->label('Programa de Formación')
                    ->disabled()
                    ->required(),

                Forms\Components\TextInput::make('estado_ficha')
                    ->label('Estado de la ficha'),
            ])->columns(2),

            // 🧍 Datos Personales
            Forms\Components\Fieldset::make('🧍 Datos Personales')->schema([
                Forms\Components\Select::make('tipo_documento')
                    ->label('Tipo de Documento')
                    ->options([
                        'CC' => 'Cedula de Ciudadania',
                        'TI' => 'Tarjeta de Identidad',
                        'CE' => 'Cédula de Extranjeria',
                        'PA' => 'Pasaporte',
                        'PEP' => 'Permiso Especial de Permanencia',
                        'PPT' => 'Permiso Temporal de Protección',
                    ])
                    ->required()
                    ->placeholder('Seleccione una opción'),

                Forms\Components\TextInput::make('numero_documento')
                    ->label('Número de Documento')
                    ->required(),

                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre')
                    ->required(),

                Forms\Components\TextInput::make('apellidos')
                    ->label('Apellidos')
                    ->required(),

                Forms\Components\TextInput::make('celular')
                    ->label('Celular')
                    ->tel(),

                Forms\Components\TextInput::make('correo')
                    ->label('Correo')
                    ->email(),

                Forms\Components\Select::make('estado_sofia')
                    ->label('Estado Sofia Plus')
                    ->options([
                        'EN FORMACION' => 'EN FORMACION',
                        'POR CERTIFICAR' => 'POR CERTIFICAR',
                        'CERTIFICADO' => 'CERTIFICADO',
                        'APLAZADO' => 'APLAZADO',
                        'TRASLADADO' => 'TRASLADADO',
                    ]),
            ])->columns(2),

            // 📋 Seguimiento
            Forms\Components\Fieldset::make('📋 Seguimiento')->schema([
                Forms\Components\Select::make('instructores_id')
                    ->label('Instructor de Seguimiento')
                    ->searchable()
                    ->required()
                    ->options(
                        Instructores::all()->mapWithKeys(function ($instructor) {
                            return [
                                $instructor->id => "{$instructor->nombre_completo} - {$instructor->correo} - {$instructor->celular}"
                            ];
                        })
                    )
                    ->placeholder('Seleccione un instructor'),

                Forms\Components\DatePicker::make('fecha_asignacion')
                    ->label('Fecha Asignación')
                    ->displayFormat('d/m/Y'),

                Forms\Components\Select::make('tipo_alternativa')
                    ->label('Tipo de Alternativa')
                    ->options([
                        'Vinculo laboral' => 'Vinculo laboral',
                        'Proyecto productivo' => 'Proyecto productivo',
                        'Monitoria' => 'Monitoria',
                        'Contrato de Aprendizaje' => 'Contrato de Aprendizaje',
                        'Pasantia' => 'Pasantia',
                    ])
                    ->searchable()
                    ->placeholder('Seleccione una opción'),

                Forms\Components\DatePicker::make('fecha_inicio_alternativa')
                    ->label('Fecha Inicio Alternativa')
                    ->displayFormat('d/m/Y'),

                Forms\Components\DatePicker::make('fecha_fin_alternativa')
                    ->label('Fecha Fin Alternativa')
                    ->displayFormat('d/m/Y'),

                Forms\Components\DatePicker::make('fecha_corte')
                    ->label('Fecha Corte')
                    ->displayFormat('d/m/Y'),

                Forms\Components\Textarea::make('observaciones')
                    ->label('Observaciones')
                    ->rows(3),

                Forms\Components\TextInput::make('juicios_evaluativos')
                    ->label('Juicios Evaluativos')
                    ->maxLength(255),
            ])->columns(2),

            // 📚 Bitácoras
            Forms\Components\Fieldset::make('📚 Seguimiento de Bitácoras')->schema([
                Forms\Components\Select::make('momentos')
                    ->label('Momento')
                    ->options([
                        '1' => '1 - Inicial',
                        '2' => '2 - Parcial',
                        '3' => '3 - Final',
                    ])
                    ->required()
                    ->columnSpan('full'),

                Forms\Components\Select::make('numero_bitacoras')
                    ->label('Número de Bitácora')
                    ->options(array_combine(range(1, 12), array_map(fn($i) => "$i/12", range(1, 12))))
                    ->required()
                    ->columnSpan('full'),
            ]),

            // ✅ Paz y Salvo
            Forms\Components\Fieldset::make('✅ Paz y Salvo')->schema([
                Radio::make('paz_salvo')
                    ->label('Paz y Salvo')
                    ->options([
                        'Sí' => 'Sí',
                        'No' => 'No',
                    ])
                    ->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()->searchable(),

                Tables\Columns\TextColumn::make('fecha_inicio_ep')->label('Fecha Inicio EP')->date('d/m/Y'),
                Tables\Columns\TextColumn::make('fecha_17_meses')->label('Fecha 17 Meses')->date('d/m/Y'),

                Tables\Columns\TextColumn::make('fichas.numero')->label('Ficha')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('fichas.programa_formacion')->label('Programa')->sortable()->searchable(),

                Tables\Columns\TextColumn::make('estado_ficha')->label('Estado Ficha')->sortable()->searchable(),

                Tables\Columns\TextColumn::make('tipo_documento')->label('Tipo Doc.'),
                Tables\Columns\TextColumn::make('numero_documento')->label('Doc.')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('nombre')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('apellidos')->sortable()->searchable(),

                Tables\Columns\TextColumn::make('celular'),
                Tables\Columns\TextColumn::make('correo'),
                Tables\Columns\TextColumn::make('estado_sofia')->label('Estado Sofia'),

                Tables\Columns\TextColumn::make('instructores.nombre_completo')->label('Instructor de Seguimiento')
                ->sortable()
                ->searchable()
                ->formatStateUsing(function ($state, $record) {
                if (!$record->instructores) {
                    return '<span class="text-gray-400 italic">Sin instructor</span>';
                }

                    return '
                        <div class="flex flex-col space-y-1">
                            <span class="font-semibold text-gray-900">' . e($record->instructores->nombre_completo) . ' ' . e($record->instructores->apellidos) . '</span>
                            <span class="text-sm text-gray-600">' . e($record->instructores->correo) . '</span>
                            <span class="text-sm text-gray-600">' . e($record->instructores->celular) . '</span>
                        </div>';
                        })
                    ->html(),

                Tables\Columns\TextColumn::make('fecha_asignacion')->date('d/m/Y'),
                Tables\Columns\TextColumn::make('tipo_alternativa')->label('Alternativa'),

                Tables\Columns\TextColumn::make('fecha_inicio_alternativa')->date('d/m/Y'),
                Tables\Columns\TextColumn::make('fecha_fin_alternativa')->date('d/m/Y'),
                Tables\Columns\TextColumn::make('observaciones')->limit(50)->label('Observaciones')->searchable(),
                Tables\Columns\TextColumn::make('juicios_evaluativos')->limit(50)->label('Juicios Evaluativos'),


                Tables\Columns\TextColumn::make('fecha_corte')->date('d/m/Y'),

                Tables\Columns\TextColumn::make('momentos')->label('Momentos    '),
                Tables\Columns\TextColumn::make('numero_bitacoras')->label('Bitacoras'),
                Tables\Columns\TextColumn::make('paz_salvo'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('fichas_id')
                    ->label('Ficha')
                    ->relationship('fichas', 'numero')
                    ->preload()
                    ->searchable()
                    ->placeholder('Todas las fichas'),

                Tables\Filters\SelectFilter::make('instructores_id')
                    ->label('Instructor de Seguimiento')
                    ->relationship('instructores', 'nombre_completo')
                    ->preload()
                    ->searchable()
                    ->placeholder('Todos los instructores'),

                Tables\Filters\SelectFilter::make('estado_sofia')
                    ->options([
                        'EN FORMACION' => 'EN FORMACION',
                        'POR CERTIFICAR' => 'POR CERTIFICAR',
                        'CERTIFICADO' => 'CERTIFICADO',
                        'APLAZADO' => 'APLAZADO',
                        'TRASLADADO' => 'TRASLADADO',
                    ])
                    ->placeholder('Todos los estados'),
            ])
            ->actions([
                Tables\Actions\Action::make('verObservacion')
                ->label('Ver mas observaciones')
                ->icon('heroicon-o-eye')
                ->modalHeading('Observación completa')
                ->modalSubheading(fn ($record) => 'De ' . $record->nombre . ' ' . $record->apellidos)
                ->modalContent(fn ($record) => view('etapaproductiva.etapa-modal', ['observacion' => $record->observaciones]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Cerrar')
                ->color('success'),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                ExportBulkAction::make()->label('Exportar'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEtapaProductivas::route('/'),
            'create' => Pages\CreateEtapaProductiva::route('/create'),
            'edit' => Pages\EditEtapaProductiva::route('/{record}/edit'),
        ];
    }
}
