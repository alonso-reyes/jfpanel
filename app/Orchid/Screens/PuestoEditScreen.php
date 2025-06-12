<?php

namespace App\Orchid\Screens;

use App\Models\Puesto;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class PuestoEditScreen extends Screen
{
    public $puesto;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Puesto $puesto): iterable
    {
        return [
            'puesto' => $puesto
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->puesto->exists ? 'Editar' : 'Agregar';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        $exists = $this->puesto->exists ? true : false;

        return [
            Button::make($exists ? 'Editar' : 'Agregar')
                ->icon($exists ? 'pencil' : 'plus')
                ->method('createOrUpdate')
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
            Layout::rows([
                Input::make('puesto.puesto')
                    ->title('Puesto')
                    ->required(),
            ])
        ];
    }

    public function createOrUpdate(Request $request)
    {
        $obraId = session('obra_id');

        if (!$obraId) {
            Alert::error('Error: No se ha seleccionado ningun puesto.');
            return redirect()->route('platform.puesto.list');
        }

        //$this->turno->fill($request->get('turno'))->save();
        $this->puesto->fill([
            ...$request->get('puesto'),
            'obra_id' => $obraId,
        ])->save();

        Alert::info('puesto agregado con éxito');

        return redirect()->route('platform.puesto.list');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove()
    {
        $this->puesto->delete();

        Alert::info('puesto eliminado');

        return redirect()->route('platform.puesto.list');
    }
}
