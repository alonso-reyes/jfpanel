<?php

namespace App\Orchid\Layouts;

use App\Models\Camion;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class CamionListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'camiones';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('clave', 'Clave'),
            TD::make('tipo', 'Tipo'),
            TD::make('largo', 'Largo'),
            TD::make('ancho', 'Ancho'),
            TD::make('altura', 'Altura'),
            TD::make('capacidad', 'Capacidad'),
            TD::make('inspeccion_mecanica', 'Inspección mecánica'),
            TD::make('propietario', 'Propietario'),

            TD::make('')
                ->alignRight()
                ->render(function (Camion $camion) {
                return
                    '<div style="display: inline-flex; gap: 5px;">' .
                        Link::make('')
                        ->icon('pencil')
                        ->route('platform.camion.edit', $camion)
                        ->render() .
                        Button::make('')
                            ->icon('trash')
                            ->confirm('¿Desea eliminar este camión?')
                            ->method('delete', ['camion' => $camion->id])
                             .
                    '</div>';
            }),
        ];
    }
}
