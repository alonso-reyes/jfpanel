<?php

namespace App\Orchid\Layouts;

use App\Models\CatalogoCamionAcarreo;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class TiposCamionListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'tipos_camion';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('nombre', 'Tipo de camión'),

            TD::make('')
                ->alignRight()
                ->render(function (CatalogoCamionAcarreo $tipo_camion) {
                    return
                        '<div style="display: inline-flex; gap: 5px;">' .
                        Link::make('')
                        ->icon('pencil')
                        ->route('platform.tipo.camion.edit', $tipo_camion)
                        ->render() .
                        Button::make('')
                        ->icon('trash')
                        ->confirm('¿Desea eliminar este registro?')
                        ->method('delete', ['tipo_camion' => $tipo_camion->id])
                        .
                        '</div>';
                }),
        ];
    }
}
