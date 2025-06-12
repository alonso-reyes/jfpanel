<?php

namespace App\Orchid\Layouts;

use App\Models\UsuariosJefeFrente;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class UsuariosJefesFrenteListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'usuariosJF';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('usuario', 'Usuario'),
            TD::make('password', 'Contraseña'),
            TD::make('nombre', 'Nombre'),

            TD::make('')
                ->alignRight()
                ->render(function (UsuariosJefeFrente $usuariosJefeFrente) {
                return
                    '<div style="display: inline-flex; gap: 5px;">' .
                        Link::make('')
                        ->icon('pencil')
                        ->route('platform.usuarios.jefes.frente.edit', $usuariosJefeFrente)
                        ->render() .
                        Button::make('')
                            ->icon('trash')
                            ->confirm('¿Desea eliminar este usuario?')
                            ->method('delete', ['usuariojf' => $usuariosJefeFrente->id])
                             .
                    '</div>';
            }),
        ];
    }
}
