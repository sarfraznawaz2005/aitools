<?php

namespace App\Livewire\ApiKeys;

use App\Models\ApiKey;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ApiKeysForm extends Component
{
    public ApiKey $model;

    public string $llm_type;
    public ?string $base_url;
    public string $api_key;
    public string $model_name;

    protected function rules(): array
    {
        return [
            'llm_type' => 'required|regex:/^\S*$/u',
            'base_url' => 'required_if:llm_type,Ollama',
            'api_key' => 'required|min:3|regex:/^\S*$/u',
            'model_name' => 'required|min:3|unique:api_keys,model_name,' . ($this->model->id ?? 'NULL') . ',id|regex:/^\S*$/u',
        ];
    }

    #[Computed]
    public function apiKeys()
    {
        return ApiKey::all()->sortBy('model_name');
    }

    public function mount(ApiKey $apiKey = null): void
    {
        $this->model = $apiKey ?? new ApiKey();

        $this->fill($this->model->toArray());
    }

    public function markDefault(ApiKey $apiKey): void
    {
        ApiKey::whereActive()->update(['active' => false]);

        $apiKey->update(['active' => true]);

        $this->resetForm();
    }

    public function edit(ApiKey $apiKey): void
    {
        $this->resetErrorBag();

        $this->model = $apiKey;

        $this->fill($apiKey->toArray());
    }

    public function save()
    {
        $this->validate();

        $isFirstApiKey = ApiKey::count() === 0;

        $this->model->fill([
            'llm_type' => $this->llm_type,
            'base_url' => $this->base_url ?? '',
            'api_key' => $this->api_key,
            'model_name' => $this->model_name,
        ])->save();

        session()->flash('message', $this->model->wasRecentlyCreated ? 'API key created successfully!' : 'API key saved successfully!');

        if ($isFirstApiKey) {
            return $this->redirect(route('home'), true);
        }

        $this->resetForm();
    }

    public function deleteApiKey(ApiKey $apiKey): void
    {
        if ($apiKey->active) {
            $this->resetForm();
            $this->addError('error', 'Cannot delete the default API key!');
            return;
        }

        $apiKey->delete();

        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset();
        $this->resetErrorBag();

        $this->model = new ApiKey();

        $this->dispatch('apiKeysUpdated');
    }
}
