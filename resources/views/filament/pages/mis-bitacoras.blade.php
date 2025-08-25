<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Formulario de Subida -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Subir Nueva Bitácora</h2>
            
            <form wire:submit.prevent="subirBitacora">
                {{ $this->form }}
                
                <div class="mt-6">
                    <x-filament::button type="submit" size="lg">
                        <x-heroicon-o-arrow-up-tray class="w-5 h-5 mr-2" />
                        Subir Bitácora
                    </x-filament::button>
                </div>
            </form>
        </div>

        <!-- Tabla de Bitácoras -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Mis Bitácoras Subidas</h2>
            </div>
            
            <div class="p-6">
                {{ $this->table }}
            </div>
        </div>
    </div>

    <!-- Información del Aprendiz -->
    @if(auth()->user()->etapaProductiva)
    <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-start">
            <x-heroicon-o-information-circle class="w-5 h-5 text-green-500 mr-3 mt-0.5" />
            <div>
                <h3 class="text-sm font-medium text-green-800">Información de tu Etapa Productiva</h3>
                <div class="mt-2 text-sm text-green-700">
                    <p><strong>Ficha:</strong> {{ auth()->user()->etapaProductiva->ficha->numero }}</p>
                    <p><strong>Programa:</strong> {{ auth()->user()->etapaProductiva->ficha->programa_formacion }}</p>
                    @if(auth()->user()->etapaProductiva->instructor)
                        <p><strong>Instructor:</strong> {{ auth()->user()->etapaProductiva->instructor->nombre_completo }}</p>
                    @endif
                    <p><strong>Momento Actual:</strong> {{ auth()->user()->etapaProductiva->momentos }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</x-filament-panels::page>