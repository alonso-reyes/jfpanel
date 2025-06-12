<?php

namespace App\Orchid\Screens;

use App\Models\Conceptos;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class ConceptoEditScreen extends Screen
{

    public $concepto;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Conceptos $concepto): iterable
    {
        return [
            'concepto' => $concepto
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->concepto->exists ? 'Editar' : 'Agregar';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        $exists = $this->concepto->exists ? true : false;
        return [
            // Button::make('Agregar')
            //     ->icon('pencil')
            //     ->method('createOrUpdate')
            //     ->canSee(!$this->concepto->exists),
           
                Button::make($exists ? 'Editar' : 'Agregar')
                ->icon($exists ? 'pencil' : 'plus')
                ->method('createOrUpdate')
                //->canSee($this->concepto->exists),

            // Button::make('Eliminar')
            //     ->icon('trash')
            //     ->method('remove')
            //     ->canSee($this->concepto->exists),
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
                Input::make('concepto.nombre')
                    ->title('Concepto'),
                    //->placeholder('Concepto')
                    //->help('Nombre del concepto'),

                TextArea::make('concepto.descripcion')
                    ->title('Descripción')
                    ->rows(3)
                    ->maxlength(200),
                    //->placeholder('Descripcion')
                Input::make('concepto.unidad')
                    ->title('Unidad'),
    
                Input::make('concepto.cantidad')
                    ->type('number')
                    ->title('Cantidad')
                    ->step(0.01)
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

        //$this->concepto->fill($request->get('concepto'))->save();
        //dd(session('obra_id'));
        $concepto_nombre = $request->input('concepto.nombre');
        $concepto_id = $this->concepto->id ?? null;  // Si es un registro existente, obtenemos su ID

        // Verificar si ya existe otro operador con la misma clave_trabajador, pero que no sea el operador actual
        $repetead = Conceptos::where('nombre', $concepto_nombre)
                                    ->where('obra_id', $obraId)
                                    ->where('id', '!=', $concepto_id)  // Excluir el operador actual si estamos editando
                                    ->first();

        // Si ya existe otro operador con la misma clave_trabajador, mostramos un error
        if ($repetead) {
            Alert::error('El concepto ya está registrado.');
            return back();
        }
        ///
        $this->concepto->fill([
            ...$request->get('concepto'),
            'obra_id' => $obraId, 
        ])->save();

        Alert::info('Concepto agregado con éxito');

        return redirect()->route('platform.concepto.list');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove()
    {
        $this->concepto->delete();

        Alert::info('Concepto eliminado');

        return redirect()->route('platform.concepto.list');
    }
}
