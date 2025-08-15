<?php

namespace App\Orchid\Layouts;

use App\Models\Conceptos;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ConceptoListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    public $target = 'conceptos';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    // protected function columns(): iterable
    // {
    //     return [

    //         TD::make('nombre', 'Concepto'),
    //         TD::make('descripcion', 'Descripción'),
    //         TD::make('unidad', 'Unidad'),
    //         TD::make('cantidad', 'Cantidad'),

    //         TD::make('')
    //             ->alignRight()
    //             ->render(function (Conceptos $concepto) {
    //                 return
    //                     '<div style="display: inline-flex; gap: 5px;">' .
    //                     Link::make('')
    //                     ->icon('pencil')
    //                     ->route('platform.concepto.edit', $concepto)
    //                     ->render() .
    //                     Button::make('')
    //                     ->icon('trash')
    //                     ->confirm('¿Desea eliminar este concepto?')
    //                     ->method('delete', ['concepto' => $concepto->id])
    //                     .
    //                     '</div>';
    //             }),

    //     ];
    // }

    protected function columns(): iterable
    {
        return [

            TD::make('nombre', 'Clave')
                ->render(function (Conceptos $concepto) {
                    $profundidad = substr_count($concepto->nombre, '.');
                    $espacios = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $profundidad);

                    $contenido = $concepto->tipo === 'SUBCAPITULO'
                        ? "<strong style='color:#1d4ed8'>{$concepto->nombre}</strong>"
                        : e($concepto->nombre);

                    if ($concepto->tipo === 'SUBCAPITULO') {
                        return "<div padding: 10px; border-radius: 6px;'>{$espacios}{$contenido}</div>";
                    }

                    return "{$espacios}{$contenido}";
                }),


            TD::make('descripcion', 'Descripción del concepto')
                ->render(function (Conceptos $concepto) {
                    return $concepto->tipo === 'SUBCAPITULO'
                        ? "<span style='font-weight: bold; color: #1e293b;'>{$concepto->descripcion}</span>"
                        : e($concepto->descripcion);
                }),

            // Ocultar estas columnas si es tipo SUBCAPITULO
            TD::make('unidad', 'Unidad')
                ->render(
                    fn(Conceptos $concepto) =>
                    $concepto->tipo === 'SUBCAPITULO' ? '' : e($concepto->unidad)
                ),

            TD::make('cantidad', 'Cantidad')
                ->render(
                    fn(Conceptos $concepto) =>
                    $concepto->tipo === 'SUBCAPITULO' ? '' : number_format($concepto->cantidad, 2)
                ),

            TD::make('')
                ->alignRight()
                ->render(function (Conceptos $concepto) {
                    return '<div style="display: inline-flex; gap: 5px;">' .
                        Link::make('')
                        ->icon('pencil')
                        ->route('platform.concepto.edit', $concepto)
                        ->render() .
                        Button::make('')
                        ->icon('trash')
                        ->confirm('¿Desea eliminar este concepto?')
                        ->method('delete', ['concepto' => $concepto->id])
                        . '</div>';
                }),

        ];
    }
}
