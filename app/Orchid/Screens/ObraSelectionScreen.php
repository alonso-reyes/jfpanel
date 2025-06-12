<?php

namespace App\Orchid\Screens;

use App\Models\Obra;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class ObraSelectionScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public $name = 'Selecciona una Obra';
    public $description = 'Elige una obra para continuar trabajando';

    public function query(): iterable
    {
        return [
            'obras' => Obra::all()->pluck('nombre', 'id')->toArray(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'ObraSelectionScreen';
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
        return [
            Layout::rows([
                Select::make('obra_id')
                    ->options(fn() => Obra::pluck('nombre', 'id'))
                    ->title('Seleccionar Obra')
                    ->required(),
                Button::make('Continuar')
                    ->method('selectObra'),
                Link::make('Crear Nueva Obra')
                    ->route('platform.obra.create'),
            ]),
        ];
    }

    public function selectObra($request)
    {
        $obraId = $request->get('obra_id');
        session(['obra_id' => $obraId]);

        Alert::info('Obra seleccionada correctamente.');
        return redirect()->route('platform.main');
    }
}
