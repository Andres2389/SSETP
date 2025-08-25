<?php

namespace App\Filament\Pages;

use App\Models\BitacoraUpload;
use App\Models\EtapaProductiva;
use App\Notifications\BitacoraSubidaNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class MisBitacoras extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.mis-bitacoras';

    protected static ?string $navigationLabel = 'Mis Bitácoras';

    protected static ?string $title = 'Mis Bitácoras';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->isAprendiz() ?? false;
    }

    public function mount(): void
    {
        if (!auth()->user()?->isAprendiz()) {
            abort(403);
        }

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Subir Nueva Bitácora')
                    ->schema([
                        Forms\Components\Select::make('numero_bitacora')
                            ->label('Número de Bitácora')
                            ->options([
                                1 => 'Bitácora 1', 2 => 'Bitácora 2', 3 => 'Bitácora 3',
                                4 => 'Bitácora 4', 5 => 'Bitácora 5', 6 => 'Bitácora 6',
                                7 => 'Bitácora 7', 8 => 'Bitácora 8', 9 => 'Bitácora 9',
                                10 => 'Bitácora 10', 11 => 'Bitácora 11', 12 => 'Bitácora 12',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                // Verificar si ya existe esta bitácora
                                if ($state && auth()->user()->etapaProductiva) {
                                    $exists = BitacoraUpload::where('etapa_productiva_id', auth()->user()->etapa_productiva_id)
                                        ->where('numero_bitacora', $state)
                                        ->exists();

                                    if ($exists) {
                                        Notification::make()
                                            ->title('Bitácora ya existe')
                                            ->body("Ya has subido la bitácora número {$state}. Para reemplazarla, elimina la existente primero.")
                                            ->warning()
                                            ->send();

                                        $set('numero_bitacora', null);
                                    }
                                }
                            }),

                        Forms\Components\Select::make('momento')
                            ->label('Momento')
                            ->options([
                                '1' => 'Momento 1',
                                '2' => 'Momento 2',
                                '3' => 'Momento 3',
                            ])
                            ->required()
                            ->default(auth()->user()?->etapaProductiva?->momentos ?? '1'),

                        Forms\Components\FileUpload::make('archivo')
                            ->label('Archivo de Bitácora')
                            ->acceptedFileTypes(['application/pdf', 'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'image/jpeg', 'image/png'])
                            ->maxSize(10240) // 10MB
                            ->required()
                            ->disk('public')
                            ->directory('bitacoras/temp'),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getBitacorasQuery())
            ->columns([
                Tables\Columns\TextColumn::make('numero_bitacora')
                    ->label('Número')
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

                Tables\Columns\TextColumn::make('observaciones_revision')
                    ->label('Observaciones')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->observaciones_revision)
                    ->placeholder('Sin observaciones'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Subido')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado_revision')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'aceptado' => 'Aceptado',
                        'devuelto' => 'Devuelto',
                    ]),

                Tables\Filters\SelectFilter::make('momento')
                    ->label('Momento')
                    ->options([
                        '1' => 'Momento 1',
                        '2' => 'Momento 2',
                        '3' => 'Momento 3',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('descargar')
                    ->label('Descargar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (BitacoraUpload $record) => Storage::url($record->file_path))
                    ->openUrlInNewTab(),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn (BitacoraUpload $record) => $record->estado_revision === 'devuelto'),
            ])
            ->defaultSort('numero_bitacora')
            ->poll('30s'); // Auto-refresh every 30 seconds
    }

    private function getBitacorasQuery(): Builder
    {
        return BitacoraUpload::query()
            ->where('etapa_productiva_id', auth()->user()->etapa_productiva_id)
            ->with(['reviewedBy']);
    }

    public function subirBitacora(): void
    {
        $data = $this->form->getState();

        try {
            // Validar que no exista ya esta bitácora
            $existingBitacora = BitacoraUpload::where('etapa_productiva_id', auth()->user()->etapa_productiva_id)
                ->where('numero_bitacora', $data['numero_bitacora'])
                ->first();

            if ($existingBitacora) {
                Notification::make()
                    ->title('Error')
                    ->body('Ya existe una bitácora con este número. Para reemplazarla, elimina la existente primero.')
                    ->danger()
                    ->send();
                return;
            }

            // Obtener información del aprendiz
            $etapaProductiva = auth()->user()->etapaProductiva;

            // Mover archivo a ubicación final
            $tempPath = $data['archivo'];
            $filename = basename($tempPath);
            $finalPath = "bitacoras/{$etapaProductiva->ficha->numero}/{$etapaProductiva->numero_documento}/{$data['numero_bitacora']}/{$filename}";

            // Copiar archivo a la ubicación final
            Storage::disk('public')->copy($tempPath, $finalPath);

            // Crear registro en base de datos
            BitacoraUpload::create([
                'etapa_productiva_id' => auth()->user()->etapa_productiva_id,
                'numero_bitacora' => $data['numero_bitacora'],
                'momento' => $data['momento'],
                'file_path' => $finalPath,
                'file_name' => $filename,
                'estado_revision' => 'pendiente',
                'uploaded_by' => auth()->id(),
            ]);

            // Limpiar archivo temporal
            Storage::disk('public')->delete($tempPath);

            // Notificar al instructor
            if ($etapaProductiva->instructor && $etapaProductiva->instructor->user) {
                $etapaProductiva->instructor->user->notify(
                    new BitacoraSubidaNotification($etapaProductiva, $data['numero_bitacora'])
                );
            }

            // Limpiar formulario
            $this->form->fill();

            Notification::make()
                ->title('Bitácora subida exitosamente')
                ->body("La bitácora #{$data['numero_bitacora']} ha sido enviada para revisión.")
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al subir bitácora')
                ->body('Error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('subir')
                ->label('Subir Bitácora')
                ->action('subirBitacora')
                ->color('success')
                ->icon('heroicon-o-arrow-up-tray'),
        ];
    }
}
