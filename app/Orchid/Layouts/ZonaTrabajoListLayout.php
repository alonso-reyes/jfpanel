<?php

namespace App\Orchid\Layouts;

use App\Models\ZonaTrabajo;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ZonaTrabajoListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'zonas_trabajo';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('clave', 'Clave'),
            TD::make('nombre', 'Concepto'),
            TD::make('descripcion', 'Descripción'),
 
            TD::make('')
                ->alignRight()
                ->render(function (ZonaTrabajo $zona_trabajo) {
                return
                    '<div style="display: inline-flex; gap: 5px;">' .
                        Link::make('')
                        ->icon('pencil')
                        ->route('platform.zona.trabajo.edit', $zona_trabajo)
                        ->render() .
                        Button::make('')
                            ->icon('trash')
                            ->confirm('¿Desea eliminar esta zona de trabajo?')
                            ->method('delete', ['zona_trabajo' => $zona_trabajo->id])
                             .
                    '</div>';
            }),
        ];
    }
}
