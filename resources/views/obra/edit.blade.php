<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Obra') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <h4 class="font-semibold text-lg">Editar obra o proyecto</h4>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <form action="{{ route('obra.update', $obra->id) }}" method="POST" class="mt-4">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="clave" class="block text-sm font-medium text-gray-700">Clave de la Obra</label>
                        <input type="text" id="clave" name="clave" value="{{ $obra->clave }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                    </div>

                    <div class="mb-4">
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre de la Obra</label>
                        <input type="text" id="nombre" name="nombre" value="{{ $obra->nombre }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                    </div>

                    <div class="mb-4">
                        <label for="contrato" class="block text-sm font-medium text-gray-700">No. de contrato</label>
                        <input type="text" id="contrato" name="contrato" value="{{ $obra->contrato }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                    </div>

                    <div class="mb-4">
                        <label for="ubicacion" class="block text-sm font-medium text-gray-700">Ubicación</label>
                        <input type="text" id="ubicacion" name="ubicacion" value="{{ $obra->ubicacion }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                    </div>

                    <div class="mb-4">
                        <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="4" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>{{ $obra->descripcion }}</textarea>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button type="submit">Guardar cambios</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
