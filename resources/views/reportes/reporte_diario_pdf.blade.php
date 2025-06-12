<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Diario de Obra</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .logo {
            max-width: 120px;
            margin-bottom: 10px;
        }

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

        h1 {
            color: #2c3e50;
            font-size: 20px;
            margin: 0;
            padding: 0;
        }

        h2 {
            color: #2c3e50;
            font-size: 16px;
            margin: 10px 0;
            padding: 0;
            border-bottom: 1px solid #eee;
        }

        .info-obra {
            margin-bottom: 20px;
        }

        .info-obra p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .seccion {
            margin-bottom: 20px;
        }

        .totales {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .page-break {
            page-break-after: always;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            font-size: 10px;
            text-align: center;
        }
    </style>
</head>

<body>
    <table class="tabla-sin-bordes" width="100%" style="border-top:none;border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
        <tr>
            <td style="vertical-align: middle; width: 50%;">
                <img src="{{ $datos['logoPath'] }}" style="width: 150px; height: auto;" alt="Logo">
            </td>
            <td style="text-align: right; vertical-align: middle; width: 50%;">
                <h1 style="margin: 0; font-size: 18pt;">Reporte Diario de Avance</h1>
                <p style="margin: 5px 0 0 0; font-size: 10pt;">Fecha: {{ $datos['fecha'] }}</p>
            </td>
        </tr>
    </table>


    <div class="info-obra">
        <h2>Información de la Obra</h2>
        <p><strong>Nombre:</strong> {{ $datos['obra']->nombre }}</p>
        <p><strong>Ubicación:</strong> {{ $datos['obra']->ubicacion }}</p>
    </div>

    <div class="seccion">
        <h2>Resumen de Maquinaria</h2>
        <p><strong>Equipos activos:</strong> {{ $datos['total_maquinaria'] }}</p>

        @if(count($datos['maquinaria_por_tipo']) > 0)
        <table>
            <thead>
                <tr>
                    <th>Tipo de Maquinaria</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos['maquinaria_por_tipo'] as $maquinaria)
                <tr>
                    <td>{{ $maquinaria['tipo'] }}</td>
                    <td>{{ $maquinaria['total'] }}</td>
                </tr>
                @endforeach
                <tr class="totales">
                    <td>Total</td>
                    <td>{{ $datos['total_maquinaria'] }}</td>
                </tr>
            </tbody>
        </table>
        @else
        <p>No hay maquinaria registrada para esta fecha.</p>
        @endif
    </div>


    <div class="seccion">
        <h2>Resumen de Personal</h2>
        <p><strong>Personal activo:</strong> {{ $datos['total_personal'] }}</p>

        @if(count($datos['personal_por_puesto']) > 0)
        <table>
            <thead>
                <tr>
                    <th>Puesto</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos['personal_por_puesto'] as $personal)
                <tr>
                    <td>{{ $personal['puesto'] }}</td>
                    <td>{{ $personal['total'] }}</td>
                </tr>
                @endforeach
                <tr class="totales">
                    <td>Total</td>
                    <td>{{ $datos['total_personal'] }}</td>
                </tr>
            </tbody>
        </table>
        @else
        <p>No hay personal registrado para esta fecha.</p>
        @endif
    </div>


    <div class="seccion">
        <h2>Resumen de Acarreos</h2>

        @if(isset($datos['acarreos']['volumen']) && $datos['acarreos']['volumen'])
        <h3>Volúmenes</h3>
        <p><strong>Total de Viajes:</strong> {{ $datos['acarreos']['volumen']->total_viajes ?? 0 }}</p>
        <p><strong>Capacidad Total:</strong> {{ $datos['acarreos']['volumen']->total_capacidad ?? 0 }} m³</p>
        <p><strong>Volumen Total:</strong> {{ $datos['acarreos']['volumen']->total_volumen ?? 0 }} m³</p>

        @if(count($datos['acarreos']['detalles_volumen']) > 0)
        <table>
            <thead>
                <tr>
                    <th>Material</th>
                    <th>Uso</th>
                    <th>Volumen (m³)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos['acarreos']['detalles_volumen'] as $detalle)
                <tr>
                    <td>{{ $detalle['material'] }}</td>
                    <td>{{ $detalle['uso'] }}</td>
                    <td>{{ $detalle['volumen'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
        @endif

        @if($datos['acarreos']['area'] > 0)
        <h3>Áreas</h3>
        <p><strong>Área Total:</strong> {{ $datos['acarreos']['area'] }} m²</p>
        @endif

        @if($datos['acarreos']['metro_lineal'] > 0)
        <h3>Metros Lineales</h3>
        <p><strong>Total de Metros Lineales:</strong> {{ $datos['acarreos']['metro_lineal'] }} m</p>
        @endif

        @if(!isset($datos['acarreos']['volumen']) && $datos['acarreos']['area'] == 0 && $datos['acarreos']['metro_lineal'] == 0)
        <p>No hay acarreos registrados para esta fecha.</p>
        @endif
    </div>

    <div class="footer">
        <p>Reporte generado el {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>

</html>