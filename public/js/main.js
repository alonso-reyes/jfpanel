

//document.addEventListener("DOMContentLoaded", () => {
document.addEventListener("turbo:load", () => {
    return;
    const estadoSelect = document.getElementById('estado-select');
    const inactividadSelect = document.getElementById('inactividad-select');
    //const inactividadSelect = document.getElementById('field-maquinariainactividad-7ab9371e86b62d25977a24b48735e12bd973b4c8');
    console.log('Valor inicial de estadoSelect:', estadoSelect.value);
    //console.log(estadoSelect);
    // Verifica que los elementos existan
    if (estadoSelect && inactividadSelect) {
        // Función para habilitar/deshabilitar el select de inactividad
        function toggleInactividadSelect() {
            console.log('Estado actual:', estadoSelect.value);
        
            if (estadoSelect.value === 'inactivo') {
                inactividadSelect.style.display = 'none'; // Ocultamos primero
                inactividadSelect.offsetHeight; // Forzamos un reflujo
                inactividadSelect.style.display = 'block'; // Luego lo mostramos
                inactividadSelect.required = true;
            } else {
                inactividadSelect.style.display = 'none';
                inactividadSelect.required = false;
                inactividadSelect.value = '';
            }
            

            //Turbo.visit(window.location.href);  // Recarga la página (o el frame actual)
        }

        // Ejecutar al cargar la página
        //toggleInactividadSelect();

        // Escuchar cambios en el estado
        estadoSelect.addEventListener('change', toggleInactividadSelect);
    } else {
        console.log('No se encontraron los elementos estado-select o inactividad-select.');
    }
});

