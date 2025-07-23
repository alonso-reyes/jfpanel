<?php

namespace App\Orchid\Screens;

use App\Models\AcarreoVolumen;
use App\Models\Conceptos;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class AcarreoVolumenEditScreen extends Screen
{
    public $acarreovolumen;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(AcarreoVolumen $acarreovolumen): iterable
    {
        return [
            'acarreovolumen' => $acarreovolumen
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Volumen';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        $exists = $this->acarreovolumen->exists ? true : false;

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
        $obraId = session('obra_id');

        // Obtener conceptos con su factor de abundamiento para JavaScript
        $conceptos = Conceptos::where('tipo', 'CAPITULO')
            ->where('obra_id', $obraId)
            ->orderBy('descripcion')
            ->get(['id', 'descripcion', 'factor_abundamiento'])
            ->keyBy('id');

        return [
            Layout::rows([
                Input::make('acarreovolumen.volumen')
                    ->type('number')
                    ->step('0.01')
                    ->title('Volumen suelto (m³)')
                    ->required()
                    ->id('volumen-suelto'),

                Select::make('acarreovolumen.concepto_id')
                    ->title('Concepto')
                    ->empty('Seleccione un concepto')
                    ->options($conceptos->pluck('descripcion', 'id')->toArray())
                    ->required()
                    ->help('Seleccione el concepto para aplicar el factor de abundamiento.')
                    ->id('concepto-select'),

                Input::make('acarreovolumen.factor_abundamiento')
                    ->title('Factor de abundamiento')
                    ->readonly()
                    ->id('factor-abundamiento'),

                Input::make('acarreovolumen.volumen_compactado')
                    ->title('Volumen compactado (m³)')
                    ->readonly()
                    ->id('volumen-compactado'),
            ]),

            // Pasar los datos de conceptos a JavaScript
            Layout::view('screens.volumen-js', [
                'conceptos' => $conceptos->toJson()
            ]),
        ];
    }

    public function createOrUpdate(Request $request)
    {
        $obraId = session('obra_id');

        if (!$obraId) {
            Alert::error('Error: No se ha seleccionado ninguna obra.');
            return redirect()->route('obra.select');
        }

        $id = $this->acarreovolumen->id ?? null;  // Si es un registro existente, obtenemos su ID

        $this->acarreovolumen->fill([
            ...$request->get('acarreovolumen'),
            'obra_id' => $obraId,
        ])->save();

        Alert::info('Registro actualizado con éxito');

        return redirect()->route('platform.reportes.edit', ['reporte' => $id]);
    }
}
