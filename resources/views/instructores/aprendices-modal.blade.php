<div class="space-y-4">
    @forelse($aprendices as $aprendiz)
        <div class="border p-3 rounded-md shadow-sm">
            <p><strong>Nombre:</strong> {{ $aprendiz->nombre }} {{ $aprendiz->apellidos }}</p>
            <p><strong>Documento:</strong> {{ $aprendiz->numero_documento }}</p>
            <p><strong>Correo:</strong> {{ $aprendiz->correo }}</p>
            <p><strong>Ficha:</strong> {{ $aprendiz->fichas->numero ?? 'N/A' }}</p>
            <p><strong>Programa de Formacion:</strong> {{ $aprendiz->fichas->programa_formacion ?? 'N/A' }}</p>
        </div>
    @empty
        <p>No hay aprendices asignados.</p>
    @endforelse

    
</div>
