<?php

namespace App\Orchid\Screens;

use App\Models\Destino;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class DestinoEditScreen extends Screen
{
    public $destino;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Destino $destino): iterable
    {
        return [
            'destino' => $destino
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->destino->exists ? 'Editar' : 'Agregar';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        $exists = $this->destino->exists ? true : false;

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
                Input::make('destino.destino')
                    ->title('Destino')
                    ->required(),
            ])
        ];
    }

    public function createOrUpdate(Request $request)
    {
        $obraId = session('obra_id');

        if (!$obraId) {
            Alert::error('Error: No se ha seleccionado ningun destino.');
            return redirect()->route('platform.destino.list');
        }

        //$this->turno->fill($request->get('turno'))->save();
        $this->destino->fill([
            ...$request->get('destino'),
            'obra_id' => $obraId,
        ])->save();

        Alert::info('Destino agregado con éxito');

        return redirect()->route('platform.destino.list');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove()
    {
        $this->destino->delete();

        Alert::info('Destino eliminado');

        return redirect()->route('platform.destino.list');
    }
}
