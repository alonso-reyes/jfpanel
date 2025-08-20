<?php

namespace App\Orchid\Screens;

use App\Models\Maquinaria;
use App\Orchid\Layouts\MaquinariaInactivaListLayout;
use App\Orchid\Layouts\ReporteDateFilterLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class MaquinariaInactivaListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'maquinarias' => Maquinaria::where('obra_id', session('obra_id'))
                ->inactivas()
                ->orderBy('numero_economico')
                ->get()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Maquinaria Inactiva';
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
                ->route('exportar.lista.maquinaria.inactiva', ['obraId' => session('obra_id')])
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
            MaquinariaInactivaListLayout::class
        ];
    }

    public function redirectToExcel()
    {
        $fecha_inicio = request()->input('fecha_inicio');
        $fecha_termino   = request()->input('fecha_termino');
        $obraId = session('obra_id');

        return redirect()->route('exportar.reporte.maquinaria.inactiva', [
            'obraId' => $obraId,
            'fecha_inicio' => $fecha_inicio,
            'fecha_termino' => $fecha_termino,
        ]);
    }
}
