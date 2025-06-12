<?php

namespace App\Orchid\Screens;

use App\Models\Turno;
use App\Orchid\Layouts\TurnoListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class TurnoListScreen extends Screen
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
            //'turnos' => Turno::paginate()
            'turnos' => Turno::where('obra_id', $obraId)->get()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Turnos';
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
                ->route('platform.turno.edit')
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
            TurnoListLayout::class
        ];
    }

    public function delete(Turno $turno)
    {
        $turno->delete();
    }
}
