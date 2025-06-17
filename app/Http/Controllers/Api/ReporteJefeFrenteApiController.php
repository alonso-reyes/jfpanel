<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReporteJefeFrente;
use App\Models\AcarreoVolumen;
use App\Models\AcarreoArea;
use App\Models\AcarreoMetroLineal;
use App\Models\AcarreoAgua;
use App\Models\Horometro;
use App\Models\ReporteFotografia;
use App\Models\ReporteMaquinaria;
use App\Models\ReportePersonal;
use App\Models\ZonaTrabajoDibujo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Cloudinary\Cloudinary;


class ReporteJefeFrenteApiController extends Controller
{
    public function guardarReporte(Request $request)
    {
        //Log::info('Datos recibidos:', $request->all()); 
        //Log::info('Datos recibidos:', $request['data']['acarreos_volumen']);


        // Validar la solicitud
        $request->validate([
            'obra_id' => 'required|exists:obras,id',
            'data.usuario_id' => 'required|exists:usuarios_jefe_frente,id',
            'data.turno.id' => 'required|exists:turnos,id',
            'data.zona_trabajo.id' => 'required|exists:zonas_trabajo,id',
        ]);

        // Configurar Cloudinary
        $cloudName = config('cloudinary.cloud_name');
        $apiKey = config('cloudinary.api_key');
        $apiSecret = config('cloudinary.api_secret');

        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => $cloudName,
                'api_key'    => $apiKey,
                'api_secret' => $apiSecret,
            ],
            'url' => [
                'secure' => true
            ]
        ]);

        // Obtener los datos de la solicitud
        $data = $request->all();

        // Convertir el formato de tiempo
        $horaInicio = Carbon::createFromFormat('h:i A', $data['data']['turno']['hora_real_entrada'])->format('H:i:s');
        $horaTermino = Carbon::createFromFormat('h:i A', $data['data']['turno']['hora_real_salida'])->format('H:i:s');

        // Crear el reporte principal
        $reporte = ReporteJefeFrente::create([
            'usuario_id' => $data['data']['usuario_id'],
            'turno_id' => $data['data']['turno']['id'],
            'hora_inicio_real_actividades' => $horaInicio, // Formato HH:MM:SS
            'hora_termino_real_actividades' => $horaTermino, // Formato HH:MM:SS
            'zona_trabajo_id' => $data['data']['zona_trabajo']['id'],
            'observaciones' => $data['data']['generales']['observaciones'],
            'sobrestante' => $data['data']['generales']['sobrestante'],
            'obra_id' => $data['obra_id'],
        ]);

        if (!empty($data['data']['zona_trabajo'])) {
            $reporte_id = $reporte->id;
            $zona_id = $data['data']['zona_trabajo']['id'];
            $imagePath = null;

            if (!empty($data['data']['zona_trabajo']['imagen_dibujada'])) {
                $imagen = $data['data']['zona_trabajo']['imagen_dibujada'];
                $image = base64_decode($imagen);
                // $imageName = $reporte_id . '_' . $zona_id . '_' . time() . '_' . uniqid() . '.jpg';
                // $imagePath = public_path('images/zona_trabajo/' . $imageName);
                //file_put_contents($imagePath, $image);
                $tempPath = tempnam(sys_get_temp_dir(), 'cloud');
                // Guardar la imagen en el servidor
                file_put_contents($tempPath, $image);

                $uploaded = $cloudinary->uploadApi()->upload($tempPath, [
                    'folder' => 'zona_trabajo_reportes',
                    'use_filename' => true,
                    'unique_filename' => true,
                ]);

                $imageUrl = $uploaded['secure_url'];
            }

            //Log::info('Ruta de la imagen dibujada: ' . $imagePath);

            ZonaTrabajoDibujo::create([
                'reporte_id' => $reporte_id,
                'zona_trabajo_id' => $zona_id,
                'puntos' => !empty($data['data']['zona_trabajo']['dibujos'])
                    ? json_encode($data['data']['zona_trabajo']['dibujos'])
                    : null,
                'color' => '#000000',
                'grosor' => 1.0,
                'ruta_imagen' => $imageUrl
            ]);

            //Log::info('Dibujo combinado creado:', $dibujo->toArray());
        }

        // Guardar acarreos_volumen si existen
        if (!empty($data['data']['acarreos_volumen'])) {
            foreach ($data['data']['acarreos_volumen'] as $acarreoVolumen) {
                AcarreoVolumen::create([
                    'reporte_frente_id' => $reporte->id,
                    'material_id' => $acarreoVolumen['material']['id'],
                    'material_uso_id' => $acarreoVolumen['usoMaterial']['id'],
                    'origen_id' => $acarreoVolumen['origen']['id'],
                    'destino_id' => $acarreoVolumen['destino']['id'],
                    'camion_id' => $acarreoVolumen['camion']['id'],
                    'viajes' => $acarreoVolumen['viajes'],
                    'capacidad' => $acarreoVolumen['capacidad'],
                    'volumen' => $acarreoVolumen['volumen'],
                    'observaciones' => $acarreoVolumen['observaciones'],
                ]);
            }
        }

        // Guardar acarreos_area si existen
        if (!empty($data['data']['acarreos_area'])) {
            foreach ($data['data']['acarreos_area'] as $acarreoArea) {
                AcarreoArea::create([
                    'reporte_frente_id' => $reporte->id,
                    // 'viajes' => $acarreoArea['viajes'],
                    'largo' => $acarreoArea['largo'],
                    'ancho' => $acarreoArea['ancho'],
                    'area' => $acarreoArea['area'],
                    'observaciones' => $acarreoArea['observaciones'],
                ]);
            }
        }

        // Guardar acarreos_metro_lineal si existen
        if (!empty($data['data']['acarreos_metro_lineal'])) {
            foreach ($data['data']['acarreos_metro_lineal'] as $acarreoMetroLineal) {
                AcarreoMetroLineal::create([
                    'reporte_frente_id' => $reporte->id,
                    // 'viajes' => $acarreoMetroLineal['viajes'],
                    'largo' => $acarreoMetroLineal['largo'],
                    'observaciones' => $acarreoMetroLineal['observaciones'],
                ]);
            }
        }

        // Guardar acarreos_agua si existen
        if (!empty($data['data']['acarreos_agua'])) {
            foreach ($data['data']['acarreos_agua'] as $acarreoAgua) {
                AcarreoAgua::create([
                    'reporte_frente_id' => $reporte->id,
                    'maquinaria_id' => $acarreoAgua['pipa']['id'],
                    'origen_id' => $acarreoAgua['origen']['id'],
                    'destino_id' => $acarreoAgua['destino']['id'],
                    'viajes' => $acarreoAgua['viajes'],
                    'observaciones' => $acarreoAgua['observaciones'],
                ]);
            }
        }

        if (!empty($data['data']['maquinaria'])) {
            foreach ($data['data']['maquinaria'] as $maquinaria) {
                $reporteMaquinas = ReporteMaquinaria::create([
                    'reporte_frente_id' => $reporte->id,
                    'concepto_id' => $maquinaria['concepto']['id'],
                    'tipo_maquinaria_id' => $maquinaria['familia']['id'],
                    'maquinaria_id' => $maquinaria['maquinaria']['id'],
                    'operador_id' => $maquinaria['operador']['id'],
                    'observaciones' => $maquinaria['observaciones'],
                    'horometro_inicial' => $maquinaria['horometro']['horometro_inicial'],
                    'horometro_final' => $maquinaria['horometro']['horometro_final'],
                ]);

                // Guardar horómetro
                $horometro1 = Horometro::create([
                    'maquinaria_id' => $maquinaria['maquinaria']['id'],
                    'horometro_inicial' => $maquinaria['horometro']['horometro_inicial'],
                    'horometro_final' => $maquinaria['horometro']['horometro_final'],
                ]);

                // Insertar el segundo horómetro con horómetro_final vacío
                $horometro2 = Horometro::create([
                    'maquinaria_id' => $maquinaria['maquinaria']['id'],
                    'horometro_inicial' => $horometro1->horometro_final,
                    'horometro_final' => null,
                ]);
            }
            //Log::info('Maquinas creado:', $reporteMaquinas->toArray()); // Registrar el reporte creado
            //Log::info('Horometros creado:', $horometros->toArray()); // Registrar el reporte creado
        }

        if (!empty($data['data']['personal'])) {
            foreach ($data['data']['personal'] as $persona) {
                ReportePersonal::create([
                    'reporte_frente_id' => $reporte->id,
                    'personal_id' => $persona['personal']['id'],
                ]);
            }
            //Log::info('Personal creado:', $reporteMaquinas->toArray());
        }

        if (!empty($data['data']['fotografias'])) {
            foreach ($data['data']['fotografias'] as $fotoData) {
                // Ahora esperamos un array con imagen y descripción
                $fotografia = is_array($fotoData) ? $fotoData : ['image' => $fotoData, 'description' => ''];

                // Decodificar la imagen base64
                $image = base64_decode($fotografia['image']);
                // $imageName = 'foto_' . time() . '_' . uniqid() . '.jpg';
                // $path = public_path('images/' . $imageName);
                // file_put_contents($path, $image);
                $image = base64_decode($fotografia['image']);
                $tempPath = tempnam(sys_get_temp_dir(), 'cloud');
                file_put_contents($tempPath, $image);

                $uploaded = $cloudinary->uploadApi()->upload($tempPath, [
                    'folder' => 'fotografias_reportes',
                    'use_filename' => true,
                    'unique_filename' => true,
                ]);

                $imageUrl = $uploaded['secure_url'];

                // Guardar en la base de datos con descripción
                $reportefotos = ReporteFotografia::create([
                    'reporte_frente_id' => $reporte->id,
                    // 'url' => 'images/' . $imageName,
                    'url' => $imageUrl,
                    'descripcion' => $fotografia['description'] ?? null,
                ]);
            }

            //Log::info('Imagenes:', $reportefotos->toArray());
        }

        /*if (!empty($data['data']['fotografias'])) {
            foreach ($data['data']['fotografias'] as $fotografia) {
                // Decodificar la imagen base64
                $image = base64_decode($fotografia);
                $imageName = 'foto_' . time() . '_' . uniqid() . '.jpg'; // Nombre único para la imagen
                $path = public_path('images/' . $imageName); // Ruta donde se guardará la imagen

                // Guardar la imagen en el servidor
                file_put_contents($path, $image);

                // Guardar la ruta de la imagen en la base de datos
                ReporteFotografia::create([
                    'reporte_frente_id' => $reporte->id,
                    'url' => 'images/' . $imageName, // Ruta relativa de la imagen
                ]);
            }
        }*/

        return response()->json([
            'success' => true,
            'message' => 'Reporte guardado exitosamente',
        ]);
    }
}
