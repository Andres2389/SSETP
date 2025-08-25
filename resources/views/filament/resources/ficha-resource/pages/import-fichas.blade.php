<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Instrucciones -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <x-heroicon-o-information-circle class="w-5 h-5 text-blue-500 mr-3 mt-0.5" />
                <div>
                    <h3 class="text-sm font-medium text-blue-800">Importar Reporte de Ficha</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Sube un archivo Excel con el reporte de aprendices de la ficha.</p>
                        <p class="mt-1"><strong>El proceso creará:</strong></p>
                        <ul class="list-disc ml-5 mt-1">
                            <li>El registro de la ficha (si no existe)</li>
                            <li>Los registros de Etapa Productiva para cada aprendiz</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de Importación -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Formulario de Importación</h2>
            </div>
            
            <div class="p-6">
                <form wire:submit.prevent="import">
                    {{ $this->form }}
                    
                    <div class="mt-6 flex justify-end space-x-3">
                        <x-filament::button 
                            color="gray" 
                            tag="a" 
                            href="{{ \App\Filament\Resources\FichaResource::getUrl('index') }}"
                        >
                            Cancelar
                        </x-filament::button>
                        
                        <x-filament::button type="submit" color="success">
                            <x-heroicon-o-arrow-up-tray class="w-4 h-4 mr-2" />
                            Importar
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Formato Esperado -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <h3 class="text-sm font-medium text-gray-900 mb-3">Formato Esperado del Excel</h3>
            <div class="text-sm text-gray-600">
                <p class="mb-2">El archivo Excel debe contener las siguientes columnas (nombres exactos):</p>
                <div class="grid grid-cols-2 gap-4">
                    <ul class="space-y-1">
                        <li>• <code class="bg-gray-200 px-1 rounded">numero_documento</code> (obligatorio)</li>
                        <li>• <code class="bg-gray-200 px-1 rounded">tipo_documento</code> (CC, TI, CE)</li>
                        <li>• <code class="bg-gray-200 px-1 rounded">nombre</code> (obligatorio)</li>
                        <li>• <code class="bg-gray-200 px-1 rounded">apellidos</code> (obligatorio)</li>
                    </ul>
                    <ul class="space-y-1">
                        <li>• <code class="bg-gray-200 px-1 rounded">celular</code> (opcional)</li>
                        <li>• <code class="bg-gray-200 px-1 rounded">correo</code> (opcional)</li>
                        <li>• <code class="bg-gray-200 px-1 rounded">estado_sofia</code> (opcional)</li>
                        <li>• <code class="bg-gray-200 px-1 rounded">fecha_inicio_ep</code> (opcional)</li>
                    </ul>
                </div>
                <p class="mt-3 text-xs text-gray-500">
                    Nota: La primera fila debe contener los nombres de las columnas.
                </p>
            </div>
        </div>
    </div>
</x-filament-panels::page>