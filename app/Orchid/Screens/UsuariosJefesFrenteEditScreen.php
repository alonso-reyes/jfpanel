<?php

namespace App\Orchid\Screens;

use App\Models\UsuariosJefeFrente;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class UsuariosJefesFrenteEditScreen extends Screen
{
    public $usuariojf;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(UsuariosJefeFrente $usuariojf): iterable
    {
        return [
            'usuariojf' => $usuariojf
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->usuariojf->exists ? 'Editar' : 'Agregar';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {

        $exists = $this->usuariojf->exists ? true : false;

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
                Input::make('usuariojf.usuario')
                    ->title('Nombre del usuario')
                    ->required(),

                Input::make('usuariojf.password')
                    ->title('Contraseña')
                    ->required(),

                Input::make('usuariojf.nombre')
                    ->title('Nombre completo')
                    ->required(),

                Select::make('usuariojf.tipo_usuario')
                    ->title('Tipo de usuario')
                    ->options([
                        'JEFE DE FRENTE' => 'Jefe de Frente',
                        'SUPERINTENDENTE' => 'Superintendente',
                    ])
                    ->required()
                    ->help('Seleccione el tipo de usuario'),
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
        $this->usuariojf->fill([
            ...$request->get('usuariojf'),
            'obra_id' => $obraId,
        ])->save();

        Alert::info('Usuario creado con éxito');

        return redirect()->route('platform.usuarios.jefes.frente.list');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove()
    {
        $this->usuariojf->delete();

        Alert::info('Usuario eliminado');

        return redirect()->route('platform.usuarios.jefes.frente.list');
    }
}
