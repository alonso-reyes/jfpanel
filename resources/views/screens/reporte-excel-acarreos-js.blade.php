<script>
    document.addEventListener('turbo:load', function() {
        console.log('entro aquiii');
        const dateRange = document.querySelector('#date-range input[name="rango_fechas"]');
        const exportLink = document.querySelector('#export-link');

        if (dateRange && exportLink) {
            exportLink.addEventListener('click', function(e) {
                e.preventDefault(); // evitamos que vaya al link sin parámetros

                // La librería de Orchid guarda el valor como "YYYY-MM-DD - YYYY-MM-DD"
                const value = dateRange.value.trim();
                if (!value) {
                    window.location.href = exportLink.href; // sin fechas
                    return;
                }

                const dates = value.split(' - ');
                const start = dates[0];
                const end = dates[1];

                // Construimos la URL con parámetros GET
                const url = new URL(exportLink.href, window.location.origin);
                url.searchParams.set('start', start);
                url.searchParams.set('end', end);

                window.location.href = url; // redirige a la ruta con fechas
            });
        }
    });
</script>