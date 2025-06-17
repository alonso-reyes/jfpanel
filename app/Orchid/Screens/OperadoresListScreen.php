<?php

namespace App\Orchid\Screens;

use App\Imports\DynamicImport;
use App\Models\Operador;
use App\Models\TipoMaquinaria;
use App\Orchid\Layouts\ExcelImportLayout;
use App\Orchid\Layouts\OperadoresListLayout;
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

class OperadoresListScreen extends Screen
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
            //'operadores' => Operador::paginate()
            'operadores' => Operador::where('obra_id', $obraId)->get()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Operadores';
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
                ->route('platform.operador.edit')
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
            OperadoresListLayout::class,

            Layout::modal('importExcelModal', [
                ExcelImportLayout::class, // Reutilización del layout
            ])
                ->title('Importar desde Excel')
                ->applyButton('Importar'),
        ];
    }

    public function delete(Operador $operador)
    {
        $operador->delete();
    }

    public function importExcel(Request $request)
    {
        $obraId = session('obra_id');

        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);

        Excel::import(new DynamicImport($obraId, Operador::class, [
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

        // $fileExtension = strtolower(pathinfo($attachment->original_name, PATHINFO_EXTENSION));

        // $filePath = public_path("storage" . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $attachment->path) . $attachment->name . '.' . $fileExtension);

        // // Verifica si el archivo realmente existe
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
            if ($key === 0) {
                $headers = $row; // Guardar encabezados en la primera vuelta
                continue;
            }
            // Saltar encabezados
            // Valida repetidos
            $existingOperador = Operador::where('clave_trabajador', $row[0])
                ->where('obra_id', $obraId)  // Aseguramos que la obra_id también coincida
                ->first();

            // Si ya existe otro operador con la misma clave_trabajador y obra_id, no lo agregamos
            if ($existingOperador) {
                continue;  // Saltar al siguiente registro
            }

            // Si no existe, proceder con la creación del operador
            $operador = Operador::create([
                'clave_trabajador' => $row[0],
                'nombre' => $row[1],
                'obra_id' => $obraId,
            ]);

            for ($i = 2; $i < count($row); $i++) {
                $valor = strtolower(trim($row[$i]));

                if ($valor === 'x') {
                    $nombreMaquinaria = trim($headers[$i]);

                    // Crear o buscar tipo de maquinaria
                    $tipoMaquinaria = TipoMaquinaria::firstOrCreate(
                        ['nombre' => $nombreMaquinaria, 'obra_id' => $obraId],
                        ['acarreo_agua' => 0] // o lo que necesites como default
                    );

                    // Asociar con el operador
                    $operador->tiposMaquinaria()->attach($tipoMaquinaria->id);
                }
            }
            // $tiposMaquinaria = TipoMaquinaria::pluck('nombre', 'columna_excel')->toArray();

            // foreach ($tiposMaquinaria as $columnaExcel => $nombreMaquinaria) {
            //     if (!empty($row[$columnaExcel])) {
            //         // Limpiar espacios en blanco
            //         $nombreMaquinaria = trim($nombreMaquinaria);

            //         // Buscar el tipo de maquinaria en la base de datos
            //         $tipoMaquinaria = TipoMaquinaria::where('nombre', $nombreMaquinaria)->first();

            //         // Si el tipo de maquinaria existe, insertarlo en la tabla intermedia
            //         if ($tipoMaquinaria) {
            //             $operador->tiposMaquinaria()->attach($tipoMaquinaria->id);
            //         } else {
            //             // Opcional: Mostrar mensaje si no se encuentra el tipo de maquinaria
            //             Toast::warning("Tipo de maquinaria '{$nombreMaquinaria}' no encontrado.");
            //         }
            //     }
            // }

            // $tiposMaquinaria = [
            //     2 => 'VIBRO',
            //     3 => 'MOTO',
            //     4 => 'TRACTOR',
            //     5 => 'PIPA',
            //     6 => 'TRACTO'
            // ];

            // foreach ($tiposMaquinaria as $indice => $nombreMaquinaria) {
            //     if (!empty($row[$indice])) {
            //         $tipoMaquinaria = TipoMaquinaria::where('nombre', $nombreMaquinaria)->first();

            //         if ($tipoMaquinaria) {
            //             $operador->tiposMaquinaria()->attach($tipoMaquinaria->id);
            //         } else {
            //             Toast::warning("Tipo de maquinaria '{$nombreMaquinaria}' no encontrado.");
            //         }
            //     }
            // }

            /// Agrega tipo de maquinaria separado por comas 
            // if (!empty($row[2])) {
            //     // Separar la cadena de tipos de maquinaria por comas
            //     $maquinariaArray = explode(',', $row[2]);

            //     foreach ($maquinariaArray as $maquinaria) {
            //         // Limpiar espacios en blanco
            //         $maquinaria = trim($maquinaria);

            //         // Buscar el tipo de maquinaria en la base de datos
            //         $tipoMaquinaria = TipoMaquinaria::where('nombre', $maquinaria)->first();

            //         // Si el tipo de maquinaria existe, insertarlo en la tabla intermedia
            //         if ($tipoMaquinaria) {
            //             $operador->tiposMaquinaria()->attach($tipoMaquinaria->id);
            //         } else {
            //             // Opcional: Puedes mostrar un mensaje si no se encuentra el tipo de maquinaria
            //             Toast::warning("Tipo de maquinaria '{$maquinaria}' no encontrado.");
            //         }
            //     }
            // }
        }

        Toast::info('Datos importados correctamente.');
    }
}
