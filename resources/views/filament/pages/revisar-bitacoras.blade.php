<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Información del Instructor -->
        @if(auth()->user()->instructor)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <x-heroicon-o-information-circle class="w-5 h-5 text-blue-500 mr-3 mt-0.5" />
                <div>
                    <h3 class="text-sm font-medium text-blue-800">Panel de Revisión de Bitácoras</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Aquí puedes revisar las bitácoras de tus aprendices asignados.</p>
                        <p><strong>Instructor:</strong> {{ auth()->user()->instructor->nombre_completo }}</p>
                        <p><strong>Aprendices Asignados:</strong> {{ auth()->user()->instructor->aprendices_asignados_count }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Tabla de Bitácoras para Revisar -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Bitácoras para Revisar</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Revisa, acepta o devuelve las bitácoras de tus aprendices asignados.
                </p>
            </div>
            
            <div class="p-6">
                {{ $this->table }}
            </div>
        </div>

        <!-- Instrucciones -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <h3 class="text-sm font-medium text-gray-900 mb-2">Instrucciones de Revisión</h3>
            <ul class="text-sm text-gray-600 space-y-1">
                <li>• <strong>Ver:</strong> Haz clic en el botón "Ver" para abrir el archivo de la bitácora.</li>
                <li>• <strong>Aceptar:</strong> Si la bitácora cumple con todos los requisitos, acéptala.</li>
                <li>• <strong>Devolver:</strong> Si necesita correcciones, devuélvela con observaciones específicas.</li>
                <li>• Las notificaciones se envían automáticamente al aprendiz cuando revisas una bitácora.</li>
            </ul>
        </div>
    </div>
</x-filament-panels::page>