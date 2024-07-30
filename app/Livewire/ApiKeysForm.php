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
        Log::info('Opening API Key form for creation.');

        $this->model = new ApiKey();
    }

    #[On('onEditApiKey')]
    public function edit($id): void
    {
        Log::info('Opening API Key form for editing.');

        $this->model = ApiKey::find($id);

        $this->llm_type = $this->model->llm_type;
        $this->base_url = $this->model->base_url;
        $this->api_key = $this->model->api_key;
        $this->name = $this->model->name;
    }

    public function save(): void
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

        if ($isCreate) {
            session()->flash('message', 'API key created successfully!');
        } else {
            session()->flash('message', 'API key saved successfully!');
        }

        $this->resetForm();
    }

    protected function resetForm()
    {
        $this->reset();
        $this->model = new ApiKey();
    }

    public function render()
    {
        $apiKeys = ApiKey::all()->sortBy('name');

        return view('livewire.api-keys-form', compact('apiKeys'));
    }
}
