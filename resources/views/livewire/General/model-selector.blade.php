<div wire:init="loadModels" x-data="{
    open: false,
    position: 'bottom',
    updatePosition() {
        if (!this.$refs.dropdown || !this.$refs.button) return;

        const dropdown = this.$refs.dropdown;
        const button = this.$refs.button;
        const rect = button.getBoundingClientRect();
        const dropdownRect = dropdown.getBoundingClientRect();

        const spaceAbove = rect.top;
        const spaceBelow = window.innerHeight - rect.bottom;
        const spaceLeft = rect.left;
        const spaceRight = window.innerWidth - rect.right;

        if (spaceBelow < dropdownRect.height && spaceAbove > spaceBelow) {
            this.position = 'top';
        } else {
            this.position = 'bottom';
        }

        if (spaceRight < dropdownRect.width && spaceLeft > spaceRight) {
            this.position += '-right';
        } else {
            this.position += '-left';
        }

        dropdown.style.top = this.position.includes('bottom') ? `${rect.height}px` : 'auto';
        dropdown.style.bottom = this.position.includes('top') ? `${rect.height}px` : 'auto';
        dropdown.style.left = this.position.includes('left') ? '0' : 'auto';
        dropdown.style.right = this.position.includes('right') ? '0' : 'auto';
    }
}"
     x-init="
        $nextTick(() => {
            updatePosition();
            window.addEventListener('resize', () => { if (open) updatePosition(); });
            window.addEventListener('scroll', () => { if (open) updatePosition(); }, true);
        })
    "
     class="relative inline-block text-left"
>
    <button
        @click="open = !open; $nextTick(() => updatePosition())"
        type="button"
        x-ref="button"
        x-tooltip.raw="{{$selectedModel}}"
        class="inline-flex items-center text-sm font-medium text-gray-800 focus:outline-none mt-1"
    >
        <x-icons.dotsv/>
    </button>

    <template x-if="open">
        <div class="fixed inset-0 bg-black/20 backdrop-blur-sm z-40" @click="open = false"></div>
    </template>

    <div
        x-cloak
        x-show="open"
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        x-ref="dropdown"
        class="absolute z-50 bg-white shadow-2xl space-y-0.5 divide-y divide-gray-200 rounded-lg border"
        style="min-width: max-content;"
        role="menu"
    >
        <div class="py-2 first:pt-0 last:pb-0">
            @if($loaded)
                @foreach($this->apiKeys->groupBy('llm_type') as $llmType => $groupedApiKeys)
                    <ul class="m-3">
                        <li class="font-bold text-sm text-gray-500 whitespace-nowrap">
                            {{ $llmType }}
                        </li>

                        @foreach($groupedApiKeys as $apiKey)
                            <li
                                wire:click="setModel('{{ $apiKey->model_name }}'); open = false;"
                                class="ml-4 text-sm cursor-pointer py-1.5 text-gray-500
                            {{$apiKey->model_name === $selectedModel ? 'font-bold pointer-events-none' : ''}}
                            hover:text-blue-600 p-1 list-disc list-inside whitespace-nowrap"
                            >
                                {{ $apiKey->model_name }}
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            @endif
        </div>
    </div>
</div>
