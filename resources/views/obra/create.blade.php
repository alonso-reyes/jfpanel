<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Agregar Obra') }}
        </h2>
    </x-slot>

    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                <h4 class="font-semibold text-lg">Registrar obra o proyecto</h4>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                       <!-- Formulario para registrar obra -->
                <form action="{{ route('obra.store') }}" method="POST" class="mt-4">
                    @csrf
                    <div class="mb-4">
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Clave de la Obra</label>
                        <input type="text" id="clave" name="clave" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="" required>
                    </div>

                    <div class="mb-4">
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre de la Obra</label>
                        <input type="text" id="nombre" name="nombre" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="" required>
                    </div>

                    <div class="mb-4">
                        <label for="contrato" class="block text-sm font-medium text-gray-700">No. de contrato</label>
                        <input type="text" id="contrato" name="contrato" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="" required>
                    </div>

                    <div class="mb-4">
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Ubicación</label>
                        <input type="text" id="ubicacion" name="ubicacion" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="" required>
                    </div>

                    <div class="mb-4">
                        <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="4" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="" required></textarea>
                    </div>

                    <!-- <div class="mb-4">
                        <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                    </div>

                    <div class="mb-4">
                        <label for="fecha_fin" class="block text-sm font-medium text-gray-700">Fecha de Fin (Opcional)</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div> -->

                    <!-- <div class="mb-4">
                        <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                        <select id="estado" name="estado" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="En Proceso">En Proceso</option>
                            <option value="Completado">Completado</option>
                            <option value="Pendiente">Pendiente</option>
                        </select>
                    </div> -->

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold text-sm leading-5 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 hover:bg-blue-500">
                            Guardar Obra
                        </button>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button type="submit">Guardar</x-primary-button>
                    </div>
                </form>
                </div>
            </div>

        </div>
    </div>

    
</x-app-layout>
