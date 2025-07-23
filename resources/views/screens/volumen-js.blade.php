<script>
    document.addEventListener('turbo:load', function() {
        //const conceptoSelect = document.querySelector('[name="acarreovolumen[concepto_id]"]');
        const conceptoSelect = document.getElementById('concepto-select');
        const inputVolumenSuelto = document.getElementById('volumen-suelto');
        const inputFactorAbundamiento = document.getElementById('factor-abundamiento');
        const inputVolumenCompactado = document.getElementById('volumen-compactado');

        async function actualizarCompactado() {
            const conceptoId = conceptoSelect.value;
            const volumen = parseFloat(inputVolumenSuelto.value);

            //console.log(conceptoId);
            if (conceptoId && !isNaN(volumen)) {
                try {
                    const response = await fetch(`/api/getFactorAbundamiento/${conceptoId}`);
                    const data = await response.json();

                    if (data) {
                        const factor = parseFloat(data.factor_abundamiento);
                        inputFactorAbundamiento.value = factor;

                        const volumenCompactado = volumen / factor;
                        inputVolumenCompactado.value = volumenCompactado;
                    }
                } catch (e) {
                    console.error('Error al obtener factor:', e);
                }
            }

        }

        if (conceptoSelect) {
            conceptoSelect.addEventListener('change', actualizarCompactado);
        }
        if (inputVolumenSuelto) {
            inputVolumenSuelto.addEventListener('input', actualizarCompactado);
        }
    });
</script>