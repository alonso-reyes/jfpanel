<?php

namespace App\Orchid\Screens;

use App\Models\Personal;
use App\Models\Puesto;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class PersonalEditScreen extends Screen
{
    public $personal;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Personal $personal): iterable
    {
        return [
            'personal' => $personal
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->personal->exists ? 'Editar' : 'Agregar';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        $exists = $this->personal->exists ? true : false;

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
                Input::make('personal.nombre')
                    ->title('Nombre')
                    ->required(),

                Select::make('personal.puesto_id')
                    ->title('Puesto')
                    ->options($this->getPuestos())
                    ->value($this->personal->puesto_id ?? null)
                    ->required(),
            ])
        ];
    }

    public function createOrUpdate(Request $request)
    {
        $obraId = session('obra_id');
        //dd($obraId);return;
        if (!$obraId) {
            Alert::error('Error: No se ha seleccionado ninguna obra.');
            return redirect()->route('obra.select');
        }

        $nombre = $request->input('personal.nombre');
        $peronalId = $this->personal->id ?? null;  // Si es un registro existente, obtenemos su ID

        $existing = Personal::where('nombre', $nombre)
            ->where('obra_id', $obraId)
            ->where('id', '!=', $peronalId)  // Excluir el operador actual si estamos editando
            ->first();

        if ($existing) {
            Alert::error('La persona ya está registrada.');
            return back();
        }

        // Si no hay duplicado, se guarda o actualiza el operador
        $this->personal->fill([
            'nombre' => $request->input('personal.nombre'),
            'puesto_id' => $request->input('personal.puesto_id'),
            'obra_id' => $obraId,
        ])->save();

        Alert::info('Actualizado con éxito');

        return redirect()->route('platform.personal.list');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove()
    {
        $this->personal->delete();

        Alert::info('Personal eliminado');

        return redirect()->route('platform.personal.list');
    }


    public function getPuestos()
    {
        // Obtener todos los puestos
        $obraId = session('obra_id');

        if (!$obraId) {
            return [];
        }

        // Filtrar tipos de puesto por obra_id
        return Puesto::where('obra_id', $obraId)
            ->pluck('puesto', 'id') // Obtener un array ['id' => 'nombre']
            ->toArray();
    }
}
