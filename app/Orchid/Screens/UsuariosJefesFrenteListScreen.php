<?php

namespace App\Orchid\Screens;

use App\Models\UsuariosJefeFrente;
use App\Orchid\Layouts\UsuariosJefesFrenteListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class UsuariosJefesFrenteListScreen extends Screen
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
            'usuariosJF' => UsuariosJefeFrente::where('obra_id', $obraId)->get()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Jefes de frente';
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
            ->route('platform.usuarios.jefes.frente.edit')
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
            UsuariosJefesFrenteListLayout::class
        ];
    }
}
