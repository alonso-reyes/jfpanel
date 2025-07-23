<?php

namespace App\Orchid\Layouts;

use App\Models\AcarreoArea;
use App\Models\Conceptos;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class AcarreoAreaListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'acarreosArea';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        $obraId = session('obra_id');

        // Obtener conceptos con su factor de abundamiento para JavaScript
        $conceptos = Conceptos::where('tipo', 'CAPITULO')
            ->where('obra_id', $obraId)
            ->orderBy('descripcion')
            ->get(['id', 'descripcion', 'factor_abundamiento'])
            ->keyBy('id');

        return [
            TD::make('area', 'Área'),
            TD::make('concepto_id', 'Concepto')->render(function ($item) use ($conceptos) {
                return $conceptos->firstWhere('id', $item->concepto_id)?->descripcion ?? '';
            }),
            TD::make('concepto_id', 'Factor abundamiento')->render(function ($item) use ($conceptos) {
                return $conceptos->firstWhere('id', $item->concepto_id)?->factor_abundamiento ?? '';
            }),


            TD::make('')
                ->alignRight()
                ->render(function (AcarreoArea $acarreoarea) {
                    return
                        '<div style="display: inline-flex; gap: 5px;">' .
                        Link::make('')
                        ->icon('pencil')
                        ->route('platform.acarreoarea.edit', $acarreoarea)
                        ->render() .
                        Button::make('')
                        ->icon('trash')
                        ->confirm('¿Desea eliminar este registro?')
                        ->method('delete', ['acarreoarea' => $acarreoarea->id])
                        . '</div>';
                }),
        ];
    }
}
