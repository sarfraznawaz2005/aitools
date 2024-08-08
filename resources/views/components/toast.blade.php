<script>
    function handleToastMessage(event) {
        const style = event.detail[0]?.style || event.detail.style || 'info';
        const message = event.detail[0]?.message || event.detail.message || '';

        const notificationType = (style === 'success' || style === 'error' || style === 'warning') ? style : 'info';

        if (message) {
            window.notyf.open({
                type: notificationType,
                message: message
            });
        }
    }

    window.addEventListener('toast-message', (event) => handleToastMessage(event));

    Livewire.hook('message.processed', () => {
        window.addEventListener('toast-message', (event) => handleToastMessage(event));
    });

    window.addEventListener('livewire:navigated', () => {
        window.addEventListener('toast-message', (event) => handleToastMessage(event));
    });
</script>
