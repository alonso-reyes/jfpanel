<?php

namespace App\Orchid\Screens;

use App\Imports\DynamicImport;
use App\Models\Conceptos;
use App\Orchid\Layouts\ConceptoListLayout;
use App\Orchid\Layouts\ExcelImportLayout;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Orchid\Attachment\Models\Attachment;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Attach;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ConceptoListScreen extends Screen
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
            //'conceptos' => Conceptos::paginate()
            'conceptos' => Conceptos::where('obra_id', $obraId)->get()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Conceptos';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            /*Button::make('Importar desde Excel')
                 ->icon('cloud-upload')
                 ->modal('importExcelModal')
                 ->method('action') 
                 ->rawClick(),*/

            ModalToggle::make('Importar desde Excel')
                ->icon('cloud-upload')
                ->method('excelImport')
                ->icon('full-screen')
                ->modal('importExcelModal'),

            Link::make('Agregar')
                ->icon('plus')
                ->route('platform.concepto.edit'),

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
            ConceptoListLayout::class,

            Layout::modal('importExcelModal', [
                ExcelImportLayout::class, // Reutilización del layout
            ])
                ->title('Importar desde Excel')
                ->applyButton('Importar'),

            /* Layout::modal('importExcelModal', [
                 Layout::rows([
                    //  Attach::make('archivo')
                    //      ->accept('.xlsx')
                      \Orchid\Screen\Fields\Upload::make('excel_file')
                      ->title('Subir archivo Excel')
                      ->acceptedFiles('.xlsx,.xls')
                      ->required()
                      ->maxFiles(1), // Solo un archivo
                 ]),
             ])->title('Importar Conceptos desde Excel')
                 //->deferred('action')
                 //->rawClick()
                 ->applyButton('Importar'),
               //->async('asyncMethod'),*/

        ];
    }

    public function delete(Conceptos $concepto)
    {
        $concepto->delete();
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

        // El archivo no se encuentra en la ruta especificada: C:\xampp\htdocs\JFPanel\public\storage\2024/12/17/Libro1.xlsx
        //El archivo no se encuentra en la ruta especificada: C:\xampp\htdocs\JFPanel\public\storage\2024\12\17\Libro1.xlsx
        // El archivo no se encuentra en la ruta especificada: C:\xampp\htdocs\JFPanel\public\storage\2024\12\17\16228a43e6888159fc8202a9b8f158ecd9445880xlsx
        //$fileExtension = strtolower(pathinfo($attachment->original_name, PATHINFO_EXTENSION));

        // Combina la ruta con el nombre del archivo
        //$filePath = storage_path("app/public/{$attachment->path}{$attachment->name}");
        //$filePath = public_path("storage" . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $attachment->path) . $attachment->name . '.' . $fileExtension);
        //$filePath = 'C:\xampp\htdocs\JFPanel\public\storage\2024\12\17\16228a43e6888159fc8202a9b8f158ecd9445880.xlsx'; <--- Estructura correcta


        // Verifica si el archivo realmente existe
        // if (!file_exists($filePath)) {
        //     Toast::error("El archivo no se encuentra en la ruta especificada: $filePath");
        //     return;
        // }

        $fileExtension = strtolower(pathinfo($attachment->original_name, PATHINFO_EXTENSION));

        $storageRelativePath = 'app/public/' . str_replace('/', DIRECTORY_SEPARATOR, $attachment->path) . $attachment->name . '.' . $fileExtension;

        $filePath = storage_path($storageRelativePath);

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
            $existingOperador = Conceptos::where('nombre', $row[0])
                ->where('obra_id', $obraId)  // Aseguramos que la obra_id también coincida
                ->first();

            // Si ya existe otro operador con la misma clave_trabajador y obra_id, no lo agregamos
            if ($existingOperador) {
                continue;  // Saltar al siguiente registro
            }

            Conceptos::create([
                'nombre' => $row[0],
                'descripcion' => $row[1],
                'unidad' => $row[2],
                'cantidad' => $row[3],
                'obra_id' => $obraId,
            ]);
        }

        Toast::info('Datos importados correctamente.');
    }
}
