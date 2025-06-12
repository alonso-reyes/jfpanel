<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Obra;
use Illuminate\Http\Request;

class ObraController extends Controller
{
    //
    public function select()
     {
         // Obtener todas las obras disponibles
         $obras = Obra::all();

         // Mostrar la vista de selección de obra
         return view('obra.select', compact('obras'));
    }


    public function create()
     {
         // Mostrar la vista de selección de obra
         return view('obra.create');
    }

    public function store(Request $request)
     {
         // Validar los datos enviados por el formulario
        $request->validate([
            'clave' => 'required|string',
            'nombre' => 'required|string|max:255',
            'contrato' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'ubicacion' => 'required|string',
        ]);

        // Crear la nueva obra
        $obra = Obra::create([
            'clave' => $request->input('clave'),
            'nombre' => $request->input('nombre'),
            'contrato' => $request->input('contrato'),
            'descripcion' => $request->input('descripcion'),
            'ubicacion' => $request->input('ubicacion'),
        ]);

        // Establecer la obra creada como seleccionada en la sesión
        session(['obra_id' => $obra->id]);
        // Redirigir al usuario a la pantalla principal de Orchid o a otra vista
        return redirect()->route('platform.concepto.list')->with('success', 'Obra creada y seleccionada con éxito.');
     }


     public function setObra(Request $request)
    {
        // Validar que el ID de la obra exista en la base de datos
        $request->validate([
            'obra_id' => 'required|exists:obras,id',
        ]);
        // Guardar el ID de la obra en la sesión
        session(['obra_id' => $request->obra_id]);
        // Redirigir al listado de conceptos u otra pantalla
        return redirect()->route('platform.concepto.list')->with('success', 'Obra seleccionada con éxito.');
    }

    public function edit($id)
    {
        $obra = Obra::findOrFail($id);
        return view('obra.edit', compact('obra'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'clave' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'contrato' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
            'descripcion' => 'required|string',
        ]);

        $obra = Obra::findOrFail($id);
        $obra->update($request->all());

        return redirect()->route('obra.select')->with('success', 'Obra actualizada exitosamente.');
    }


}
