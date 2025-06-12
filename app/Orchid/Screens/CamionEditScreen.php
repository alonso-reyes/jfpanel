<?php

namespace App\Orchid\Screens;

use App\Models\Camion;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class CamionEditScreen extends Screen
{
    public $camion;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Camion $camion): iterable
    {
        return [
            'camion' => $camion
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->camion->exists ? 'Editar' : 'Agregar';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        $exists = $this->camion->exists ? true : false;
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
                Input::make('camion.clave')
                    ->title('Clave'),

                Input::make('camion.tipo')
                    ->title('Tipo'),
                    
                Input::make('camion.largo')
                    ->type('number')
                    ->title('Largo')
                    ->step(0.01),
                
                Input::make('camion.ancho')
                    ->type('number')
                    ->title('Ancho')
                    ->step(0.01),
                
                Input::make('camion.altura')
                    ->type('number')
                    ->title('Altura')
                    ->step(0.01),
                
                Input::make('camion.capacidad')
                    ->type('number')
                    ->title('Capacidad')
                    ->step(0.01),

                Input::make('camion.inspeccion_mecanica')
                    ->title('Inspección mecánica'),

                Input::make('camion.propietario')
                    ->title('Propietario'),

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

        // Validar datos
        $validatedData = $request->validate([
            'camion.clave' => 'required|string|max:255',
            'camion.tipo' => 'nullable|string|max:255',
            'camion.largo' => 'nullable|numeric|min:0',
            'camion.ancho' => 'nullable|numeric|min:0',
            'camion.altura' => 'nullable|numeric|min:0',
            'camion.capacidad' => 'nullable|numeric|min:0',
            'camion.inspeccion_mecanica' => 'nullable|string|max:255',
            'camion.propietario' => 'nullable|string|max:255',
        ]);

        $clave = $validatedData['camion']['clave'];
        $clave_id = $this->camion->id ?? null;

        // Verificar si ya existe un camión con la misma clave en la misma obra
        $repetead = Camion::where('clave', $clave)
                          ->where('obra_id', $obraId)
                          ->where('id', '!=', $clave_id ?? 0)
                          ->first();

        if ($repetead) {
            Alert::error('El camión ya está registrado en esta obra.');
            return back();
        }

        // Si estamos editando, usamos la instancia existente, si no, creamos una nueva
        $camion = $this->camion->exists ? $this->camion : new Camion();

        $camion->fill([
            ...$validatedData['camion'],
            'obra_id' => $obraId,
        ])->save();

        Alert::info($this->camion->exists ? 'Camión actualizado con éxito.' : 'Camión agregado con éxito.');

        return redirect()->route('platform.camion.list');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove()
    {
        $this->camion->delete();

        Alert::info('Camion eliminado');

        return redirect()->route('platform.camion.list');
    }
}
