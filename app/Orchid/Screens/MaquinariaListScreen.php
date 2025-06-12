<?php

namespace App\Orchid\Screens;

use App\Imports\DynamicImport;
use App\Models\Horometro;
use App\Models\Maquinaria;
use App\Models\TipoMaquinaria;
use App\Orchid\Layouts\ExcelImportLayout;
use App\Orchid\Layouts\MaquinariaListLayout;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Orchid\Attachment\Models\Attachment;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MaquinariaListScreen extends Screen
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
            'maquinarias' => Maquinaria::where('obra_id', $obraId)->get()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Maquinaria';
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
                ->route('platform.maquinaria.edit')
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
            MaquinariaListLayout::class,

            Layout::modal('importExcelModal', [
                ExcelImportLayout::class, // Reutilización del layout
            ])
                ->title('Importar desde Excel')
                ->applyButton('Importar'),
        ];
    }

    public function delete(Maquinaria $maquinaria)
    {
        $maquinaria->delete();
    }

    public function importExcel(Request $request)
    {
        $obraId = session('obra_id');

        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);

        Excel::import(new DynamicImport($obraId, Maquinaria::class, [
            'title' => 0,
            'description' => 1,
        ]), $request->file('excel_file'));

        Alert::info('Datos importados con éxito.');
        return redirect()->route('platform.concepto.list');
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
            $existing = Maquinaria::where('numero_economico', $row[0])
                ->where('obra_id', $obraId)  // Aseguramos que la obra_id también coincida
                ->first();

            // Si ya existe otro operador con la misma clave_trabajador y obra_id, no lo agregamos
            if ($existing) {
                continue;  // Saltar al siguiente registro
            }

            //$tipoMaquinaria = TipoMaquinaria::where('nombre', $row[2])->first();
            $tipoMaquinaria = TipoMaquinaria::firstOrCreate(
                ['nombre' => $row[2], 'obra_id' => $obraId],
                ['acarreo_agua' => 0] // Valor por defecto si no viene en el Excel
            );

            $maquinaria = Maquinaria::create([
                'numero_economico' => $row[0],
                'modelo' => $row[1],
                'tipo_maquinaria_id' => $tipoMaquinaria->id,
                'horometro_inicial' => $row[3],
                'estado' => $row[4],
                'inactividad' => $row[5],
                'obra_id' => $obraId,
            ]);

            Horometro::create([
                'maquinaria_id' => $maquinaria->id,
                'horometro_inicial' => $row[3],
                'horometro_final' => null,
                'parcialidad_turno' => 0
            ]);

            // if (!empty($row[2])) {
            //     // Separar la cadena de tipos de maquinaria por comas
            //     $maquinariaArray = explode(',', $row[2]);

            //     foreach ($maquinariaArray as $tipo_maquinaria) {
            //         // Limpiar espacios en blanco
            //         $tipo_maquinaria = trim($tipo_maquinaria);

            //         // Buscar el tipo de maquinaria en la base de datos
            //         $tipoMaquinaria = TipoMaquinaria::where('nombre', $tipo_maquinaria)->first();

            //         // Si el tipo de maquinaria existe, insertarlo en la tabla intermedia
            //         if ($tipoMaquinaria) {
            //             $maquinaria->tiposMaquinaria()->attach($tipoMaquinaria->id);
            //         } else {
            //             // Opcional: Puedes mostrar un mensaje si no se encuentra el tipo de maquinaria
            //             Toast::warning("Tipo de maquinaria '{$tipo_maquinaria}' no encontrado.");
            //         }
            //     }
            // }
        }

        Toast::info('Datos importados correctamente.');
    }
}
