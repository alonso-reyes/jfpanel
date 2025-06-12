<?php

namespace App\Orchid\Screens;

use App\Models\MaterialUso;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class UsoMaterialEditScreen extends Screen
{
    public $uso;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(MaterialUso $uso): iterable
    {
        //dd($uso);

        return [
            'uso' => $uso
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->uso->exists ? 'Editar' : 'Agregar';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        $exists = $this->uso->exists ? true : false;

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
                Input::make('uso.uso')
                    ->title('Uso del material')
                    ->required(),
            ])
        ];
    }

    public function createOrUpdate(Request $request)
    {
        $obraId = session('obra_id');

        if (!$obraId) {
            Alert::error('Error: No se ha seleccionado ningun material.');
            return redirect()->route('platform.uso.material.list');
        }

        //$this->turno->fill($request->get('turno'))->save();
        $this->uso->fill([
            ...$request->get('uso'),
            'obra_id' => $obraId,
        ])->save();

        Alert::info('Uso de material agregado con éxito');

        return redirect()->route('platform.uso.material.list');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove()
    {
        $this->uso->delete();

        Alert::info('Uso de material eliminado');

        return redirect()->route('platform.uso.material.list');
    }
}
