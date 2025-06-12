<?php

namespace App\Orchid\Screens;

use App\Models\Operador;
use App\Models\TipoMaquinaria;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class OperadoresEditScreen extends Screen
{
    public $operador;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Operador $operador): iterable
    {
        return [
            'operador' => $operador
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        //$obraId = session('obra_id');return $obraId;
        return $this->operador->exists ? 'Editar' : 'Agregar';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        $exists = $this->operador->exists ? true : false;

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
                Input::make('operador.clave_trabajador')
                    ->title('Clave')
                    ->required(),

                Input::make('operador.nombre')
                    ->title('Nombre completo')
                    ->required(),

                Select::make('operador.tipos_maquinaria')  // Este será el campo que almacene la relación
                    ->title('Tipos de maquinaria')  // Título del campo
                    ->multiple()  // Permite seleccionar múltiples tipos
                    //->options($this->getTiposMaquinaria())  // Obtener las opciones de tipos de maquinaria
                    ->options($this->getTiposMaquinaria())  // Llamar al método que retorna las opciones
                    ->value($this->operador->tiposMaquinaria->pluck('id')->toArray())
                    ->required()
            ])
        ];
    }

    public function createOrUpdate(Request $request)
    {
        // Validar los tipos de maquinaria seleccionados
        // $request->validate([
        //     'operador.tipos_maquinaria' => 'array|exists:tipos_maquinaria,id', // Validar que los tipos seleccionados existan en la tabla 'tipos_maquinaria'
        // ]);

        // // Crear o actualizar el operador
        // $operador = Operador::updateOrCreate(
        //     ['id' => $request->get('operador.id')],  // Buscar el operador por ID, o crear uno nuevo si no existe
        //     $request->get('operador') // Llenar los datos del operador
        // );

        // // Sincronizar los tipos de maquinaria seleccionados con el operador
        // if ($request->has('operador.tipos_maquinaria')) {
        //     $operador->tiposMaquinaria()->sync($request->input('operador.tipos_maquinaria'));
        // }

        $obraId = session('obra_id');
        //dd($obraId);return;
        if (!$obraId) {
            Alert::error('Error: No se ha seleccionado ninguna obra.');
            return redirect()->route('obra.select');
        }

        $claveTrabajador = $request->input('operador.clave_trabajador');
        $operadorId = $this->operador->id ?? null;  // Si es un registro existente, obtenemos su ID

        // Verificar si ya existe otro operador con la misma clave_trabajador, pero que no sea el operador actual
        $existingOperador = Operador::where('clave_trabajador', $claveTrabajador)
            ->where('obra_id', $obraId)
            ->where('id', '!=', $operadorId)  // Excluir el operador actual si estamos editando
            ->first();

        // Si ya existe otro operador con la misma clave_trabajador, mostramos un error
        if ($existingOperador) {
            Alert::error('La clave del trabajador ya está registrada.');
            return back();
        }

        // Si no hay duplicado, se guarda o actualiza el operador
        //$this->operador->fill($request->get('operador'))->save();
        $this->operador->fill([
            ...$request->get('operador'),
            'obra_id' => $obraId,
        ])->save();

        // Guardar la relación con los tipos de maquinaria
        $this->operador->tiposMaquinaria()->sync($request->input('operador.tipos_maquinaria'));

        Alert::info('Operador actualizado con éxito');

        return redirect()->route('platform.operador.list');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove()
    {
        $this->operador->delete();

        Alert::info('Operador eliminado');

        return redirect()->route('platform.operador.list');
    }

    public function getTiposMaquinaria()
    {
        // Obtener todos los tipos de maquinaria
        //return \App\Models\TipoMaquinaria::pluck('nombre', 'id')->toArray();
        $obraId = session('obra_id');

        if (!$obraId) {
            return [];
        }

        // Filtrar tipos de maquinaria por obra_id
        return TipoMaquinaria::where('obra_id', $obraId)
            ->pluck('nombre', 'id') // Obtener un array ['id' => 'nombre']
            ->toArray();
    }
}
