<?php

namespace App\Orchid\Layouts;

use App\Models\TipoMaquinaria;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class TipoMaquinaListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'tipos_maquinaria';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('nombre', 'Tipo de maquinaria'),

            TD::make('acarreo_agua', 'Acarreo de agua')
                ->render(function (TipoMaquinaria $tipo_maquinaria) {
                    return $tipo_maquinaria->acarreo_agua ? 'SI' : 'NO';
                }),

            TD::make('')
                ->alignRight()
                ->render(function (TipoMaquinaria $tipo_maquinaria) {
                    return
                        '<div style="display: inline-flex; gap: 5px;">' .
                        Link::make('')
                        ->icon('pencil')
                        ->route('platform.tipo.maquinaria.edit', $tipo_maquinaria)
                        ->render() .
                        Button::make('')
                        ->icon('trash')
                        ->confirm('¿Desea eliminar este tipo?')
                        ->method('delete', ['tipo_maquinaria' => $tipo_maquinaria->id])
                        .
                        '</div>';
                }),
        ];
    }
}
