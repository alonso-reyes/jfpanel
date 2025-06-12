<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ReporteListlayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'reportes';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('id', 'ID')
                ->sort()
                ->filter(TD::FILTER_TEXT),

            TD::make('usuario_jefe_frente.nombre', 'Jefe de Frente')
                ->render(function ($reporte) {
                    // Verifica primero si la relación existe
                    if ($reporte->usuario_jefe_frente) {
                        return $reporte->usuario_jefe_frente->nombre;
                    }
                    return 'N/A';
                }),

            TD::make('turno.nombre_turno', 'Turno')
                ->render(function ($reporte) {
                    // Verifica primero si la relación existe
                    if ($reporte->turno) {
                        return $reporte->turno->nombre_turno;
                    }
                    return 'N/A';
                }),

            TD::make('hora_inicio_real_actividades', 'Inicio Actividades')
                ->sort()
                ->render(function ($reporte) {
                    return $reporte->hora_inicio_real_actividades;
                }),

            TD::make('hora_termino_real_actividades', 'Término Actividades')
                ->sort()
                ->render(function ($reporte) {
                    return $reporte->hora_termino_real_actividades;
                }),

            TD::make('zonaTrabajo.nombre', 'Zona de Trabajo')
                ->render(function ($reporte) {
                    return $reporte->zonaTrabajo->nombre ?? 'N/A';
                }),

            TD::make('obra.nombre', 'Obra')
                ->render(function ($reporte) {
                    return $reporte->obra->nombre ?? 'N/A';
                }),

            TD::make('sobrestante', 'Sobrestante'),

            TD::make('observaciones', 'Observaciones'),

            TD::make('acarreos_volumen', 'Acarreos Volumen')
                ->render(function ($reporte) {
                    return $reporte->acarreosVolumen->count();
                }),

            TD::make('acarreos_area', 'Acarreos Área')
                ->render(function ($reporte) {
                    return $reporte->acarreosArea->count();
                }),

            TD::make('acarreos_metro_lineal', 'Acarreos Metro Lineal')
                ->render(function ($reporte) {
                    return $reporte->acarreosMetroLineal->count();
                }),

            TD::make('acarreos_agua', 'Acarreos Agua')
                ->render(function ($reporte) {
                    return $reporte->acarreosAgua->count();
                }),

            TD::make('fotografias', 'Fotografías')
                ->render(function ($reporte) {
                    return $reporte->fotografias->count();
                }),

            TD::make('created_at', 'Creado')
                ->sort()
                ->render(function ($reporte) {
                    return $reporte->created_at->format('d/m/Y H:i');
                }),

            TD::make('actions', 'Acciones')
                ->alignRight()
                ->render(function ($reporte) {
                    return '<div style="display: inline-flex; gap: 5px;">' .
                        Link::make('')
                        ->icon('file-text')
                        ->route('platform.reporte.pdf', $reporte->id)
                        ->target('_blank')
                        ->render() .
                        '</div>';
                }),
        ];
    }
}
