<?php

namespace App\Orchid\Screens;

use App\Models\Horometro;
use App\Models\Maquinaria;
use App\Models\MotivoInactividad;
use App\Models\TipoMaquinaria;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class MaquinariaEditScreen extends Screen
{
    public $maquinaria;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Maquinaria $maquinaria): iterable
    {
        return [
            'maquinaria' => $maquinaria
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->maquinaria->exists ? 'Editar' : 'Agregar';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        $exists = $this->maquinaria->exists ? true : false;

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
                Input::make('maquinaria.numero_economico')
                    ->title('Número económico')
                    ->required(),

                // Input::make('maquinaria.nombre')
                //     ->title('Nombre')
                //     ->required(),

                Input::make('maquinaria.modelo')
                    ->title('Modelo')
                    ->required(),

                Select::make('maquinaria.tipo_maquinaria_id')
                    ->title('Tipo de maquinaria')
                    ->options($this->getTiposMaquinaria())
                    ->value($this->maquinaria->tipo_maquinaria_id ?? null)
                    ->required(),

                // Input::make('maquinaria.capacidad')
                //     ->title('Capacidad'),

                Input::make('maquinaria.horometro_inicial')
                    ->title('Horometro inicial')
                    ->required()
                    ->type('number')
                    ->step(0.01),

                // Select::make('maquinaria.estado')
                //     ->title('Estado de la máquina')
                //     ->options([
                //         '' => 'Seleccione',
                //         'activo' => 'Activa',
                //         'inactivo' => 'Inactiva',
                //     ])
                //     ->value($this->maquinaria->estado ?? 'activo')
                //     ->required()
                //     ->id('estado-select'),

                CheckBox::make('maquinaria.estado')
                    ->sendTrueOrFalse()
                    ->title('¿Está inactiva la máquina?')
                    ->placeholder('Marcar si la máquina está inactiva')
                    ->value(1)
                    ->checked(isset($this->maquinaria) && $this->maquinaria->estado === 'INACTIVO')
                    ->id('estado-inactivo-checkbox'),

                // Select::make('maquinaria.inactividad')  // Campo para el enum
                //     ->title('Motivo de inactividad')  // Título del campo
                //     ->options([
                //         'ninguna' => 'Ninguna',
                //         'mantenimiento' => 'En mantenimiento',
                //         'falta de operador' => 'Falta de operador',
                //         'falta de tramo' => 'Falta de tramo',
                //         'condiciones climaticas' => 'Condiciones climáticas',
                //     ])  // Opciones del enum
                //     ->value($this->maquinaria->inactividad ?? 'ninguna') // Valor por defecto o el actual
                //     //->disabled()
                //     ->id('inactividad-select'), // Agrega un ID al select

                Select::make('maquinaria.motivo_inactividad_id')
                    ->title('Motivo de inactividad')
                    ->options($this->getMotivosInactividad())
                    ->value($this->maquinaria->motivo_inactividad_id ?? null)
                    ->empty('Seleccione...')
                    ->id('motivo-select-wrapper')
            ]),

            Layout::view('screens.maquinaria-js'),
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

        $numeroEconomico = $request->input('maquinaria.numero_economico');
        $maquinariaId = $this->maquinaria->id ?? null;  // Si es un registro existente, obtenemos su ID

        // Verificar si ya existe otro operador con la misma clave_trabajador, pero que no sea el operador actual
        $existing = Maquinaria::where('numero_economico', $numeroEconomico)
            ->where('obra_id', $obraId)
            ->where('id', '!=', $maquinariaId)  // Excluir el operador actual si estamos editando
            ->first();

        // Si ya existe otro operador con la misma clave_trabajador, mostramos un error
        if ($existing) {
            Alert::error('La máquina ya está registrada.');
            return back();
        }

        // Si no hay duplicado, se guarda o actualiza el operador
        //$this->operador->fill($request->get('operador'))->save();
        $inactiva = filter_var($request->input('maquinaria.estado'), FILTER_VALIDATE_BOOLEAN);
        $estado = $inactiva ? 'INACTIVO' : 'ACTIVO';

        $motivoInactividadId = $inactiva
            ? $request->input('maquinaria.motivo_inactividad_id')
            : null;

        $this->maquinaria->fill([
            ...$request->get('maquinaria'),
            'estado' => $estado,
            'motivo_inactividad_id' => $motivoInactividadId,
            'obra_id' => $obraId,
        ])->save();

        // Agregar primer horometro a la tabla
        // Obtenemos el ID actualizado o nuevo
        $maquinariaId = $this->maquinaria->id;

        // Si no existe un horómetro registrado para esta maquinaria, lo creamos
        $horometroExistente = \App\Models\Horometro::where('maquinaria_id', $maquinariaId)->first();

        if (!$horometroExistente) {
            Horometro::create([
                'maquinaria_id' => $maquinariaId,
                'horometro_inicial' => $request->input('maquinaria.horometro_inicial'), // Asegúrate que este valor venga en el request
            ]);
        }

        Alert::info('Actualizado con éxito');

        return redirect()->route('platform.maquinaria.list');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove()
    {
        $this->maquinaria->delete();

        Alert::info('Maquinaria eliminado');

        return redirect()->route('platform.maquinaria.list');
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

    public function getMotivosInactividad()
    {
        $obraId = session('obra_id');

        if (!$obraId) {
            return [];
        }

        return MotivoInactividad::where('obra_id', $obraId)
            ->pluck('motivo_inactividad', 'id')
            ->toArray();
    }
}
