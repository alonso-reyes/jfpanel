<script>
    document.addEventListener('turbo:load', function() {
        const checkbox = document.getElementById('estado-inactivo-checkbox');
        const motivoWrapper = document.getElementById('motivo-select-wrapper')?.closest('.form-group');

        function toggleMotivoVisibility() {
            if (checkbox.checked) {
                motivoWrapper.style.display = 'block';
            } else {
                motivoWrapper.style.display = 'none';
            }
        }

        if (checkbox && motivoWrapper) {
            checkbox.addEventListener('change', toggleMotivoVisibility);
            toggleMotivoVisibility(); // para el estado inicial
        }
    });
</script>