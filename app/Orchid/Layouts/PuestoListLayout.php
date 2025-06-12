<?php

namespace App\Orchid\Layouts;

use App\Models\Puesto;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class PuestoListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'puestos';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('puesto', 'Puesto'),

            TD::make('')
                ->alignRight()
                ->render(function (Puesto $puesto) {
                    return
                        '<div style="display: inline-flex; gap: 5px;">' .
                        Link::make('')
                        ->icon('pencil')
                        ->route('platform.puesto.edit', $puesto)
                        ->render() .
                        Button::make('')
                        ->icon('trash')
                        ->confirm('¿Desea eliminar este puesto?')
                        ->method('delete', ['puesto' => $puesto->id])
                        .
                        '</div>';
                }),
        ];
    }
}
