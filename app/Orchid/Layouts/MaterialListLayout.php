<?php

namespace App\Orchid\Layouts;

use App\Models\Material;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class MaterialListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'materiales';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('material', 'Material'),

            TD::make('')
                ->alignRight()
                ->render(function (Material $material) {
                    return
                        '<div style="display: inline-flex; gap: 5px;">' .
                        Link::make('')
                        ->icon('pencil')
                        ->route('platform.material.edit', $material)
                        ->render() .
                        Button::make('')
                        ->icon('trash')
                        ->confirm('¿Desea eliminar este material?')
                        ->method('delete', ['material' => $material->id])
                        .
                        '</div>';
                }),
        ];
    }
}
