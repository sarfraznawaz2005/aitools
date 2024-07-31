<?php

namespace App\Livewire;

use App\Models\ApiKey;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;

class ApiKeysForm extends Component
{
    public ApiKey $model;
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

    public function mount(ApiKey $apiKey = null): void
    {
        $this->model = $apiKey ?? new ApiKey();
        $this->fill($this->model->toArray());
    }

    public function create(): void
    {
        $this->resetForm();
        $this->model = new ApiKey();
    }

    public function markDefault(ApiKey $apiKey): void
    {
        ApiKey::where('active', true)->update(['active' => false]);
        $apiKey->update(['active' => true]);
        $this->resetForm();
    }

    public function edit(ApiKey $apiKey): void
    {
        $this->model = $apiKey;
        $this->fill($apiKey->toArray());
    }

    #[On('onDeleteApiKey')]
    public function deleteApiKey($id): void
    {
        $apiKey = ApiKey::find($id);

        if ($apiKey->isActive()) {
            $this->addError('error', 'Cannot delete the default API key!');
            return;
        }

        $apiKey->delete();

        $this->resetForm();
    }

    public function save(): void
    {
        $this->validate();

        $this->model->fill([
            'llm_type' => $this->llm_type,
            'base_url' => $this->base_url,
            'api_key' => $this->api_key,
            'name' => $this->name,
        ])->save();

        session()->flash('message', $this->model->wasRecentlyCreated ? 'API key created successfully!' : 'API key saved successfully!');

        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->reset();
        $this->model = new ApiKey();
    }

    public function render(): View|Application|Factory
    {
        return view('livewire.api-keys-form', [
            'apiKeys' => ApiKey::all()->sortBy('name'),
        ]);
    }
}
