<?php

namespace App\Orchid\Layouts;

use App\Models\MaterialUso;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class UsoMaterialListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'usos';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('uso', 'Uso del material'),

            TD::make('')
                ->alignRight()
                ->render(function (MaterialUso $uso) {
                    //dd($uso);
                    return
                        '<div style="display: inline-flex; gap: 5px;">' .
                        Link::make('')
                        ->icon('pencil')
                        ->route('platform.uso.material.edit', $uso)
                        ->render() .
                        Button::make('')
                        ->icon('trash')
                        ->confirm('¿Desea eliminar este uso de material?')
                        ->method('delete', ['uso' => $uso->id])
                        .
                        '</div>';
                }),
        ];
    }
}
