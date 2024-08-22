<div>
    <select wire:model.change="selectedModel"
            autocomplete="off"
            class="{{$classes ?? ''}} py-3 px-4 pe-9 block w-full font-bold text-center bg-white rounded-lg text-sm outline-0 focus:outline-none focus:ring-0 focus-visible:ring-0 focus:border-gray-400 sm:text-sm md:xs lg:xs">
        @foreach($this->apiKeys->groupBy('llm_type') as $llmType => $groupedApiKeys)
            <optgroup label="{{ $llmType }}">
                @foreach($groupedApiKeys as $apiKey)
                    <option
                        value="{{ $apiKey->model_name }}" {{$selectedModel === $apiKey->model_name ? 'selected' : ''}}>{{ $apiKey->model_name }}
                    </option>
                @endforeach
            </optgroup>
        @endforeach
    </select>
</div>
