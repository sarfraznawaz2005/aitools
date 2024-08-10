//import './bootstrap';
import {Livewire, Alpine} from '../../vendor/livewire/livewire/dist/livewire.esm';
import Clipboard from '@ryangjchandler/alpine-clipboard'
import Tooltip from "@ryangjchandler/alpine-tooltip";
import '../css/app.css';
import 'preline';
import {Notyf} from 'notyf';
import 'notyf/notyf.min.css'; // for React, Vue and Svelte
import {marked} from '../../node_modules/marked/lib/marked.esm.js';

Alpine.plugin(Tooltip);
Alpine.plugin(Clipboard)

window.notyf = new Notyf();
window.marked = marked;

// This re-initializes the preline components. Need to do this only when using livewire's wire:navigate on links.
document.addEventListener('livewire:navigated', () => {
    delete window.notyf;
    window.notyf = new Notyf();

    window.HSStaticMethods.autoInit();
})

Livewire.start()
