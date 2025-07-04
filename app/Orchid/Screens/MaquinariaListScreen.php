<?php

namespace App\Orchid\Screens;

use App\Imports\DynamicImport;
use App\Models\Horometro;
use App\Models\Maquinaria;
use App\Models\TipoMaquinaria;
use App\Orchid\Layouts\ExcelImportLayout;
use App\Orchid\Layouts\MaquinariaListLayout;
use FFI\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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


    public function excelImport(Request $request)
    {
        $obraId = session('obra_id');

        // Obtén el ID del archivo subido
        $fileId = $request->input('excel_file.0');
        $attachment = Attachment::find($fileId);

        if (!$attachment) {
            Toast::error('El archivo no se pudo encontrar.');
            return;
        }

        $fileExtension = strtolower(pathinfo($attachment->original_name, PATHINFO_EXTENSION));

        // Intentar múltiples rutas posibles
        $possiblePaths = [
            // Ruta usando Storage::disk('public')
            Storage::disk('public')->path($attachment->path . $attachment->name . '.' . $fileExtension),

            // Ruta directa en storage/app/public
            storage_path('app/public/' . $attachment->path . $attachment->name . '.' . $fileExtension),

            // Ruta usando solo storage/app
            storage_path('app/' . $attachment->path . $attachment->name . '.' . $fileExtension),
        ];

        $filePath = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $filePath = $path;
                break;
            }
        }

        // Si no encontramos el archivo, intentar usar el contenido directamente
        if (!$filePath) {
            // Verificar si podemos obtener el contenido usando Storage
            try {
                $diskPath = $attachment->path . $attachment->name . '.' . $fileExtension;

                if (Storage::disk('public')->exists($diskPath)) {
                    // Crear archivo temporal
                    $tempPath = sys_get_temp_dir() . '/' . uniqid() . '.' . $fileExtension;
                    $content = Storage::disk('public')->get($diskPath);
                    file_put_contents($tempPath, $content);
                    $filePath = $tempPath;
                } else {
                    Toast::error("El archivo no se encuentra disponible para procesamiento.");
                    return;
                }
            } catch (Exception $e) {
                Toast::error("Error al acceder al archivo: " . $e->getMessage());
                return;
            }
        }

        try {
            // Cargar el archivo Excel
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Procesar las filas del archivo
            foreach ($rows as $key => $row) {
                if ($key == 0) continue; // Saltar encabezados

                // Validar que la fila tenga datos suficientes
                if (count($row) < 6) {
                    continue; // Saltar filas incompletas
                }

                // Valida repetidos
                $existing = Maquinaria::where('numero_economico', $row[0])
                    ->where('obra_id', $obraId)
                    ->first();

                if ($existing) {
                    continue;
                }

                $tipoMaquinaria = TipoMaquinaria::firstOrCreate(
                    ['nombre' => $row[2], 'obra_id' => $obraId],
                    ['acarreo_agua' => 0]
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
            }

            Toast::info('Datos importados correctamente.');
        } catch (Exception $e) {
            Toast::error('Error al procesar el archivo: ' . $e->getMessage());
        } finally {
            // Limpiar archivo temporal si fue creado
            if (isset($tempPath) && file_exists($tempPath)) {
                unlink($tempPath);
            }
        }
    }
}
