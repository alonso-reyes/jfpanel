<?php

namespace App\Orchid\Screens;

use App\Models\Puesto;
use Orchid\Screen\Screen;
use App\Orchid\Layouts\ExcelImportLayout;
use App\Orchid\Layouts\PuestoListLayout;
use Illuminate\Http\Request;
use Orchid\Attachment\Models\Attachment;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PuestoListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $obraId = session('obra_id');

        return [
            'puestos' => Puesto::where('obra_id', $obraId)->get()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Puestos';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            // ModalToggle::make('Importar desde Excel')
            //     ->icon('cloud-upload')
            //     ->method('excelImport')
            //     ->icon('full-screen')
            //     ->modal('importExcelModal'),

            Link::make('Agregar')
                ->icon('plus')
                ->route('platform.puesto.edit')
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            PuestoListLayout::class,

            // Layout::modal('importExcelModal', [
            //     ExcelImportLayout::class,
            // ])
            //     ->title('Importar desde Excel')
            //     ->applyButton('Importar'),
        ];
    }

    public function delete(Puesto $puesto)
    {
        $puesto->delete();
    }

    public function excelImport(Request $request)
    {
        $obraId = session('obra_id');
        // Obtén el ID del archivo subido
        $fileId = $request->input('excel_file.0');
        $attachment = Attachment::find($fileId);
        //dd($attachment);return;
        if (!$attachment) {
            Toast::error('El archivo no se pudo encontrar.');
            return;
        }

        $fileExtension = strtolower(pathinfo($attachment->original_name, PATHINFO_EXTENSION));
        $filePath = public_path("storage" . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $attachment->path) . $attachment->name . '.' . $fileExtension);

        // Verifica si el archivo realmente existe
        if (!file_exists($filePath)) {
            Toast::error("El archivo no se encuentra en la ruta especificada: $filePath");
            return;
        }

        // Cargar el archivo Excel
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Procesar las filas del archivo
        foreach ($rows as $key => $row) {
            if ($key == 0) continue; // Saltar encabezados
            // Valida repetidos
            $exists = Puesto::where('puesto', $row[0])
                ->where('obra_id', $obraId)  // Aseguramos que la obra_id también coincida
                ->first();

            if ($exists) {
                continue;  // Saltar al siguiente registro
            }

            Puesto::create([
                'puesto' => $row[0],
                'obra_id' => $obraId,
            ]);
        }

        Toast::info('Datos importados correctamente.');
    }
}
