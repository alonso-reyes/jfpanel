<?php
// app/Imports/DynamicImport.php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DynamicImport 
{
    protected $obraId;
    protected $model;
    protected $fields;

    public function __construct($obraId, $model, $fields)
    {
        $this->obraId = $obraId;
        $this->model = $model;
        $this->fields = $fields;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $data = [];
            foreach ($this->fields as $key => $columnIndex) {
                $data[$key] = $row[$columnIndex];
            }

            $data['obra_id'] = $this->obraId;

            // Crear un nuevo concepto en la base de datos
            $this->model::create($data);
        }
    }
}
