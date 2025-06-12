<?php

namespace App\Orchid\Layouts;

use App\Models\Maquinaria;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class MaquinariaListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'maquinarias';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('numero_economico', 'Número económico'),
            TD::make('modelo', 'Modelo'),
            TD::make('horometro_inicial', 'Horometro inicial'),
            TD::make('estado', 'Estado'),
            TD::make('inactividad', 'Inactividad'),
            // TD::make('observaciones_inactividad', 'Observaciones de inactividad'),
            // TD::make('observaciones', 'Observaciones'),
            TD::make('')
                ->alignRight()
                ->render(function (Maquinaria $maquinaria) {
                    return
                        '<div style="display: inline-flex; gap: 5px;">' .
                        Link::make('')
                        ->icon('pencil')
                        ->route('platform.maquinaria.edit', $maquinaria)
                        ->render() .
                        Button::make('')
                        ->icon('trash')
                        ->confirm('¿Desea eliminar esta maquinaria?')
                        ->method('delete', ['maquinaria' => $maquinaria->id])
                        .
                        '</div>';
                }),
        ];
    }
}
