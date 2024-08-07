<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js" async></script>
<script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('singlePull', () => {
            document.querySelectorAll('button').forEach(button => {
                button.disabled = true;
            });
        });
        Livewire.on('tenPulls', () => {
            document.querySelectorAll('button').forEach(button => {
                button.disabled = true;
            });
        });
    });
</script>
