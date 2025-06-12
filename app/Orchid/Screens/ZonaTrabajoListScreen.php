<?php

namespace App\Orchid\Screens;

use App\Models\ZonaTrabajo;
use App\Orchid\Layouts\ExcelImportLayout;
use App\Orchid\Layouts\ZonaTrabajoListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class ZonaTrabajoListScreen extends Screen
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
            'zonas_trabajo' => ZonaTrabajo::where('obra_id', $obraId)->get()
        ];
    }

    /**6
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Zona de trabajo';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Agregar')
                ->icon('plus')
                ->route('platform.zona.trabajo.edit'),
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
            ZonaTrabajoListLayout::class,
        ];
    }

    public function delete(ZonaTrabajo $zona_trabajo)
    {
        $zona_trabajo->delete();
    }
}
