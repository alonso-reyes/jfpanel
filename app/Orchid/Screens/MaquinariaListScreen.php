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
use Illuminate\Support\Facades\Log;
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

        // Validar que se haya subido un archivo
        if (!$request->hasFile('excel_file')) {
            Toast::error('No se ha seleccionado ningún archivo.');
            return;
        }

        $file = $request->file('excel_file');

        // Validar que sea un archivo Excel
        $allowedExtensions = ['xlsx', 'xls', 'csv'];
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, $allowedExtensions)) {
            Toast::error('El archivo debe ser un Excel (.xlsx, .xls) o CSV.');
            return;
        }

        // Validar el tamaño del archivo (opcional, por ejemplo 5MB máximo)
        if ($file->getSize() > 5 * 1024 * 1024) {
            Toast::error('El archivo es demasiado grande. Máximo 5MB.');
            return;
        }

        try {
            // Procesar directamente desde el archivo temporal
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $processed = 0;
            $skipped = 0;
            $errors = [];

            foreach ($rows as $key => $row) {
                // Saltar encabezados
                if ($key == 0) continue;

                try {
                    // Validar que la fila tenga datos suficientes
                    if (count($row) < 6 || empty($row[0])) {
                        $skipped++;
                        continue;
                    }

                    // Limpiar datos (remover espacios extra, etc.)
                    $numeroEconomico = trim($row[0]);
                    $modelo = trim($row[1]);
                    $tipoMaquinariaNombre = trim($row[2]);
                    $horometroInicial = is_numeric($row[3]) ? (float)$row[3] : 0;
                    $estado = trim($row[4]);
                    $inactividad = trim($row[5]);

                    // Validar datos requeridos
                    if (empty($numeroEconomico) || empty($tipoMaquinariaNombre)) {
                        $skipped++;
                        continue;
                    }

                    // Validar repetidos
                    $existing = Maquinaria::where('numero_economico', $numeroEconomico)
                        ->where('obra_id', $obraId)
                        ->first();

                    if ($existing) {
                        $skipped++;
                        continue;
                    }

                    // Crear o encontrar tipo de maquinaria
                    $tipoMaquinaria = TipoMaquinaria::firstOrCreate(
                        ['nombre' => $tipoMaquinariaNombre, 'obra_id' => $obraId],
                        ['acarreo_agua' => 0]
                    );

                    // Crear maquinaria
                    $maquinaria = Maquinaria::create([
                        'numero_economico' => $numeroEconomico,
                        'modelo' => $modelo,
                        'tipo_maquinaria_id' => $tipoMaquinaria->id,
                        'horometro_inicial' => $horometroInicial,
                        'estado' => $estado,
                        'inactividad' => $inactividad,
                        'obra_id' => $obraId,
                    ]);

                    // Crear horómetro inicial
                    Horometro::create([
                        'maquinaria_id' => $maquinaria->id,
                        'horometro_inicial' => $horometroInicial,
                        'horometro_final' => null,
                        'parcialidad_turno' => 0
                    ]);

                    $processed++;
                } catch (Exception $e) {
                    $errors[] = "Fila " . ($key + 1) . ": " . $e->getMessage();
                    Log::error("Error procesando fila $key: " . $e->getMessage());
                }
            }

            // Mostrar resultados
            $message = "Importación completada. Procesados: $processed";
            if ($skipped > 0) {
                $message .= ", Omitidos: $skipped";
            }
            if (count($errors) > 0) {
                $message .= ", Errores: " . count($errors);
            }

            if ($processed > 0) {
                Toast::success($message);
            } else {
                Toast::warning('No se procesaron registros. ' . $message);
            }

            // Log errores si los hay
            if (count($errors) > 0) {
                Log::warning('Errores durante importación:', $errors);
            }
        } catch (Exception $e) {
            Log::error('Error al procesar archivo Excel: ' . $e->getMessage());
            Toast::error('Error al procesar el archivo: ' . $e->getMessage());
        }
    }
}
