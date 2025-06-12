<?php

declare(strict_types=1);

use App\Http\Controllers\ObraController;
use App\Http\Controllers\ReportePdfController;
use App\Models\Maquinaria;
use App\Orchid\Layouts\PostListLayout;
use App\Orchid\Layouts\PuestoListLayout;
use App\Orchid\Screens\CamionEditScreen;
use App\Orchid\Screens\CamionListScreen;
use App\Orchid\Screens\ConceptoEditScreen;
use App\Orchid\Screens\ConceptoListScreen;
use App\Orchid\Screens\DestinoEditScreen;
use App\Orchid\Screens\DestinoListScreen;
use App\Orchid\Screens\Examples\ExampleActionsScreen;
use App\Orchid\Screens\Examples\ExampleCardsScreen;
use App\Orchid\Screens\Examples\ExampleChartsScreen;
use App\Orchid\Screens\Examples\ExampleFieldsAdvancedScreen;
use App\Orchid\Screens\Examples\ExampleFieldsScreen;
use App\Orchid\Screens\Examples\ExampleGridScreen;
use App\Orchid\Screens\Examples\ExampleLayoutsScreen;
use App\Orchid\Screens\Examples\ExampleScreen;
use App\Orchid\Screens\Examples\ExampleTextEditorsScreen;
use App\Orchid\Screens\MaquinariaEditScreen;
use App\Orchid\Screens\MaquinariaListScreen;
use App\Orchid\Screens\MaterialEditScreen;
use App\Orchid\Screens\MaterialListScreen;
use App\Orchid\Screens\ObraCreateEditScreen;
use App\Orchid\Screens\OperadoresEditScreen;
use App\Orchid\Screens\OperadoresListScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\PostEditScreen;
use App\Orchid\Screens\PostListScreen;
use App\Orchid\Screens\PostScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\TipoMaquinaEditScreen;
use App\Orchid\Screens\TipoMaquinaListScreen;
use App\Orchid\Screens\TurnoEditScreen;
use App\Orchid\Screens\TurnoListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
//use Orchid\Platform\Dashboard;
use Orchid\Support\Facades\Dashboard;
use Tabuna\Breadcrumbs\Trail;
use App\Orchid\Screens\ObraSelectionScreen;
use App\Orchid\Screens\OrigenEditScreen;
use App\Orchid\Screens\OrigenListScreen;
use App\Orchid\Screens\PersonalEditScreen;
use App\Orchid\Screens\PersonalListScreen;
use App\Orchid\Screens\PuestoEditScreen;
use App\Orchid\Screens\PuestoListScreen;
use App\Orchid\Screens\ReporteListScreen;
use App\Orchid\Screens\UsoMaterialEditScreen;
use App\Orchid\Screens\UsoMaterialListScreen;
use App\Orchid\Screens\UsuariosJefesFrenteEditScreen;
use App\Orchid\Screens\UsuariosJefesFrenteListScreen;
use App\Orchid\Screens\ZonaTrabajoEditScreen;
use App\Orchid\Screens\ZonaTrabajoListScreen;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Route::middleware(['auth', 'checkObra'])->group(function () {
//     Route::screen('/main', PlatformScreen::class)
//         ->name('platform.main');
// });

// Seleccion de obra
//Route::view('dashboard', 'dashboard')->name('dashboard');
Route::get('/obra/select', [ObraController::class, 'select'])->name('obra.select');
Route::get('/obra/create', [ObraController::class, 'create'])->name('obra.create');
Route::get('/obra/{id}/edit', [ObraController::class, 'edit'])->name('obra.edit');
Route::put('/obra/{id}/update', [ObraController::class, 'update'])->name('obra.update');


Route::post('admin/obra/store', [ObraController::class, 'store'])->name('obra.store');
Route::post('/obra/set', [ObraController::class, 'setObra'])->name('obra.set');

// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users > User
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn(Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push($user->name, route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

// Platform > System > Users
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn(Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));

// Example...
Route::screen('example', ExampleScreen::class)
    ->name('platform.example')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Example Screen'));

Route::screen('/examples/form/fields', ExampleFieldsScreen::class)->name('platform.example.fields');
Route::screen('/examples/form/advanced', ExampleFieldsAdvancedScreen::class)->name('platform.example.advanced');
Route::screen('/examples/form/editors', ExampleTextEditorsScreen::class)->name('platform.example.editors');
Route::screen('/examples/form/actions', ExampleActionsScreen::class)->name('platform.example.actions');

Route::screen('/examples/layouts', ExampleLayoutsScreen::class)->name('platform.example.layouts');
Route::screen('/examples/grid', ExampleGridScreen::class)->name('platform.example.grid');
Route::screen('/examples/charts', ExampleChartsScreen::class)->name('platform.example.charts');
Route::screen('/examples/cards', ExampleCardsScreen::class)->name('platform.example.cards');

//Route::screen('obra/select', ObraSelectionScreen::class)->name('platform.obra.select');
//Route::screen('obra/create', ObraCreateEditScreen::class)->name('platform.obra.create');

//Route::screen('/posts', PostScreen::class)->name('platform.post');
Route::screen('posts', PostListScreen::class)->name('platform.post.list');
Route::screen('post/{post?}', PostEditScreen::class)->name('platform.post.edit');

Route::screen('conceptos', ConceptoListScreen::class)->name('platform.concepto.list');
Route::screen('concepto/{concepto?}', ConceptoEditScreen::class)->name('platform.concepto.edit');
Route::screen('conceptos/action', ConceptoListScreen::class)->name('platform.concepto.action');

Route::screen('zonas_trabajo', ZonaTrabajoListScreen::class)->name('platform.zona.trabajo.list');
Route::screen('zona_trabajo/{zona_trabajo?}', ZonaTrabajoEditScreen::class)->name('platform.zona.trabajo.edit');

Route::screen('turnos', TurnoListScreen::class)->name('platform.turno.list');
Route::screen('turno/{turno?}', TurnoEditScreen::class)->name('platform.turno.edit');

Route::screen('tipos_maquinaria', TipoMaquinaListScreen::class)->name('platform.tipo.maquinaria.list');
Route::screen('tipo_maquinaria/{tipo_maquinaria?}', TipoMaquinaEditScreen::class)->name('platform.tipo.maquinaria.edit');

Route::screen('operadores', OperadoresListScreen::class)->name('platform.operador.list');
Route::screen('operador/{operador?}', OperadoresEditScreen::class)->name('platform.operador.edit');

Route::screen('maquinarias', MaquinariaListScreen::class)->name('platform.maquinaria.list');
Route::screen('maquinaria/{maquinaria?}', MaquinariaEditScreen::class)->name('platform.maquinaria.edit');

Route::screen('usuarios_jf', UsuariosJefesFrenteListScreen::class)->name('platform.usuarios.jefes.frente.list');
Route::screen('usuario_jf/{usuariojf?}', UsuariosJefesFrenteEditScreen::class)->name('platform.usuarios.jefes.frente.edit');

Route::screen('materiales', MaterialListScreen::class)->name('platform.material.list');
Route::screen('material/{material?}', MaterialEditScreen::class)->name('platform.material.edit');

Route::screen('materiales_uso', UsoMaterialListScreen::class)->name('platform.uso.material.list');
Route::screen('material_uso/{uso?}', UsoMaterialEditScreen::class)->name('platform.uso.material.edit');

Route::screen('origenes', OrigenListScreen::class)->name('platform.origen.list');
Route::screen('origen/{origen?}', OrigenEditScreen::class)->name('platform.origen.edit');

Route::screen('destinos', DestinoListScreen::class)->name('platform.destino.list');
Route::screen('destino/{destino?}', DestinoEditScreen::class)->name('platform.destino.edit');

Route::screen('camiones', CamionListScreen::class)->name('platform.camion.list');
Route::screen('camion/{camion?}', CamionEditScreen::class)->name('platform.camion.edit');

Route::screen('puestos', PuestoListScreen::class)->name('platform.puesto.list');
Route::screen('puesto/{puesto?}', PuestoEditScreen::class)->name('platform.puesto.edit');

Route::screen('personales', PersonalListScreen::class)->name('platform.personal.list');
Route::screen('personal/{personal?}', PersonalEditScreen::class)->name('platform.personal.edit');

//////Reporte
Route::screen('reportes', ReporteListScreen::class)->name('platform.reportes.list');
Route::get('reportes/pdf/{reporte}', [ReportePdfController::class, 'generate'])->name('platform.reporte.pdf');

// Route::screen('posts', PostListLayout::class)
//     ->name('platform.post.layout');
//Route::screen('idea', Idea::class, 'platform.screens.idea');
