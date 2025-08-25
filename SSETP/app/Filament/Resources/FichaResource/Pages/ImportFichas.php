<?php

namespace App\Filament\Resources\FichaResource\Pages;

use App\Filament\Resources\FichaResource;
use App\Imports\FichasImport; // 👈 Importar la clase correctamente
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Maatwebsite\Excel\Facades\Excel;

class ImportFichas extends Page
{
    protected static string $resource = FichaResource::class;
    protected static string $view = 'filament.resources.ficha-resource.pages.import-fichas';
    protected static ?string $title = 'Importar Reporte de Ficha';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Importar Reporte de Aprendices')
                    ->description('Sube un archivo Excel con el reporte de la ficha. Se creará la ficha y los registros de Etapa Productiva.')
                    ->schema([
                        Forms\Components\FileUpload::make('file')
                            ->label('Archivo Excel')
                            ->acceptedFileTypes(['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                            ->required()
                            ->disk('local')
                            ->directory('imports'),

                        Forms\Components\TextInput::make('numero_ficha')
                            ->label('Número de Ficha')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('programa_formacion')
                            ->label('Programa de Formación')
                            ->required()
                            ->maxLength(255),
                    ]),
            ])
            ->statePath('data');
    }

    public function import(): void
    {
        $data = $this->form->getState();

        if (!isset($data['file']) || empty($data['file'])) {
            Notification::make()
                ->title('Error')
                ->body('Debe seleccionar un archivo.')
                ->danger()
                ->send();
            return;
        }

        try {
            // Ruta real al archivo
            $filePath = storage_path('app/' . $data['file']);

            // Usar el import
            $import = new FichasImport($data['numero_ficha'], $data['programa_formacion']);
            Excel::import($import, $filePath);

            Notification::make()
                ->title('Importación completada')
                ->body('El archivo se procesó correctamente.')
                ->success()
                ->send();

            $this->redirect(FichaResource::getUrl('index'));
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error en la Importación')
                ->body('Error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('import')
                ->label('Importar')
                ->action('import')
                ->color('success'),
        ];
    }
}
