import '../css/app.css';
import {Livewire, Alpine} from '../../vendor/livewire/livewire/dist/livewire.esm';
import Clipboard from '@ryangjchandler/alpine-clipboard'
import Tooltip from "@ryangjchandler/alpine-tooltip";
import 'preline';
import {Notyf} from 'notyf';
//import './echo';

Alpine.plugin(Tooltip);
Alpine.plugin(Clipboard)

window.notyf = new Notyf();

// This re-initializes the preline components. Need to do this only when using livewire's wire:navigate on links.
document.addEventListener('livewire:navigated', () => {
    window.HSStaticMethods.autoInit();

    delete window.notyf;
    window.notyf = new Notyf();
})

Livewire.start()
