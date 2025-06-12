<?php

namespace App\Orchid\Screens;

use App\Models\TipoMaquinaria;
use App\Orchid\Layouts\TipoMaquinaListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class TipoMaquinaListScreen extends Screen
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
            'tipos_maquinaria' => TipoMaquinaria::where('obra_id', $obraId)->get()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Familia de maquinaria';
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
                ->route('platform.tipo.maquinaria.edit')
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
            TipoMaquinaListLayout::class
        ];
    }

    public function delete(TipoMaquinaria $tipo_maquinaria)
    {
        $tipo_maquinaria->delete();
    }
}
