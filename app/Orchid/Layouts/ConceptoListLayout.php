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
    protected function columns(): iterable
    {
        return [
            // TD::make('title', 'Concepto')
            //     ->render(function (Conceptos $concepto) {
            //         return Link::make($concepto->title)
            //             ->route('platform.concepto.edit', $concepto);
            //     }),
            // TD::make('description', 'Descripción')
            //     ->render(function (Conceptos $concepto) {
            //         return Link::make($concepto->description)
            //             ->route('platform.concepto.edit', $concepto);
            //     }),

            TD::make('nombre', 'Concepto'),
            TD::make('descripcion', 'Descripción'),
            TD::make('unidad', 'Unidad'),
            TD::make('cantidad', 'Cantidad'),

            // TD::make('')
            // ->alignRight()
            // ->render(function (Post $post) {
            //     return Button::make('')
            //         ->icon('pencil')
            //         ->confirm('After deleting, the task will be gone forever.')
            //         ->method('delete', ['post' => $post->id]);
            // }),
            
            TD::make('')
                ->alignRight()
                ->render(function (Conceptos $concepto) {
                return
                    '<div style="display: inline-flex; gap: 5px;">' .
                        Link::make('')
                        ->icon('pencil')
                        ->route('platform.concepto.edit', $concepto)
                        ->render() .
                        Button::make('')
                            ->icon('trash')
                            ->confirm('¿Desea eliminar este concepto?')
                            ->method('delete', ['concepto' => $concepto->id])
                             .
                    '</div>';
            }),

        ];
    }
}
