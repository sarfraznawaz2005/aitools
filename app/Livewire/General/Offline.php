<?php

namespace App\Livewire\General;

use Livewire\Component;

class Offline extends Component
{
    /** @noinspection ALL */
    public function render(): string
    {
        $id = uniqid('', true);

        return <<<blade
            <div class="w-full my-4 mt-20 flex flex-row z-[1000] justify-center items-center mx-auto" wire:offline wire:key="offline-$id">
                <div class="p-3 text-sm text-white break-words flex items-center rounded-lg bg-yellow-500">
                    <div class="flex items-center justify-center text-center">
                        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="info-circle" class="w-4 h-4 mr-2 fill-current" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path fill="currentColor" d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 110c23.196 0 42 18.804 42 42s-18.804 42-42 42-42-18.804-42-42 18.804-42 42-42zm56 254c0 6.627-5.373 12-12 12h-88c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h12v-64h-12c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h64c6.627 0 12 5.373 12 12v100h12c6.627 0 12 5.373 12 12v24z"></path>
                        </svg>
                        <p class="font-bold text-sm text-white break-words">
                           It seems you are offline.
                        </p>
                    </div>
                </div>
            </div>
        blade;
    }
}
