<?php

namespace App\Orchid\Screens;

use App\Models\CatalogoCamionAcarreo;
use App\Orchid\Layouts\ExcelImportLayout;
use App\Orchid\Layouts\OrigenListLayout;
use App\Orchid\Layouts\TiposCamionListLayout;
use Illuminate\Http\Request;
use Orchid\Attachment\Models\Attachment;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TiposCamionListScreen extends Screen
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
            'tipos_camion' => CatalogoCamionAcarreo::where('obra_id', $obraId)->orderBy('nombre')->get()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Tipos de camión';
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
                ->route('platform.tipo.camion.edit')
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [TiposCamionListLayout::class];
    }

    public function delete(CatalogoCamionAcarreo $tipo_camion)
    {
        $tipo_camion->delete();
    }
}
