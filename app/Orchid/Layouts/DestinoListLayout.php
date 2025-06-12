<?php

namespace App\Orchid\Layouts;

use App\Models\Destino;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class DestinoListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'destinos';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('destino', 'Destino'),

            TD::make('')
                ->alignRight()
                ->render(function (Destino $destino) {
                    return
                        '<div style="display: inline-flex; gap: 5px;">' .
                        Link::make('')
                        ->icon('pencil')
                        ->route('platform.destino.edit', $destino)
                        ->render() .
                        Button::make('')
                        ->icon('trash')
                        ->confirm('¿Desea eliminar este destino?')
                        ->method('delete', ['destino' => $destino->id])
                        .
                        '</div>';
                }),
        ];
    }
}
