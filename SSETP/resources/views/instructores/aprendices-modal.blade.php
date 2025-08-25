<div class="space-y-4">
    @forelse($etapas as $etapa)
        <div class="border p-3 rounded-md shadow-sm">
            <p><strong>Nombre:</strong> {{ $etapa->nombre }} {{ $etapa->apellidos }}</p>
            <p><strong>Documento:</strong> {{ $etapa->numero_documento }}</p>
            <p><strong>Correo:</strong> {{ $etapa->correo }}</p>
            <p><strong>Ficha:</strong> {{ $etapa->ficha->numero ?? 'N/A' }}</p>
            <p><strong>Programa de Formacion:</strong> {{ $etapa->ficha->programa_formacion ?? 'N/A' }}</p>
        </div>
    @empty
        <p>No hay aprendices asignados.</p>
    @endforelse
</div>
