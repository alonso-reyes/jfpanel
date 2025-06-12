<?php

namespace App\Orchid\Screens;

use App\Models\Material;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class MaterialEditScreen extends Screen
{
    public $material;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Material $material): iterable
    {
        return [
            'material' => $material
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->material->exists ? 'Editar' : 'Agregar';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        $exists = $this->material->exists ? true : false;

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
                Input::make('material.material')
                    ->title('Material')
                    ->required(),
            ])
        ];
    }

    public function createOrUpdate(Request $request)
    {
        $obraId = session('obra_id');

        if (!$obraId) {
            Alert::error('Error: No se ha seleccionado ningun material.');
            return redirect()->route('platform.material.list');
        }

        //$this->turno->fill($request->get('turno'))->save();
        $this->material->fill([
            ...$request->get('material'),
            'obra_id' => $obraId,
        ])->save();

        Alert::info('Material agregado con éxito');

        return redirect()->route('platform.material.list');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove()
    {
        $this->material->delete();

        Alert::info('Material eliminado');

        return redirect()->route('platform.material.list');
    }
}
