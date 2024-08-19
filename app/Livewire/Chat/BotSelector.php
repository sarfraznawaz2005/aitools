<?php

namespace App\Livewire\Chat;

use App\Enums\BotTypeEnum;
use App\Models\Bot;
use App\Traits\InteractsWithToast;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class BotSelector extends Component
{
    use WithFileUploads;
    use InteractsWithToast;

    public Bot $model;

    public string $name;
    public string $bio;
    public string $prompt;
    public string $icon;
    public string $type;

    #[Validate(['files.*' => 'mimes:txt,pdf|max:10240'])]
    public array $files = [];

    public array $botFiles = [];

    public int $newBotId = 0;

    public bool $isNodeInstalled = true;

    protected $listeners = ['refreshBot' => '$refresh'];

    protected function rules(): array
    {
        return [
            'name' => 'required|min:5|max:25|unique:bots,name,' . ($this->model->id ?? 'NULL') . ',id',
            'bio' => 'required|min:5|max:500',
            'prompt' => 'required|min:3|max:2500',
            'icon' => 'required',
        ];
    }

    protected function messages(): array
    {
        return [
            'bio.required' => 'The description field is required.',
            'bio.min' => 'The description must be at least 5 characters long.',
            'bio.max' => 'The description must not exceed 500 characters.',
        ];
    }

    public function mount(Bot $bot = null): void
    {
        $this->model = $bot ?? new Bot();

        $this->files = [];

        $this->fill($this->model->toArray());

        $this->isNodeInstalled = preg_match('/v\d+(\.\d+)*\b/', shell_exec('node -v')) === 1;
    }

    public function selectBot(Bot $bot): void
    {
        $this->dispatch('botSelected', $bot->id);
    }

    public function save(): void
    {
        $this->validate();

        $this->model->fill([
            'name' => $this->name,
            'bio' => $this->bio,
            'prompt' => $this->prompt,
            'icon' => $this->icon,
            'type' => $this->type ?? BotTypeEnum::TEXT,
        ])->save();

        //dd($this->model);

        foreach ($this->files as $file) {
            $file->storeAs(path: 'files/' . strtolower(Str::slug($this->model->name)), name: $file->getClientOriginalName());
        }

        $this->dispatch('closeModal', ['id' => 'botModal']);

        $this->success($this->model->wasRecentlyCreated ? 'Bot created successfully!' : 'Bot saved successfully!');

        if ($this->model->wasRecentlyCreated) {
            $this->newBotId = $this->model->id;

            $this->resetForm();
        }

        $this->dispatch('indexFiles', $this->model->id);
    }

    public function edit(Bot $bot): void
    {
        $this->newBotId = 0;

        $this->botFiles = array_map(fn($file) => basename($file), glob(base_path('storage/app/files/') . strtolower(Str::slug($bot->name)) . '/*'));

        $this->dispatch('showModal', ['id' => 'botModal']);

        $this->resetErrorBag();

        $this->model = $bot;
        $this->fill($bot->toArray());
    }

    public function delete(Bot $bot): void
    {
        $this->newBotId = 0;

        $bot->delete();

        $this->dispatch('closeModal', ['id' => 'botModal']);

        // clean bot files
        File::cleanDirectory(base_path('storage/app/files/') . strtolower(Str::slug($bot->name)));
        File::deleteDirectory(base_path('storage/app/files/') . strtolower(Str::slug($bot->name)));
        // todo: delete indexing folder too if already created

        $this->success('Bot deleted successfully!');

        $this->resetForm();
    }

    public function deleteFile(string $fileName): void
    {
        $path = base_path('storage/app/files/') . strtolower(Str::slug($this->model->name)) . '/' . $fileName;

        @unlink($path);

        $this->botFiles = array_map(fn($file) => basename($file), glob(base_path('storage/app/files/') . strtolower(Str::slug($this->model->name)) . '/*'));

        $this->success('File deleted successfully!');

        $this->dispatch('indexFiles', $this->model->id);
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'bio', 'prompt', 'icon', 'type', 'files']);

        $this->resetErrorBag();

        $this->model = new Bot();
    }

    public function render(): View|Factory|Application
    {
        return view('livewire.chat.bot-selector', [
            'bots' => Bot::query()->orderBy('name')->get(),
        ]);
    }
}
