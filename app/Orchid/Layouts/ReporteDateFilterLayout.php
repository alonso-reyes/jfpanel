<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ReporteDateFilterLayout extends Rows
{
    public function fields(): iterable
    {
        return [
            Group::make([
                DateTimer::make('fecha_inicio')
                    ->title('Fecha inicio')
                    ->format('Y-m-d'),

                DateTimer::make('fecha_termino')
                    ->title('Fecha fin')
                    ->format('Y-m-d'),


            ]),
            Button::make('Exportar a Excel')
                ->rawClick()
                ->icon('file-earmark-excel')
                ->confirm('Se descargará el Excel')
                ->method('redirectToExcel'),
        ];
    }
}
