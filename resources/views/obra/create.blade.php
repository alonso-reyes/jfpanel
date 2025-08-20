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

                        <div class="mb-4">
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        </div>

                        <div class="mb-4">
                            <label for="fecha_termino" class="block text-sm font-medium text-gray-700">Fecha de Fin</label>
                            <input type="date" id="fecha_termino" name="fecha_termino" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                        <div class="mb-4">
                            <label for="monto_contrato" class="block text-sm font-medium text-gray-700">Monto del Contrato</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="text" id="monto_contrato" name="monto_contrato"

                                    class="pl-7 mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>

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
    {{-- Script para el formato de moneda --}}
    <script>
        // Función global para que pueda ser llamada desde el HTML
        function formatCurrency(input) {
            // Guardar la posición del cursor y el valor original
            let cursorPosition = input.selectionStart;
            let originalValue = input.value;

            // Contar cuántos caracteres no numéricos hay antes del cursor
            let nonNumericBeforeCursor = 0;
            for (let i = 0; i < cursorPosition; i++) {
                if (originalValue[i] === ',' || originalValue[i] === '$' || originalValue[i] === ' ') {
                    nonNumericBeforeCursor++;
                }
            }

            // Remover todo excepto números y punto decimal
            let numericValue = originalValue.replace(/[^\d.]/g, '');

            // Remover puntos decimales extras
            let decimalCount = (numericValue.match(/\./g) || []).length;
            if (decimalCount > 1) {
                numericValue = numericValue.substring(0, numericValue.lastIndexOf('.'));
            }

            // Limitar a 2 decimales
            if (numericValue.includes('.')) {
                let parts = numericValue.split('.');
                if (parts[1].length > 2) {
                    parts[1] = parts[1].substring(0, 2);
                    numericValue = parts.join('.');
                }
            }

            // Formatear con separadores de miles solo si hay valor
            if (numericValue) {
                let number = parseFloat(numericValue);
                if (!isNaN(number)) {
                    // Formatear el número
                    input.value = number.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                    // Calcular la nueva posición del cursor
                    let numericCursorPosition = cursorPosition - nonNumericBeforeCursor;
                    let newCursorPosition = 0;
                    let numericCharsCount = 0;

                    // Recorrer el nuevo valor para encontrar la posición equivalente
                    for (let i = 0; i < input.value.length && numericCharsCount < numericCursorPosition; i++) {
                        if (input.value[i] !== ',' && input.value[i] !== '$' && input.value[i] !== ' ') {
                            numericCharsCount++;
                        }
                        newCursorPosition = i + 1;
                    }

                    // Establecer la posición del cursor
                    input.setSelectionRange(newCursorPosition, newCursorPosition);
                }
            } else {
                input.value = '';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const montoInput = document.getElementById('monto_contrato');

            if (montoInput) {
                // Formatear al perder el foco
                montoInput.addEventListener('blur', function() {
                    formatCurrency(this);
                });

                // Limpiar formato al obtener el foco
                montoInput.addEventListener('focus', function() {
                    if (this.value) {
                        let numericValue = this.value.replace(/[^\d.]/g, '');
                        this.value = numericValue;
                    }
                });

                // Formateo en tiempo real mientras se escribe
                montoInput.addEventListener('input', function(e) {
                    // Pequeño delay para permitir que el valor se actualice
                    setTimeout(() => {
                        formatCurrency(this);
                    }, 10);
                });

                // Formatear inicialmente si ya tiene valor
                if (montoInput.value) {
                    formatCurrency(montoInput);
                }
            }
        });
    </script>

</x-app-layout>