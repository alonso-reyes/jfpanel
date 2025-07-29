<?php

namespace App\Orchid\Screens;

use App\Models\ReporteJefeFrente;
use App\Orchid\Layouts\ReporteListlayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

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
            Link::make('Exportar a excel')
                ->icon('cloud-download')
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
            ReporteListlayout::class
        ];
    }
}
