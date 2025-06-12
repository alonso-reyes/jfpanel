<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Selecciona una Obra') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center">
                    <h4 class="font-semibold text-lg">Obras o proyectos</h4>
                    <!-- Botón para ir a la página de creación de obra -->
                    <!-- <a href="{{ route('obra.create') }}" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 font-semibold text-sm leading-5 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 hover:bg-blue-500">
                        Agregar nueva obra
                    </a> -->


                    <form action="{{ route('obra.create') }}" method="GET">
                        <x-primary-button type="submit">Crear nueva obra</x-primary-button>
                    </form>
                </div>
            </div>


            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                 <!-- Lista de obras -->
                 <table class="table-auto w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 border-b border-gray-300">Clave</th>
                            <th class="px-4 py-2 border-b border-gray-300">Nombre</th>
                            <th class="px-4 py-2 border-b border-gray-300">No. contrato</th>
                            <th class="px-4 py-2 border-b border-gray-300">Ubicación</th>
                            <th class="px-4 py-2 border-b border-gray-300">Descripción</th>
                            <th class="px-4 py-2 border-b border-gray-300 text-right"></th>
                            <th class="px-4 py-2 border-b border-gray-300 text-right"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($obras as $obra)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border-b border-gray-300">{{ $obra->clave }}</td>
                            <td class="px-4 py-2 border-b border-gray-300">{{ $obra->nombre }}</td>
                            <td class="px-4 py-2 border-b border-gray-300">{{ $obra->contrato }}</td>
                            <td class="px-4 py-2 border-b border-gray-300">{{ $obra->ubicacion }}</td>
                            <td class="px-4 py-2 border-b border-gray-300">{{ $obra->descripcion }}</td>
                            <td class="px-4 py-2 border-b border-gray-300 text-right">
                                <a href="{{ route('obra.edit', $obra->id) }}" class="text-gray-700 hover:text-black">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-2.036L9 13l-1.5 4.5 4.5-1.5 6.536-6.536a2.5 2.5 0 10-3.536-3.536z" />
                                    </svg>
                                </a>
                            </td>
                            <td class="px-4 py-2 border-b border-gray-300 text-right">
                                <form action="{{ route('obra.set') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="obra_id" value="{{ $obra->id }}">
                                    <button type="submit" class="text-gray-700 hover:text-black">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
