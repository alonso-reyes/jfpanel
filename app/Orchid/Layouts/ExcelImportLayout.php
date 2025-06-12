<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Fields\Upload;

class ExcelImportLayout extends Rows
{
    /**
     * Define los campos que se mostrarán en el layout.
     *
     * @return array
     */
    protected function fields(): array
    {
        return [
            Upload::make('excel_file')
                ->title('Subir archivo Excel')
                ->acceptedFiles('.xlsx,.xls') // Extensiones permitidas
                ->required() // Campo obligatorio
                ->maxFiles(1), // Limitar a un archivo
        ];
    }
}
