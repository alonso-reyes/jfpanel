<?php

namespace App\Orchid\Screens;

use App\Models\Camion;
use App\Orchid\Layouts\CamionListLayout;
use App\Orchid\Layouts\ExcelImportLayout;
use Illuminate\Http\Request;
use Orchid\Attachment\Models\Attachment;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CamionListScreen extends Screen
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
            'camiones' => Camion::where('obra_id', $obraId)->get()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Camiones';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Importar desde Excel')
                 ->icon('cloud-upload')
                 ->method('excelImport')
                 ->icon('full-screen')
                 ->modal('importExcelModal'),

            Link::make('Agregar')
                ->icon('plus')
                ->route('platform.camion.edit'),
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
            CamionListLayout::class,

            Layout::modal('importExcelModal', [
                ExcelImportLayout::class, // Reutilización del layout
            ])
                ->title('Importar desde Excel')
                ->applyButton('Importar'), 

        ];
    }

    public function delete(Camion $camion)
    {
        $camion->delete();
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
    $fileExtension = strtolower(pathinfo($attachment->original_name, PATHINFO_EXTENSION));

    // Combina la ruta con el nombre del archivo
    //$filePath = storage_path("app/public/{$attachment->path}{$attachment->name}");
    $filePath = public_path("storage" . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $attachment->path) . $attachment->name . '.' . $fileExtension);
    //$filePath = 'C:\xampp\htdocs\JFPanel\public\storage\2024\12\17\16228a43e6888159fc8202a9b8f158ecd9445880.xlsx'; <--- Estructura correcta


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
        $existingRecord = Camion::where('clave', $row[0])
        ->where('obra_id', $obraId)  // Aseguramos que la obra_id también coincida
        ->first();

        // Si ya existe no se agrega
        if ($existingRecord) {
        continue;  // Saltar al siguiente registro
        }

        Camion::create([
            'clave' => $row[0],
            'tipo' => $row[1],
            'largo' => $row[2],
            'ancho' => $row[3],
            'altura' => $row[4],
            'capacidad' => $row[5],
            'inspeccion_mecanica' => $row[6],
            'propietario' => $row[7],
            'obra_id' => $obraId,
        ]);
    }

    Toast::info('Datos importados correctamente.');

    }
}
