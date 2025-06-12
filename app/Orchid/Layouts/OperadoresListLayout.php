<?php

namespace App\Orchid\Layouts;

use App\Models\Operador;
use App\Models\TipoMaquinaria;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class OperadoresListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'operadores';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        $obraId = session('obra_id');
        $tiposMaquinaria = TipoMaquinaria::where('obra_id', $obraId)
            ->pluck('nombre')
            ->toArray();

        // Columnas estáticas
        $baseColumns = [
            TD::make('clave_trabajador', 'Clave del trabajador'),
            TD::make('nombre', 'Nombre'),
        ];

        // Columnas dinámicas para los tipos de maquinaria
        $maquinariaColumns = array_map(function ($tipoMaquinaria) {
            return TD::make($tipoMaquinaria, $tipoMaquinaria)
                ->render(function (Operador $operador) use ($tipoMaquinaria) {
                    // Verificar si el operador tiene asociado este tipo de maquinaria
                    $tieneMaquinaria = $operador
                        ->tiposMaquinaria()
                        ->where('nombre', $tipoMaquinaria)
                        ->exists();

                    return $tieneMaquinaria
                        ? 'X'
                        : '';
                })
                ->alignCenter();
        }, $tiposMaquinaria);

        // Botones de acción
        $actionColumn = [
            TD::make('')
                ->alignRight()
                ->render(function (Operador $operador) {
                    return
                        '<div style="display: inline-flex; gap: 5px;">' .
                        Link::make('')
                        ->icon('pencil')
                        ->route('platform.operador.edit', $operador)
                        ->render() .
                        Button::make('')
                        ->icon('trash')
                        ->confirm('¿Desea eliminar este operador?')
                        ->method('delete', ['operador' => $operador->id])
                        .
                        '</div>';
                }),
        ];

        // Unir todas las columnas
        return array_merge($baseColumns, $maquinariaColumns, $actionColumn);
    }

    protected function attributes(): array
    {
        return [
            'class' => 'w-full max-w-none overflow-x-auto',
        ];
    }
}
