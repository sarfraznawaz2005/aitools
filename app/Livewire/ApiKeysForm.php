<?php

namespace App\Livewire;

use App\Models\ApiKey;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class ApiKeysForm extends Component
{
    public ?ApiKey $model;

    public $llm_type;
    public $base_url;
    public $api_key;
    public $name;

    protected array $rules = [
        'llm_type' => 'required',
        'base_url' => 'required_if:llm_type,ollama',
        'api_key' => 'required|unique:api_keys',
        'name' => 'required|unique:api_keys',
    ];

    #[On('onCreateApiKey')]
    public function create(): void
    {
        Log::info('Opening API Key form for creation');

        $this->model = new ApiKey();
    }

    public function edit(ApiKey $model): void
    {
        $this->model = $model;
    }

    public function save()
    {
        $this->validate();

        $isCreate = is_null($this->model->id);

        $this->model->fill([
            'llm_type' => $this->llm_type,
            'base_url' => $this->base_url,
            'api_key' => $this->api_key,
            'name' => $this->name
        ]);

        if (!$this->model->save()) {
            $this->addError('error', 'Unable to save API Key!');
            return;
        }

        session()->flash('message', 'API key saved successfully!');

        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.api-keys-form');
    }

    protected function resetForm()
    {
        $this->reset();
        $this->model = new ApiKey();
    }
}
