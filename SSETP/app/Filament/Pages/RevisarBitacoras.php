<?php

namespace App\Filament\Pages;

use App\Models\BitacoraUpload;
use App\Models\EtapaProductiva;
use App\Notifications\BitacoraRevisadaNotification;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class RevisarBitacoras extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string $view = 'filament.pages.revisar-bitacoras';

    protected static ?string $navigationLabel = 'Revisar Bitácoras';

    protected static ?string $title = 'Revisar Bitácoras';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()?->isInstructor() ?? false;
    }

    public function mount(): void
    {
        if (!auth()->user()?->isInstructor()) {
            abort(403);
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getBitacorasQuery())
            ->columns([
                Tables\Columns\TextColumn::make('etapaProductiva.nombre_completo')
                    ->label('Aprendiz')
                    ->searchable(['etapaProductiva.nombre', 'etapaProductiva.apellidos']),

                Tables\Columns\TextColumn::make('etapaProductiva.ficha.numero')
                    ->label('Ficha')
                    ->searchable(),

                Tables\Columns\TextColumn::make('numero_bitacora')
                    ->label('Bitácora #')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('momento')
                    ->label('Momento')
                    ->colors([
                        'success' => '1',
                        'warning' => '2',
                        'danger' => '3',
                    ]),

                Tables\Columns\TextColumn::make('file_name')
                    ->label('Archivo')
                    ->limit(30),

                Tables\Columns\BadgeColumn::make('estado_revision')
                    ->label('Estado')
                    ->colors([
                        'warning' => 'pendiente',
                        'success' => 'aceptado',
                        'danger' => 'devuelto',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pendiente' => 'Pendiente',
                        'aceptado' => 'Aceptado',
                        'devuelto' => 'Devuelto',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Subido')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('reviewed_at')
                    ->label('Revisado')
                    ->dateTime()
                    ->placeholder('Sin revisar'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado_revision')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'aceptado' => 'Aceptado',
                        'devuelto' => 'Devuelto',
                    ])
                    ->default('pendiente'),

                Tables\Filters\SelectFilter::make('momento')
                    ->label('Momento')
                    ->options([
                        '1' => 'Momento 1',
                        '2' => 'Momento 2',
                        '3' => 'Momento 3',
                    ]),

                Tables\Filters\SelectFilter::make('etapa_productiva_id')
                    ->label('Aprendiz')
                    ->options(function () {
                        return EtapaProductiva::where('instructores_id', auth()->user()->instructor_id)
                            ->get()
                            ->mapWithKeys(function ($ep) {
                                return [$ep->id => "{$ep->nombre} {$ep->apellidos} - Ficha: {$ep->ficha->numero}"];
                            });
                    })
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\Action::make('ver')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (BitacoraUpload $record) => Storage::url($record->file_path))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('aceptar')
                    ->label('Aceptar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (BitacoraUpload $record) => $record->estado_revision === 'pendiente')
                    ->action(function (BitacoraUpload $record) {
                        $this->aceptarBitacora($record);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Aceptar Bitácora')
                    ->modalDescription('¿Está seguro de que desea aceptar esta bitácora?'),

                Tables\Actions\Action::make('devolver')
                    ->label('Devolver')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (BitacoraUpload $record) => $record->estado_revision === 'pendiente')
                    ->form([
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones de devolución')
                            ->required()
                            ->rows(4)
                            ->placeholder('Explique qué debe corregir el aprendiz...'),
                    ])
                    ->action(function (BitacoraUpload $record, array $data) {
                        $this->devolverBitacora($record, $data['observaciones']);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('aceptar_seleccionadas')
                    ->label('Aceptar Seleccionadas')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            if ($record->estado_revision === 'pendiente') {
                                $this->aceptarBitacora($record);
                            }
                        }
                    })
                    ->requiresConfirmation(),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s'); // Auto-refresh
    }

    private function getBitacorasQuery(): Builder
    {
        return BitacoraUpload::query()
            ->whereHas('etapaProductiva', function (Builder $query) {
                $query->where('instructores_id', auth()->user()->instructor_id);
            })
            ->with(['etapaProductiva.ficha', 'uploadedBy', 'reviewedBy']);
    }

    public function aceptarBitacora(BitacoraUpload $bitacora): void
    {
        try {
            $bitacora->update([
                'estado_revision' => 'aceptado',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'observaciones_revision' => null,
            ]);

            // Notificar al aprendiz
            if ($bitacora->uploadedBy) {
                $bitacora->uploadedBy->notify(
                    new BitacoraRevisadaNotification($bitacora, 'aceptado')
                );
            }

            Notification::make()
                ->title('Bitácora aceptada')
                ->body("La bitácora #{$bitacora->numero_bitacora} ha sido aceptada.")
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Error al aceptar la bitácora: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function devolverBitacora(BitacoraUpload $bitacora, string $observaciones): void
    {
        try {
            $bitacora->update([
                'estado_revision' => 'devuelto',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'observaciones_revision' => $observaciones,
            ]);

            // Notificar al aprendiz
            if ($bitacora->uploadedBy) {
                $bitacora->uploadedBy->notify(
                    new BitacoraRevisadaNotification($bitacora, 'devuelto', $observaciones)
                );
            }

            Notification::make()
                ->title('Bitácora devuelta')
                ->body("La bitácora #{$bitacora->numero_bitacora} ha sido devuelta para correcciones.")
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Error al devolver la bitácora: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}