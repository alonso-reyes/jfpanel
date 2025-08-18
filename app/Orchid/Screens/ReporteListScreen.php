<?php

namespace App\Orchid\Screens;

use App\Models\ReporteJefeFrente;
use App\Orchid\Layouts\ReporteDateFilterLayout;
use App\Orchid\Layouts\ReporteListlayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\DateRange;
use Orchid\Screen\Fields\DateTimer;
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

        return [
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
        ];
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
            // Link::make('Exportar a excel')
            //     ->icon('file-earmark-excel')
            //     ->route('exportar.acarreos', ['obraId' => session('obra_id')])
            // Button::make('Exportar a Excel')
            //     ->icon('file-earmark-excel')
            //     ->method('exportarExcel')
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
            ReporteDateFilterLayout::class,
            ReporteListLayout::class,
        ];
    }

    public function exportarExcel(Request $request)
    {
        $obraId = session('obra_id');
        $start = $request->input('rango_fechas.start');
        $end = $request->input('rango_fechas.end');

        return redirect()->route('exportar.acarreos', [
            'obraId' => $obraId,
            'start' => $start,
            'end' => $end,
        ]);
    }

    public function redirectToExcel()
    {
        $fecha_inicio = request()->input('fecha_inicio');
        $fecha_termino   = request()->input('fecha_termino');
        $obraId = session('obra_id');

        return redirect()->route('exportar.acarreos', [
            'obraId' => $obraId,
            'fecha_inicio' => $fecha_inicio,
            'fecha_termino' => $fecha_termino,
        ]);
    }
}
