<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Dashboard $dashboard
     *
     * @return void
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        // ...
        $dashboard->registerResource('stylesheets', [
            asset('css/app.css'),
        ]);
    }

    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function menu(): array
    {
        return [

            Menu::make('Conceptos')
                ->icon('bs.list')
                ->title('Gestión de Catálogos')
                ->route('platform.concepto.list'),

            Menu::make('Zonas de trabajo')
                ->icon('bs.geo-alt-fill')
                ->route('platform.zona.trabajo.list'),

            Menu::make('Origenes')
                ->icon('bs.flag')
                ->route('platform.origen.list'),

            Menu::make('Destinos')
                ->icon('bs.flag-fill')
                ->route('platform.destino.list'),

            Menu::make('Turnos')
                ->icon('bs.clock')
                ->route('platform.turno.list'),

            Menu::make('Maquinaria')
                ->icon('bs.tools')
                ->route('platform.tipo.maquinaria.list')
                ->list([
                    Menu::make('Familia')
                        ->icon('bs.wrench')
                        ->route('platform.tipo.maquinaria.list'),

                    Menu::make('Operadores')
                        ->icon('bs.person-fill')
                        ->route('platform.operador.list'),

                    Menu::make('Equipos')
                        ->icon('bs.truck-flatbed')
                        ->route('platform.maquinaria.list')
                ]),

            Menu::make('Camiones')
                ->icon('bs.truck-flatbed')
                ->route('platform.camion.list'),

            Menu::make('Jefes de frente')
                ->icon('bs.person-plus')
                ->route('platform.usuarios.jefes.frente.list'),

            Menu::make('Materiales')
                ->icon('bs.nut-fill')
                ->route('platform.material.list')
                ->list([
                    Menu::make('Tipo de material')
                        ->icon('bs.nut-fill')
                        ->route('platform.material.list'),

                    Menu::make('Uso de material')
                        ->icon('bs.nut')
                        ->route('platform.uso.material.list'),
                ]),

            Menu::make('Personal y puestos')
                ->icon('bs.people-fill')
                ->route('platform.puesto.list')
                ->list([
                    Menu::make('Puesto')
                        ->icon('bs.person-badge-fill')
                        ->route('platform.puesto.list'),

                    Menu::make('Personal')
                        ->icon('bs.person-fill')
                        ->route('platform.personal.list'),
                ]),


            //// Reportes

            Menu::make(__('Reporte de actividades diarias'))
                ->icon('bs.clipboard2-check')
                ->route('platform.reportes.list')
                ->title(__('Reportes')),


            /////////////////////////////////

            // Menu::make('Get Started')
            //     ->icon('bs.book')
            //     ->title('Navigation')
            //     //->route('/Resources/PostResource.php'),
            //     ->route(config('platform.index')),

            // Menu::make('Sample Screen')
            //     ->icon('bs.collection')
            //     ->route('platform.example')
            //     ->badge(fn () => 6),

            // Menu::make('Form Elements')
            //     ->icon('bs.card-list')
            //     ->route('platform.example.fields')
            //     ->active('*/examples/form/*'),

            // Menu::make('Overview Layouts')
            //     ->icon('bs.window-sidebar')
            //     ->route('platform.example.layouts'),

            // Menu::make('BUAH CHAVALON')
            //     ->icon('bs.window-sidebar')
            //     ->route('platform.post.list'),

            // Menu::make('Grid System')
            //     ->icon('bs.columns-gap')
            //     ->route('platform.example.grid'),

            // Menu::make('Charts')
            //     ->icon('bs.bar-chart')
            //     ->route('platform.example.charts'),

            // Menu::make('Cards')
            //     ->icon('bs.card-text')
            //     ->route('platform.example.cards')
            //     ->divider(),

            Menu::make(__('Users'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Access Controls')),

            Menu::make(__('Roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles')
                ->divider(),



            // Menu::make('Documentation')
            //     ->title('Docs')
            //     ->icon('bs.box-arrow-up-right')
            //     ->url('https://orchid.software/en/docs')
            //     ->target('_blank'),

            // Menu::make('Changelog')
            //     ->icon('bs.box-arrow-up-right')
            //     ->url('https://github.com/orchidsoftware/platform/blob/master/CHANGELOG.md')
            //     ->target('_blank')
            //     ->badge(fn () => Dashboard::version(), Color::DARK),
        ];
    }

    /**
     * Register permissions for the application.
     *
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),
        ];
    }
}
