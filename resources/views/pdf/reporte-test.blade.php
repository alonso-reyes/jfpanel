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
            background-color: black;
            color: white;
            text-align: left;
            font-size: 14px;

            /* Tamaño de letra del encabezado */
        }

        .table-custom th,
        .table-custom td {
            padding: 8px;
            border: 1px solid #ddd;
            line-height: 1;
            height: 24px;
        }

        .table-custom tbody td {
            font-size: 12px;
            /* Tamaño de letra más pequeño solo en las celdas */
        }

        .table-custom tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

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


    @if($reporte->acarreosVolumen->count() > 0)
    <div class="section">
        <div class="section-title">CONTROL DE ACARREOS POR VOLUMEN</div>
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Material</th>
                    <th>Uso del material</th>
                    <th>No. Viajes</th>
                    <th>Camión</th>
                    <th>Origen</th>
                    <th>Destino</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reporte->acarreosVolumen as $acarreo)
                <tr>
                    <td>{{ $acarreo->material->material ?? 'N/A' }}</td>
                    <td>{{ $acarreo->materialUso->uso ?? 'N/A' }}</td>
                    <td>{{ $acarreo->viajes }}</td>
                    <td>{{ $acarreo->camion->clave }}</td>
                    <td>{{ $acarreo->origen->origen ?? 'N/A' }}</td>
                    <td>{{ $acarreo->destino->destino ?? 'N/A' }}</td>
                    <td>{{ $acarreo->observaciones }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif


</body>

</html>