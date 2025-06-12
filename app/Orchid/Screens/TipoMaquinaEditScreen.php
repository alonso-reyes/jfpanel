<?php

namespace App\Orchid\Screens;

use App\Models\TipoMaquinaria;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Select;

class TipoMaquinaEditScreen extends Screen
{
    public $tipo_maquinaria;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(TipoMaquinaria $tipo_maquinaria): iterable
    {
        return [
            'tipo_maquinaria' => $tipo_maquinaria
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->tipo_maquinaria->exists ? 'Editar' : 'Agregar';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        $exists = $this->tipo_maquinaria->exists ? true : false;

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
                Input::make('tipo_maquinaria.nombre')
                    ->title('Tipo')
                    ->required(),

                Select::make('tipo_maquinaria.acarreo_agua')
                    ->title('Acarreo de agua')
                    ->options([
                        1 => 'Sí',
                        0 => 'No',
                    ])
                    ->required(),
            ])
        ];
    }

    public function createOrUpdate(Request $request)
    {
        $obraId = session('obra_id');

        if (!$obraId) {
            Alert::error('Error: No se ha seleccionado ninguna obra.');
            return redirect()->route('obra.select');
        }

        $nombre = $request->input('tipo_maquinaria.nombre');
        $tipo_maquinaria_id = $this->tipo_maquinaria->id ?? null;  // Si es un registro existente, obtenemos su ID

        // Verificar si ya existe otro operador con la misma clave_trabajador, pero que no sea el operador actual
        $existingTipoMaquinaria = TipoMaquinaria::where('nombre', $nombre)
            ->where('obra_id', $obraId)
            ->where('id', '!=', $tipo_maquinaria_id)  // Excluir el operador actual si estamos editando
            ->first();

        // Si ya existe otro operador con la misma clave_trabajador, mostramos un error
        if ($existingTipoMaquinaria) {
            Alert::error('La familia ya está registrada.');
            return back();
        }

        //$this->tipo_maquinaria->fill($request->get('tipo_maquinaria'))->save();
        $this->tipo_maquinaria->fill([
            ...$request->get('tipo_maquinaria'),
            'obra_id' => $obraId,
        ])->save();

        Alert::info('Familia agregada con éxito');

        return redirect()->route('platform.tipo.maquinaria.list');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove()
    {
        $this->tipo_maquinaria->delete();

        Alert::info('Familia eliminada');

        return redirect()->route('platform.tipo.maquinaria.list');
    }
}
