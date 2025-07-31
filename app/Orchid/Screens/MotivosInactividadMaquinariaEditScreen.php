<?php

namespace App\Orchid\Screens;

use App\Models\MotivoInactividad;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class MotivosInactividadMaquinariaEditScreen extends Screen
{
    public $motivo_inactividad;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(MotivoInactividad $motivo_inactividad): iterable
    {
        return [
            'motivo_inactividad' => $motivo_inactividad
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->motivo_inactividad->exists ? 'Editar' : 'Agregar';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        $exists = $this->motivo_inactividad->exists ? true : false;

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
                Input::make('motivo_inactividad.motivo_inactividad')
                    ->title('Motivo de inactividad')
                    ->required(),
            ])
        ];
    }

    public function createOrUpdate(Request $request)
    {
        $obraId = session('obra_id');

        if (!$obraId) {
            Alert::error('Error: No se ha seleccionado ningun registro.');
            return redirect()->route('platform.motivo.inactividad.list');
        }

        //$this->turno->fill($request->get('turno'))->save();
        $this->motivo_inactividad->fill([
            ...$request->get('motivo_inactividad'),
            'obra_id' => $obraId,
        ])->save();

        Alert::info('Registro agregado con éxito');

        return redirect()->route('platform.motivo.inactividad.list');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove()
    {
        $this->motivo_inactividad->delete();

        Alert::info('Registro eliminado');

        return redirect()->route('platform.motivo.inactividad.list');
    }
}
