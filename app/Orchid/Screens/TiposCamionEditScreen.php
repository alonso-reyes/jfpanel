<?php

namespace App\Orchid\Screens;

use App\Models\CatalogoCamionAcarreo;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class TiposCamionEditScreen extends Screen
{
    public $tipo_camion;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(CatalogoCamionAcarreo $tipo_camion): iterable
    {
        return [
            'tipo_camion' => $tipo_camion
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->tipo_camion->exists ? 'Editar' : 'Agregar';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        $exists = $this->tipo_camion->exists ? true : false;

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
                Input::make('tipo_camion.nombre')
                    ->title('Tipo de camion')
                    ->required(),

                Input::make('tipo_camion.capacidad')
                    ->type('number')
                    ->title('Capacidad (m³)')
                    ->step(0.01),

                Input::make('tipo_camion.capacidad_tonelada')
                    ->type('number')
                    ->title('Cantidad (Toneladas)')
                    ->step(0.01),
            ])
        ];
    }

    public function createOrUpdate(Request $request)
    {
        $obraId = session('obra_id');

        if (!$obraId) {
            Alert::error('Error: No se ha seleccionado ningun tipo_camion.');
            return redirect()->route('platform.tipo.camion.list');
        }

        //$this->turno->fill($request->get('turno'))->save();
        $this->tipo_camion->fill([
            ...$request->get('tipo_camion'),
            'obra_id' => $obraId,
        ])->save();

        Alert::info('Registro agregado con éxito');

        return redirect()->route('platform.tipo.camion.list');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove()
    {
        $this->tipo_camion->delete();

        Alert::info('tipo_camion eliminado');

        return redirect()->route('platform.tipo.camion.list');
    }
}
