<?php

namespace App\Orchid\Layouts;

use App\Models\MotivoInactividad;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class MotivosInactividadMaquinariaListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'motivos_inactividad';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('motivo_inactividad', 'Motivos de inactividad'),

            TD::make('')
                ->alignRight()
                ->render(function (MotivoInactividad $motivo_inactividad) {
                    return
                        '<div style="display: inline-flex; gap: 5px;">' .
                        Link::make('')
                        ->icon('pencil')
                        ->route('platform.motivo.inactividad.edit', $motivo_inactividad)
                        ->render() .
                        Button::make('')
                        ->icon('trash')
                        ->confirm('¿Desea eliminar este registro?')
                        ->method('delete', ['motivo_inactividad' => $motivo_inactividad->id])
                        .
                        '</div>';
                }),
        ];
    }
}
