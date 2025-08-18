<?php

namespace App\Orchid\Screens;

use App\Models\ReporteJefeFrente;
use App\Orchid\Layouts\ReporteListlayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\DateRange;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class ReporteListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $obraId = session('obra_id');
        $start = request('rango_fechas')['start'] ?? null;
        $end = request('rango_fechas')['end'] ?? null;

        $query = ReporteJefeFrente::with([
            'usuario_jefe_frente',
            'turno',
            'zonaTrabajo',
            'obra',
            'acarreosVolumen',
            'acarreosArea',
            'acarreosMetroLineal',
            'acarreosAgua',
            'fotografias'
        ])->where('obra_id', $obraId);

        if ($start && $end) {
            $query->whereBetween('fecha', [$start, $end]);
        }

        return [
            'reportes' => $query->get()
        ];

        /*return [
            'reportes' => ReporteJefeFrente::with([
                'usuario_jefe_frente',
                'turno',
                'zonaTrabajo',
                'obra',
                'acarreosVolumen',
                'acarreosArea',
                'acarreosMetroLineal',
                'acarreosAgua',
                'fotografias'
            ])->where('obra_id', $obraId)->get()
        ];*/
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Reportes';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Exportar a excel')
                ->icon('file-earmark-excel')
                ->route('exportar.acarreos', ['obraId' => session('obra_id')])
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            // Layout::rows([
            //     DateRange::make('rango_fechas')
            //         ->title('Rango de Fechas')
            //         ->placeholder('Seleccione un rango')
            //         ->value([
            //             'start' => request('rango_fechas.start'),
            //             'end'   => request('rango_fechas.end')
            //         ])
            //         ->id('date-range'), // le ponemos un ID para JS

            //     Link::make('Exportar a excel')
            //         ->icon('file-earmark-excel')
            //         ->route('exportar.acarreos', ['obraId' => session('obra_id')])
            //         ->id('export-link') // le ponemos un ID para JS
            // ]),
            ReporteListLayout::class,

            //Layout::view('reporte-excel-acarreos-js')
        ];
    }
}
