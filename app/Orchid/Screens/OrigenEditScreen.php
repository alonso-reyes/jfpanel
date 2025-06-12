<?php

namespace App\Orchid\Screens;

use App\Models\Origen;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class OrigenEditScreen extends Screen
{
    public $origen;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Origen $origen): iterable
    {
        return [
            'origen' => $origen
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->origen->exists ? 'Editar' : 'Agregar';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        $exists = $this->origen->exists ? true : false;

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
                Input::make('origen.origen')
                    ->title('Origen')
                    ->required(),
            ])
        ];
    }

    public function createOrUpdate(Request $request)
    {
        $obraId = session('obra_id');

        if (!$obraId) {
            Alert::error('Error: No se ha seleccionado ningun origen.');
            return redirect()->route('platform.origen.list');
        }

        //$this->turno->fill($request->get('turno'))->save();
        $this->origen->fill([
            ...$request->get('origen'),
            'obra_id' => $obraId,
        ])->save();

        Alert::info('Origen agregado con éxito');

        return redirect()->route('platform.origen.list');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove()
    {
        $this->origen->delete();

        Alert::info('Origen eliminado');

        return redirect()->route('platform.origen.list');
    }
}
