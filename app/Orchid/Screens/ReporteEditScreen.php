<?php

namespace App\Orchid\Screens;

use App\Models\Conceptos;
use App\Models\ReporteJefeFrente;
use App\Orchid\Layouts\AcarreoAguaListLayout;
use App\Orchid\Layouts\AcarreoAreaListLayout;
use App\Orchid\Layouts\AcarreoMetroLinealListLayout;
use App\Orchid\Layouts\AcarreoVolumenListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Support\Facades\Layout;

class ReporteEditScreen extends Screen
{
    public $reporte;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(ReporteJefeFrente $reporte): iterable
    {
        $reporte->load([
            'acarreosVolumen',
            'acarreosArea',
            'acarreosMetroLineal',
            'acarreosAgua',
        ]);

        $obraId = session('obra_id');

        $conceptos = Conceptos::where('tipo', 'CAPITULO')
            ->where('obra_id', $obraId)
            ->orderBy('descripcion')
            ->get(['id', 'descripcion', 'factor_abundamiento']);

        return [
            'reporte' => $reporte,
            'acarreosVolumen' => $reporte->acarreosVolumen,
            'acarreosArea' => $reporte->acarreosArea,
            'acarreosMetroLineal' => $reporte->acarreosMetroLineal,
            'acarreosAgua' => $reporte->acarreosAgua,
            'conceptos' => $conceptos,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Reporte de jefes de frente';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::legend('reporte', [
                Sight::make('hora_inicio_real_actividades', 'Hora real de las actividades'),
                Sight::make('hora_termino_real_actividades', 'Hora real del termino '),
                Sight::make('observaciones', 'Observaciones'),
            ]),

            Layout::block(AcarreoVolumenListLayout::class)
                ->title('Volumen'),

            Layout::block(AcarreoAreaListLayout::class)
                ->title('Área'),

            Layout::block(AcarreoMetroLinealListLayout::class)
                ->title('Metro Lineal'),

            Layout::block(AcarreoAguaListLayout::class)
                ->title('Acarreos de Agua'),
        ];
    }
}
