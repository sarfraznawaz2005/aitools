//import './bootstrap';
import '../css/app.css';
import 'preline';

// This re-initializes the preline components. Need to do this only when using livewire's wire:navigate on links.
document.addEventListener('livewire:navigated', () => {
    window.HSStaticMethods.autoInit();
})
