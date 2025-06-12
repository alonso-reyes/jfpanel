<?php

namespace App\Orchid\Layouts;

use App\Models\Origen;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class OrigenListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'origenes';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('origen', 'Origen'),

            TD::make('')
                ->alignRight()
                ->render(function (Origen $origen) {
                    return
                        '<div style="display: inline-flex; gap: 5px;">' .
                        Link::make('')
                        ->icon('pencil')
                        ->route('platform.origen.edit', $origen)
                        ->render() .
                        Button::make('')
                        ->icon('trash')
                        ->confirm('¿Desea eliminar este origen?')
                        ->method('delete', ['origen' => $origen->id])
                        .
                        '</div>';
                }),
        ];
    }
}
