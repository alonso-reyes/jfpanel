<?php

namespace App\Orchid\Layouts;

use App\Models\Turno;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class TurnoListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'turnos';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('nombre_turno', 'Turno'),
            TD::make('hora_entrada', 'Hora de inicio'),
            TD::make('hora_salida', 'Hora de término'),

            TD::make('')
                ->alignRight()
                ->render(function (Turno $turno) {
                return
                    '<div style="display: inline-flex; gap: 5px;">' .
                        Link::make('')
                        ->icon('pencil')
                        ->route('platform.turno.edit', $turno)
                        ->render() .
                        Button::make('')
                            ->icon('trash')
                            ->confirm('¿Desea eliminar este turno?')
                            ->method('delete', ['turno' => $turno->id])
                             .
                    '</div>';
            }),
        ];
    }
}
