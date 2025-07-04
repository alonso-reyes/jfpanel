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

        // Orchid te da el ID del archivo
        $fileId = $request->input('excel_file.0');
        $attachment = Attachment::find($fileId);

        if (!$attachment) {
            Toast::error('El archivo no se pudo encontrar.');
            return;
        }

        // Obtener extensión
        $fileExtension = strtolower(pathinfo($attachment->original_name, PATHINFO_EXTENSION));

        // Ruta dentro del disco 'public'
        $relativePath = $attachment->path . $attachment->name . '.' . $fileExtension;

        // Verificar si existe en el disco
        if (!Storage::disk('public')->exists($relativePath)) {
            Toast::error("El archivo no se encuentra en disco: $relativePath");
            return;
        }

        // Copiar el archivo a una ruta temporal
        $tempPath = sys_get_temp_dir() . '/' . uniqid('excel_', true) . '.' . $fileExtension;
        $localFilePath = Storage::disk('public')->path($relativePath);
        copy($localFilePath, $tempPath);

        try {
            // Leer el archivo Excel
            $spreadsheet = IOFactory::load($tempPath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            foreach ($rows as $key => $row) {
                if ($key === 0) continue; // Encabezado

                if (count($row) < 6 || empty($row[0])) continue;

                // Verificar duplicado
                $existing = Maquinaria::where('numero_economico', $row[0])
                    ->where('obra_id', $obraId)
                    ->first();

                if ($existing) continue;

                // Crear tipo maquinaria
                $tipoMaquinaria = TipoMaquinaria::firstOrCreate(
                    ['nombre' => $row[2], 'obra_id' => $obraId],
                    ['acarreo_agua' => 0]
                );

                // Crear maquinaria
                $maquinaria = Maquinaria::create([
                    'numero_economico' => $row[0],
                    'modelo' => $row[1],
                    'tipo_maquinaria_id' => $tipoMaquinaria->id,
                    'horometro_inicial' => $row[3],
                    'estado' => $row[4],
                    'inactividad' => $row[5],
                    'obra_id' => $obraId,
                ]);

                // Crear horómetro
                Horometro::create([
                    'maquinaria_id' => $maquinaria->id,
                    'horometro_inicial' => $row[3],
                    'horometro_final' => null,
                    'parcialidad_turno' => 0,
                ]);
            }

            Toast::info('Datos importados correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al procesar Excel: ' . $e->getMessage());
            Toast::error('Error al procesar el archivo.');
        } finally {
            // Borrar archivo temporal
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
        }
    }
}
