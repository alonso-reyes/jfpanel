<?php

namespace App\Orchid\Layouts;

use App\Models\Personal;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class PersonalListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'personales';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('nombre', 'Nombre'),

            TD::make('puesto', 'Puesto')
            ->render(function (Personal $personal) {
                return $personal->puesto?->puesto ?? '—';
            }),

            TD::make('')
                ->alignRight()
                ->render(function (Personal $personal) {
                    return
                        '<div style="display: inline-flex; gap: 5px;">' .
                        Link::make('')
                        ->icon('pencil')
                        ->route('platform.personal.edit', $personal)
                        ->render() .
                        Button::make('')
                        ->icon('trash')
                        ->confirm('¿Desea eliminar este personal?')
                        ->method('delete', ['personal' => $personal->id])
                        .
                        '</div>';
                }),
        ];
    }
}
