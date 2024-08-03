<?php

/*
 * Sticky header
 *
 * */

namespace App\Livewire;

use App\Models\ApiKey;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
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
            'base_url' => 'required_if:llm_type,Ollama',
            'api_key' => 'required|min:3|unique:api_keys,api_key,' . ($this->model->id ?? 'NULL') . ',id',
            'name' => 'required|min:3|unique:api_keys,name,' . ($this->model->id ?? 'NULL') . ',id',
        ];
    }

    public function mount(ApiKey $apiKey = null): void
    {
        $this->model = $apiKey ?? new ApiKey();

        $this->fill($this->model->toArray());
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

    public function save()
    {
        $this->validate();

        $isFirstApiKey = ApiKey::count() === 0;

        $this->model->fill([
            'llm_type' => $this->llm_type,
            'base_url' => $this->base_url,
            'api_key' => $this->api_key,
            'name' => $this->name,
        ])->save();

        session()->flash('message', $this->model->wasRecentlyCreated ? 'API key created successfully!' : 'API key saved successfully!');

        if ($isFirstApiKey) {
            return $this->redirect(route('home'), true);
        }

        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->reset();
        $this->resetErrorBag();
        $this->resetValidation();

        $this->model = new ApiKey();
    }

    public function render(): View|Application|Factory
    {
        return view('livewire.api-keys-form', [
            'apiKeys' => ApiKey::all()->sortBy('name'),
        ]);
    }
}
