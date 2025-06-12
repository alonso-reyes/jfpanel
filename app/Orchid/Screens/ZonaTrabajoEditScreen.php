<?php

namespace App\Orchid\Screens;

use App\Models\ZonaTrabajo;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Orchid\Attachment\Models\Attachment;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Upload;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Fields\Image;
use Orchid\Screen\Fields\Picture;

class ZonaTrabajoEditScreen extends Screen
{
    public $zona_trabajo;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(ZonaTrabajo $zona_trabajo): iterable
    {
        // Genera la URL pública de la imagen
        $imagenUrl = !empty($zona_trabajo->imagen) ? asset('storage/' . $zona_trabajo->imagen) : null;

        // Verificamos si la imagen existe y creamos la estructura necesaria para el campo 'Upload'
        if (!empty($zona_trabajo->imagen)) {
            $zona_trabajo->imagen = [
                [
                    'name' => basename($zona_trabajo->imagen),  // Nombre del archivo
                    'url' => $imagenUrl,                        // URL pública
                    'size' => filesize(public_path('storage/' . $zona_trabajo->imagen)),  // Tamaño del archivo
                ]
            ];
        }

        return [
            'zona_trabajo' => $zona_trabajo,
            'imagen_url' => $imagenUrl,  // Pasamos la URL de la imagen para usar en la ayuda
        ];
    }


    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->zona_trabajo->exists ?  '' : 'Agregar';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        $exists = $this->zona_trabajo->exists ? true : false;
        return [
            Button::make($exists ? 'Editar' : 'Agregar')
                ->icon($exists ? 'pencil' : 'plus')
                ->method('createOrUpdate')
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
            Layout::rows([
                Input::make('zona_trabajo.clave')
                    ->title('Clave'),
                    //->placeholder('Concepto')
                    //->help('Nombre del concepto'),
                Input::make('zona_trabajo.nombre')
                    ->title('Nombre'),
                
                TextArea::make('zona_trabajo.descripcion')
                    ->title('Descripción')
                    ->rows(3)
                    ->maxlength(200),
                
                // Campo para subir la imagen
                Upload::make('zona_trabajo.imagen')
                    ->title('Zona de trabajo')
                    ->acceptedFiles('image/*')
                    ->maxFiles(1),
            
            ])
        ];
    }

    public function createOrUpdate(Request $request)
    {
        $obraId = session('obra_id'); 

        if (!$obraId) {
            Alert::error('Error: No se ha seleccionado ninguna obra.');
            return redirect()->route('obra.select');
        }
        
        //$this->concepto->fill($request->get('concepto'))->save();
        $fileId = $request->input('zona_trabajo.imagen.0'); 
        $attachment = Attachment::find($fileId);

        if (!$attachment) {
            Toast::error('El archivo no se pudo encontrar.');
            return;
        }
        //dd($attachment->path);return;
        $fileExtension = strtolower(pathinfo($attachment->original_name, PATHINFO_EXTENSION));
        //PATH COMPLETO --> //$filePath = public_path("storage" . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $attachment->path) . $attachment->name . '.' . $fileExtension);
        $filePath = str_replace('/', DIRECTORY_SEPARATOR, $attachment->path) . $attachment->name . '.' . $fileExtension;
        //dd($filePath);return;
       
        $this->zona_trabajo->fill([
            ...$request->get('zona_trabajo'),
            'obra_id' => $obraId, 
            'imagen' => $filePath ?? null, // Si se subió imagen, guardarla en la base de datos
        ])->save();

        Alert::info('Zona de trabajo agregada con éxito');

        return redirect()->route('platform.zona.trabajo.list');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove()
    {
        $this->zona_trabajo->delete();

        Alert::info('Concepto eliminado');

        return redirect()->route('platform.zona.trabajo.list');
    }

}
