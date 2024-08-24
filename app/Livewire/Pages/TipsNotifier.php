<?php

namespace App\Livewire\Pages;

use App\Models\ApiKey;
use App\Models\Tip;
use App\Traits\InteractsWithToast;
use Cron\CronExpression;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Lorisleiva\CronTranslator\CronTranslator;

class TipsNotifier extends Component
{
    use InteractsWithToast;

    protected $listeners = ['apiKeysUpdated' => '$refresh'];

    public Tip $model;

    public string $api_key_id = '';
    public string $name = '';
    public string $prompt = '';
    public string $cron = '';

    private CronExpression $CronExp;

    #[Computed]
    public function tips(): Collection
    {
        return Tip::query()->latest()->get();
    }

    #[Computed]
    public function apiKeys()
    {
        return ApiKey::all()->sortBy('model_name');
    }

    #[Computed]
    public function schedulePreview(): string
    {
        try {
            $humanReadable = CronTranslator::translate(trim($this->cron));
            return ucfirst($humanReadable);
        } catch (Exception) {
            return 'Invalid cron expression';
        }
    }

    #[Computed]
    public function nextRuns(): array
    {
        try {
            $nextRuns = [];
            $date = now();
            $cronExpression = $this->CronExp->setExpression(trim($this->cron));

            for ($i = 0; $i < 3; $i++) {
                $nextRun = $cronExpression->getNextRunDate($date);
                $nextRuns[] = $nextRun->format('Y-m-d H:i:s');
                $date = $nextRun;
            }

            return $nextRuns;
        } catch (Exception) {
            return [];
        }
    }

    #[Title('Tips Notifier')]
    public function render(): View|Factory|Application
    {
        $this->CronExp = new CronExpression('* * * * *');

        return view('livewire.pages.tips-notifier');
    }

    public function save(): void
    {
        $this->validate([
            'api_key_id' => 'required',
            'name' => 'required|min:5|max:25|unique:tips,name,' . ($this->model->id ?? 'NULL') . ',id',
            'prompt' => 'required|min:100',
            'cron' => 'required|valid_cron',
        ], [
            'api_key_id.required' => 'The LLM field is required.',
            'cron.required' => 'The cron expression field is required.',
            'cron.valid_cron' => 'The cron expression is invalid.',
        ]);

        if ($this->model->exists) {
            $this->model->update([
                'api_key_id' => $this->api_key_id,
                'name' => $this->name,
                'prompt' => trim($this->prompt),
                'cron' => $this->cron,
            ]);
        } else {
            Tip::query()->create([
                'api_key_id' => $this->api_key_id,
                'name' => $this->name,
                'prompt' => trim($this->prompt),
                'cron' => $this->cron,
            ]);
        }

        $this->dispatch('closeModal', ['id' => 'tipModal']);

        $this->success($this->model->exists ? 'Tip saved successfully!' : 'Tip added successfully!');

        $this->resetForm();
    }

    public function edit(Tip $tip): void
    {
        $this->model = $tip;

        $this->resetErrorBag();

        $this->fill($tip->toArray());

        $this->dispatch('showModal', ['id' => 'tipModal']);
    }

    public function toggleStatus(Tip $tip): void
    {
        $tip->update(['active' => !$tip->active]);
    }

    public function deleteTip($id): void
    {
        Tip::destroy($id);

        $this->success('Tip deleted successfully!');
    }

    public function resetForm(): void
    {
        $this->reset();

        $this->resetErrorBag();

        $this->model = new Tip();
    }
}
