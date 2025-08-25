<?php

namespace App\Filament\Widgets;

use App\Models\BitacoraUpload;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentActivity extends BaseWidget
{
    protected static ?string $heading = 'Actividad Reciente';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getQuery())
            ->columns([
                Tables\Columns\TextColumn::make('etapaProductiva.nombre_completo')
                    ->label('Aprendiz')
                    ->limit(30),

                Tables\Columns\TextColumn::make('numero_bitacora')
                    ->label('Bitácora #'),

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
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('uploadedBy.name')
                    ->label('Usuario')
                    ->limit(20),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10]);
    }

    private function getQuery(): Builder
    {
        $user = auth()->user();
        
        $query = BitacoraUpload::with(['etapaProductiva', 'uploadedBy']);

        if ($user->isInstructor()) {
            // Solo bitácoras de aprendices asignados al instructor
            $query->whereHas('etapaProductiva', function (Builder $q) use ($user) {
                $q->where('instructores_id', $user->instructor_id);
            });
        } elseif ($user->isAprendiz()) {
            // Solo las bitácoras del propio aprendiz
            $query->where('etapa_productiva_id', $user->etapa_productiva_id);
        }
        
        return $query;
    }
}