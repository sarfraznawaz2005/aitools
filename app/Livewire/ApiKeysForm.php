<?php

namespace App\Livewire;

use App\Models\ApiKey;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
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

    protected function rules(): array
    {
        return [
            'llm_type' => 'required',
            'base_url' => 'required_if:llm_type,ollama',
            'api_key' => 'required|unique:api_keys,api_key,' . ($this->model->id ?? 'NULL') . ',id',
            'name' => 'required|unique:api_keys,name,' . ($this->model->id ?? 'NULL') . ',id',
        ];
    }

    #[On('onCreateApiKey')]
    public function create(): void
    {
        Log::info('Opening API Key form for creation.');

        $this->model = new ApiKey();
    }

    #[On('onMarkDefaultApiKey')]
    public function markDefault($id): void
    {
        $this->model = ApiKey::find($id);

        ApiKey::whereActive()->update(['active' => false]);

        $this->model->active = true;

        session()->flash('message', 'API key made default successfully!');

        $this->resetForm();
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

    #[On('onDeleteApiKey')]
    public function deleteApiKey($id): void
    {
        ApiKey::find($id)->delete();

        session()->flash('message', 'API key deleted successfully!');

        $this->resetForm();
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

    protected function resetForm(): void
    {
        $this->reset();
        $this->model = new ApiKey();
    }

    public function render(): Application|View|Factory
    {
        $apiKeys = ApiKey::all()->sortBy('name');

        return view('livewire.api-keys-form', compact('apiKeys'));
    }
}
