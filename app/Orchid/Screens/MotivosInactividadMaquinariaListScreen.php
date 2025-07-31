<?php

namespace App\Orchid\Screens;

use App\Models\MotivoInactividad;
use App\Orchid\Layouts\ExcelImportLayout;
use App\Orchid\Layouts\MotivosInactividadMaquinariaListLayout;
use App\Orchid\Layouts\OrigenListLayout;
use Illuminate\Http\Request;
use Orchid\Attachment\Models\Attachment;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use PhpOffice\PhpSpreadsheet\IOFactory;


class MotivosInactividadMaquinariaListScreen extends Screen
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
            'motivos_inactividad' => MotivoInactividad::where('obra_id', $obraId)->orderBy('motivo_inactividad')->get()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Listado de motivos de inactividad de la maquinaria';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Agregar')
                ->icon('plus')
                ->route('platform.motivo.inactividad.edit')
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
            MotivosInactividadMaquinariaListLayout::class
        ];
    }

    public function delete(MotivoInactividad $motivo_inactividad)
    {
        $motivo_inactividad->delete();
    }
}
