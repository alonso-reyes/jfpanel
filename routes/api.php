<?php

use App\Http\Controllers\Api\AcarreosAguaApiController;
use App\Http\Controllers\Api\AcarreosVolumenApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CamionesApiController;
use App\Http\Controllers\Api\CatalogoCamionesApiController;
use App\Http\Controllers\Api\ConceptosApiController;
use App\Http\Controllers\Api\GeneralesApiController;
use App\Http\Controllers\Api\MaterialApiController;
use App\Http\Controllers\Api\ObraApiController;
use App\Http\Controllers\Api\OrigenesApiController;
use App\Http\Controllers\Api\PersonalApiController;
use App\Http\Controllers\Api\TurnosApiController;
use App\Http\Controllers\Api\ZonasTrabajoApiController;
use App\Http\Controllers\Api\ZonaTrabajoApiController;
use App\Http\Controllers\Api\ZonaTrabajoController;
use App\Http\Controllers\Api\ReporteJefeFrenteApiController;
use App\Http\Controllers\Api\ReporteWhatsappApiController;
use App\Http\Controllers\Api\TipoMaquinariaApiController;
use App\Models\Conceptos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::post('/login2', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::get('/', function () {
//     return 'API WORKIN';
// });

Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API funcionando',
        'data' => [
            'server_time' => now()->toDateTimeString(),
            'client_ip' => request()->ip(),
            'request_headers' => request()->headers->all()
        ]
    ]);
});

// Jefe de frente
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/getObra', [ObraApiController::class, 'getObra'])->middleware('auth:sanctum');

Route::post('/getTurnos', [TurnosApiController::class, 'getTurnos'])->middleware('auth:sanctum');

Route::post('/getZonasTrabajo', [ZonasTrabajoApiController::class, 'get_zonas_trabajo'])->middleware('auth:sanctum');

Route::post('/getMateriales', [MaterialApiController::class, 'get_materiales'])->middleware('auth:sanctum');

Route::post('/getUsoMateriales', [MaterialApiController::class, 'get_usos_materiales'])->middleware('auth:sanctum');

Route::post('/getOrigenes', [OrigenesApiController::class, 'get_origenes'])->middleware('auth:sanctum');

Route::post('/getDestinos', [OrigenesApiController::class, 'get_destinos'])->middleware('auth:sanctum');

Route::post('/getConceptos', [ConceptosApiController::class, 'get_conceptos'])->middleware('auth:sanctum');

Route::post('/getCamiones', [CamionesApiController::class, 'get_camiones'])->middleware('auth:sanctum');

Route::post('/getCatalogoCamionesAcarreo', [CatalogoCamionesApiController::class, 'get_catalogo_camiones_volumen'])->middleware('auth:sanctum');

Route::post('/getCatalogosVolumen', [AcarreosVolumenApiController::class, 'get_catalogos_volumen'])->middleware('auth:sanctum');

Route::post('/getPipas', [AcarreosAguaApiController::class, 'get_pipas'])->middleware('auth:sanctum');

Route::post('/getCatalogosAgua', [AcarreosAguaApiController::class, 'get_catalogo_acarreos_agua'])->middleware('auth:sanctum');

Route::post('/getTiposMaquinaria', [TipoMaquinariaApiController::class, 'get_tipos_maquinaria'])->middleware('auth:sanctum');

Route::post('/getCatalogoGenerales', [GeneralesApiController::class, 'get_catalogo_generales'])->middleware('auth:sanctum');

Route::post('/getPersonal', [PersonalApiController::class, 'get_personal'])->middleware('auth:sanctum');

Route::post('/guardar_reporte', [ReporteJefeFrenteApiController::class, 'guardarReporte'])->middleware('auth:sanctum');

// Reportes
Route::post('/reporte_diario_whatsapp', [ReporteWhatsappApiController::class, 'reporteDiarioWhastapp'])->middleware('auth:sanctum');

Route::post('/enviar_reporte_diario', [ReporteWhatsappApiController::class, 'reporteDiarioWhastappPDF'])->middleware('auth:sanctum');



// Route::post('/getTurnos', [TurnosApiController::class, 'getTurnos'])->middleware('auth:sanctum');

// Route::get('/zonas-trabajo', [ZonaTrabajoController::class, 'index']);
// Route::get('/zonas-trabajo/{id}', [ZonaTrabajoController::class, 'show']);