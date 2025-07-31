<?php

namespace App\Orchid\Screens;

use App\Models\ReporteMaquinaria;
use App\Orchid\Layouts\ReporteListlayout;
use App\Orchid\Layouts\ReporteMaquinariaListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;


class ReporteMaquinariaListScreen extends Screen
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
            'reportes_maquinaria' => ReporteMaquinaria::byObra(session('obra_id'))->get()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Reporte de maquinaria';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [ReporteMaquinariaListLayout::class];
    }
}
