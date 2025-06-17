<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Reporte #{{ $reporte->id }}</title>
    <style>
        .tabla-sin-bordes {
            width: 100%;
            border-collapse: collapse;
            border: none !important;
        }

        .tabla-sin-bordes td,
        .tabla-sin-bordes th {
            border: none;
            padding: 2px 5px;
            /* Reduce el espacio interno (arriba/abajo - izquierda/derecha) */
            line-height: 1.2;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: small;
            margin: 0;
            padding: 0;
        }

        .header-container {
            padding-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .logo {
            width: 150px;
            height: auto;
        }

        .header-info {
            text-align: center;
            flex-grow: 1;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
        }

        .section {
            margin-bottom: 15px;
        }

        .section-title {
            font-weight: bold;
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        /* Para las tablas */
        .table-custom {
            width: 100%;
            border-collapse: collapse;
        }

        .table-custom thead {
            background-color: #f2f2f2;
            text-align: left;
            font-size: 14px;
            height: 24px;
        }

        .table-custom thead tr {
            line-height: 1;
            padding: 0;
            margin: 0;
        }

        .table-custom th {
            padding: 2px 4px;
            font-size: 12px;
            text-align: center;
        }

        .table-custom td {
            padding: 8px;
            border: 1px solid #ddd;
            line-height: 1;
        }

        .table-custom tbody td {
            font-size: 12px;
            /* Tamaño de letra más pequeño solo en las celdas */
        }

        /* .table-custom tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        } */

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <!-- Encabezado con logo -->
    <table class="tabla-sin-bordes">
        <tr>
            <!-- Logo con rowspan para ocupar ambas filas -->
            <td rowspan="3" style="width: 150px; text-align: center; vertical-align: middle;">
                <img src="{{ $logoPath }}" class="logo" style="width: 150px; height: auto;" alt="Logo Empresa">
            </td>

            <!-- Información de la obra -->
            <td style="text-align: right; font-size: 14px;">
                <strong>Obra:</strong>
            </td>

            <td style="text-align: left; font-size: 14px;">
                {{ $reporte->obra->nombre ?? 'N/A' }}
            </td>

        </tr>
        <tr>
            <td style="text-align: right; font-size: 14px;">
                <strong>Ubicación:</strong>
            </td>

            <td style="text-align: left; font-size: 14px;">
                {{ $reporte->obra->ubicacion ?? 'N/A' }}
            </td>
        </tr>
        <tr>
            <td style="text-align: right; font-size: 14px;">
                <strong>Fecha:</strong>
            </td>

            <td style="text-align: left; font-size: 14px;">
                {{ $reporte->created_at->format('d/m/Y H:i') }}
            </td>
        </tr>
    </table>

    <div class="header-container">
        <div class="header-info">
            <div class="title">REPORTE DE ACTIVIDADES DIARIAS</div>
            <!-- <div>Fecha: {{ $reporte->created_at->format('d/m/Y H:i') }}</div> -->
        </div>
        <div style="width: 150px;"></div> <!-- Espacio para alinear -->
    </div>

    <div class="section">
        <div class="section-title">INFORMACIÓN BÁSICA</div>
        <table>
            <tr>
                <td><strong>Turno:</strong></td>
                <td>{{ $reporte->turno->nombre_turno ?? 'N/A' }}</td>
                <td><strong>Hora de inicio Actividades:</strong></td>
                <td>{{ $reporte->hora_inicio_real_actividades }}</td>

            </tr>
            <tr>
                <td><strong>Zona de Trabajo:</strong></td>
                <td>{{ $reporte->zonaTrabajo->nombre ?? 'N/A' }}</td>
                <td><strong>Hora de término Actividades:</strong></td>
                <td>{{ $reporte->hora_termino_real_actividades }}</td>
            </tr>
            <tr>
                <td><strong>Jefe de Frente:</strong></td>
                <td>{{ $reporte->usuario_jefe_frente->nombre ?? 'N/A' }}</td>
                <td><strong>Sobrestante:</strong></td>
                <td>{{ $reporte->sobrestante }}</td>

            </tr>
        </table>
    </div>

    <!-- @php
    // Pre-filtramos los dibujos que tienen ruta_imagen
    $dibujosConImagen = $reporte->dibujosZonaTrabajo->filter(function($dibujo) {
    return !empty($dibujo->ruta_imagen);
    });
    @endphp -->

    <!-- @if(!empty($imagenesDibujos))
    <div class="section">
        <div style="margin: 0 auto;">
            <table style="border-collapse: collapse;">
                <tbody>
                    @foreach($dibujosConImagen as $imagen_zona)
                    @php
                    $imageSrc = null;
                    if(!empty($imagen_zona->ruta_imagen)) {
                    try {
                    $filename = basename($imagen_zona->ruta_imagen);
                    $publicPath = public_path('images/zona_trabajo/'.$filename);

                    if(file_exists($publicPath)) {
                    $imageData = file_get_contents($publicPath);
                    $imageSrc = 'data:image/jpeg;base64,'.base64_encode($imageData);
                    }
                    } catch(Exception $e) {
                    Log::error("Error cargando imagen: ".$e->getMessage());
                    }
                    }
                    @endphp

                    <tr>
                        <td style="padding: 10px; box-sizing: border-box;">
                            <div style="background: #f5f5f5;">
                                @if($imageSrc)
                                <img src="{{ $imageSrc }}"
                                    style="position: relative;
                                        top: 50%;
                                        left: 50%;
                                        transform: translate(-50%, -50%);
                                        max-width: 100%;
                                        max-height: 100%;
                                        display: block;">
                                @else
                                <div style="position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                    <p style="color: #999;">Imagen no disponible</p>
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif -->

    @if(!empty($imageSrc))
    <div class="section">
        <div style="text-align: center; margin: 0; padding: 0; display: block; font-size: 0; line-height: 0;">
            <div style="overflow: hidden; max-width: 100%;">
                <img src="{{ $imageSrc }}" style="max-width: 100%; height: auto; display: block; margin: 0;">
            </div>
        </div>
    </div>
    @else
    <div class="section">
        <div style="text-align: center; color: #999; font-size: 0; line-height: 0;">
            <p style="font-size: 16px;">Imagen no disponible</p>
        </div>
    </div>
    @endif



    @if($reporte->acarreosVolumen->count() > 0)
    <div class="section">
        <div class="section-title">CONTROL DE ACARREOS POR VOLUMEN</div>
        <table class="table-custom">
            <thead>
                <tr style="line-height: 1; padding: 0; margin: 0;">
                    <th style="padding: 2px 4px;">Material</th>
                    <th style="padding: 2px 4px;">Uso del material</th>
                    <th style="padding: 2px 4px;">No. Viajes</th>
                    <th style="padding: 2px 4px;">Camión</th>
                    <th style="padding: 2px 4px;">Origen</th>
                    <th style="padding: 2px 4px;">Destino</th>
                    <th style="padding: 2px 4px;">Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reporte->acarreosVolumen as $acarreo)
                <tr>
                    <td>{{ $acarreo->material->material ?? 'N/A' }}</td>
                    <td>{{ $acarreo->materialUso->uso ?? 'N/A' }}</td>
                    <td>{{ $acarreo->viajes }}</td>
                    <td>{{ $acarreo->catalogo_camion->nombre }}</td>
                    <td>{{ $acarreo->origen->origen ?? 'N/A' }}</td>
                    <td>{{ $acarreo->destino->destino ?? 'N/A' }}</td>
                    <td>{{ $acarreo->observaciones }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($reporte->acarreosArea->count() > 0)
    <div class="section">
        <div class="section-title">CONTROL DE ACARREOS POR ÁREA OPCIONAL</div>
        <table class="table-custom">
            <thead>
                <tr style="line-height: 1; padding: 0; margin: 0;">
                    <th style="padding: 2px 4px;">Largo</th>
                    <th style="padding: 2px 4px;">Ancho</th>
                    <th style="padding: 2px 4px;">Área</th>
                    <th style="padding: 2px 4px;">Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reporte->acarreosArea as $acarreo)
                <tr>
                    <td>{{ $acarreo->largo }}</td>
                    <td>{{ $acarreo->ancho }}</td>
                    <td>{{ $acarreo->area }}</td>
                    <td>{{ $acarreo->observaciones }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($reporte->acarreosMetroLineal->count() > 0)
    <div class="section">
        <div class="section-title">CONTROL DE ACARREOS POR METRO LINEAL</div>
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Largo</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reporte->acarreosMetroLineal as $acarreo)
                <tr>
                    <td>{{ $acarreo->largo }}</td>
                    <td>{{ $acarreo->observaciones }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($reporte->acarreosAgua->count() > 0)
    <div class="section">
        <div class="section-title">CONTROL DE ACARREOS DE AGUA</div>
        <table class="table-custom">
            <thead>
                <tr>
                    <th>No. económico</th>
                    <th>Viajes</th>
                    <th>Origen</th>
                    <th>Destino</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reporte->acarreosAgua as $acarreo)
                <tr>
                    <td>{{ $acarreo->maquinaria->numero_economico }}</td>
                    <td>{{ $acarreo->viajes }}</td>
                    <td>{{ $acarreo->origen->origen ?? 'N/A' }}</td>
                    <td>{{ $acarreo->destino->destino ?? 'N/A' }}</td>
                    <td>{{ $acarreo->observaciones }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="section">
        <div class="section-title">OBSERVACIONES</div>
        <p>{{ $reporte->observaciones }}</p>
    </div>

    <!-- Maquinaria y equipo -->
    @if($maquinas->count() > 0)
    <div class="section">
        <div class="section-title">MAQUINARIA Y EQUIPO</div>
        <table class="table-custom">
            <thead>
                <tr>
                    <th rowspan="2">Actividad/descripción</th>
                    <th rowspan="2">Número económico</th>
                    <th rowspan="2">Operador</th>
                    <th colspan="2" style="text-align: center;">Horómetros</th>
                </tr>
                <tr>
                    <th style="text-align: center;">Inicial</th>
                    <th style="text-align: center;">Final</th>
                </tr>
            </thead>
            <tbody>
                @foreach($maquinas as $maquina)
                @php
                $registros = $reporte->reporteMaquinaria->where('maquinaria_id', $maquina->id);
                @endphp

                @if($registros->count() > 0)
                @foreach($registros as $registro)
                <tr>
                    <td>{{ $registro->concepto->nombre ?? '' }}</td>
                    <td>{{ $maquina->numero_economico }}</td>
                    <td>{{ $registro->operador->nombre ?? '' }}</td>
                    <td>{{ $registro->horometro_inicial ?? '' }}</td>
                    <td>{{ $registro->horometro_final ?? '' }}</td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td></td>
                    <td>{{ $maquina->numero_economico }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- @if($reporte->reporteMaquinaria->count() > 0)
    <div class="section">
        <div class="section-title">MAQUINARIA Y EQUIPO</div>
        <table class="table-custom">
            <thead>
                <tr>
                    <th rowspan="2">Actividad/descripción</th>
                    <th rowspan="2">Número económico</th>
                    <th rowspan="2">Operador</th>
                    <th colspan="2" style="text-align: center;">Horómetros</th>
                </tr>
                <tr>
                    <th style="text-align: center;">Inicial</th>
                    <th style="text-align: center;">Final</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reporte->reporteMaquinaria as $maquinaria)
                @php
                // Obtener el último horómetro registrado para esta máquina
                $ultimoHorometro = $maquinaria->maquinaria->horometros->last();
                @endphp
                <tr>
                    <td>{{ $maquinaria->concepto->nombre }}</td>
                    <td>{{ $maquinaria->maquinaria->numero_economico }}</td>
                    <td>{{ $maquinaria->operador->nombre ?? 'N/A' }}</td>
                    <td>{{ $maquinaria->horometro_inicial ?? '' }}</td>
                    <td>{{ $maquinaria->horometro_final ?? '' }}</td>
             <td>{{ $ultimoHorometro->horometro_inicial ?? 'N/A' }}</td>
                    <td>{{ $ultimoHorometro->horometro_final ?? 'N/A' }}</td> -->
    <!-- </tr>
    @endforeach
    </tbody>
    </table>
    </div>
    @endif -->



    <!-- Personal -->
    @if($reporte->reportePersonal->count() > 0)
    <div class="section">
        <div class="section-title">Personal</div>
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Cargo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reporte->reportePersonal as $personal)
                <tr>
                    <td>{{ $personal->personal->nombre }}</td>
                    <td>{{ $personal->personal->puesto->puesto }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Fotografias -->
    @if($reporte->fotografias->count() > 0)
    <div class="section">
        <div class="section-title">Soporte fotográfico</div>

        <!-- Contenedor principal con ancho fijo -->
        <div style="width: 100%; max-width: 800px; margin: 0 auto;">
            <!-- Tabla de 2 columnas para control preciso -->
            <table style="width: 100%; border-collapse: collapse;">
                <tbody>
                    @foreach(array_chunk($reporte->fotografias->all(), 2) as $pair)
                    <tr>
                        @foreach($pair as $foto)
                        <td style="width: 50%; padding: 5px; vertical-align: top;">
                            @php
                            /*$imageSrc = null;
                            foreach ($reporte->dibujosZonaTrabajo as $dibujo) {
                            if (!empty($dibujo->ruta_imagen)) {
                            $imageSrc = $dibujo->ruta_imagen;
                            break; // Tomamos la primera imagen válida y salimos del bucle
                            }
                            }*/
                            $imageSrc = $foto->url;
                            @endphp

                            <!-- Contenedor de imagen con tamaño fijo -->
                            <div style="width: 100%; max-width: 350px; margin: 0 auto;">
                                @if($imageSrc)
                                <img src="{{ $imageSrc }}" style="width: 100%; height: 200px; object-fit: cover; display: block; border-radius: 4px;">
                                @if($foto->descripcion)
                                <p style="text-align: center; font-size: 12px; margin: 5px 0 10px; padding: 0 5px;">{{ $foto->descripcion }}</p>
                                @endif
                                @else
                                <div style="width: 100%; height: 200px; background: #f5f5f5; display: flex; justify-content: center; align-items: center; border-radius: 4px;">
                                    <p style="color: red; font-size: 12px;">Imagen no disponible</p>
                                </div>
                                @endif
                            </div>
                        </td>
                        @endforeach

                        <!-- Completar fila si hay número impar de imágenes -->
                        @if(count($pair) < 2)
                            <td style="width: 50%;">
                            </td>
                            @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif


    <!-- Sección de Firmas -->
    <div style="position: absolute; bottom: 50px; width: 100%; left: 0;">
        <table style="width: 100%; border: none; border-collapse: collapse;">
            <tr>
                <!-- Firma Jefe de Frente -->
                <td style="width: 33%; text-align: center; vertical-align: top; border: none;">
                    <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto; padding-top: 5px;"></div>
                    <p style="margin-top: 5px; font-weight: bold;">JEFE DE FRENTE</p>
                    <p style="margin-top: 5px; font-size: smaller;">{{ $reporte->usuario_jefe_frente->nombre ?? '' }}</p>
                </td>

                <!-- Firma Residente -->
                <td style="width: 33%; text-align: center; vertical-align: top; border: none;">
                    <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto; padding-top: 5px;"></div>
                    <p style="margin-top: 5px; font-weight: bold;">RESIDENTE</p>
                    <p style="margin-top: 5px; font-size: smaller;">Nombre</p>
                </td>

                <!-- Firma Control de Obra -->
                <td style="width: 33%; text-align: center; vertical-align: top; border: none;">
                    <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto; padding-top: 5px;"></div>
                    <p style="margin-top: 5px; font-weight: bold;">CONTROL DE OBRA</p>
                    <p style="margin-top: 5px; font-size: smaller;">Nombre</p>
                </td>
            </tr>
        </table>
    </div>


</body>

</html>