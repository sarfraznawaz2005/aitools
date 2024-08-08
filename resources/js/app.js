//import './bootstrap';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import Clipboard from '@ryangjchandler/alpine-clipboard'
import Tooltip from "@ryangjchandler/alpine-tooltip";
import '../css/app.css';
import 'preline';

Alpine.plugin(Tooltip);
Alpine.plugin(Clipboard)

// This re-initializes the preline components. Need to do this only when using livewire's wire:navigate on links.
document.addEventListener('livewire:navigated', () => {
    window.HSStaticMethods.autoInit();
})

Livewire.start()
