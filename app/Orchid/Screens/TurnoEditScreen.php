<?php

namespace App\Orchid\Screens;

use App\Models\Turno;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Components\Cells\Time;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class TurnoEditScreen extends Screen
{
    public $turno;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Turno $turno): iterable
    {
        return [
            'turno' => $turno
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->turno->exists ? 'Editar' : 'Agregar';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        $exists = $this->turno->exists ? true : false;

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
                Input::make('turno.nombre_turno')
                    ->title('Turno')
                    ->required(),

                DateTimer::make('turno.hora_entrada')
                    ->title('Inicio del turno')
                    ->noCalendar()
                    ->format('H:i')
                    ->required(),
    
                DateTimer::make('turno.hora_salida')
                    ->title('Final del turno')
                    ->noCalendar()
                    ->format('H:i')
                    ->required()
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

        //$this->turno->fill($request->get('turno'))->save();
        $this->turno->fill([
            ...$request->get('turno'),
            'obra_id' => $obraId, 
        ])->save();

        Alert::info('Turno agregado con éxito');

        return redirect()->route('platform.turno.list');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove()
    {
        $this->turno->delete();

        Alert::info('Turno eliminado');

        return redirect()->route('platform.turno.list');
    }
}
