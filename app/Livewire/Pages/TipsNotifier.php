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
    public string $schedule_type = '';
    public string $cronExpression = '';

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
        if ($this->schedule_type !== 'custom') {
            return match ($this->schedule_type) {
                'every_minute' => 'Every Minute',
                'every_hour' => 'Every Hour',
                'every_day' => 'Every Day',
                'every_week' => 'Every Week',
                'every_month' => 'Every Month',
            };
        }

        return $this->getCustomSchedulePreview();
    }

    private function getCustomSchedulePreview(): string
    {
        try {
            $humanReadable = CronTranslator::translate(trim($this->cronExpression));
            return ucfirst($humanReadable);
        } catch (Exception) {
            return 'Invalid cron expression';
        }
    }

    /**
     * @throws Exception
     */
    #[Computed]
    public function nextRuns(): array
    {
        try {
            $nextRuns = [];
            $date = now();
            $cronExpression = new CronExpression(trim($this->getCronExpression()));

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

    private function getCronExpression(): string
    {
        return $this->schedule_type === 'custom' ? trim($this->cronExpression) : match ($this->schedule_type) {
            'every_minute' => '* * * * *',
            'every_hour' => '0 * * * *',
            'every_day' => '0 0 * * *',
            'every_week' => '0 0 * * 0',
            'every_month' => '0 0 1 * *',
            default => '',
        };
    }

    #[Title('Tips Notifier')]
    public function render(): View|Factory|Application
    {
        return view('livewire.pages.tips-notifier');
    }

    public function save(): void
    {
        $this->validate([
            'api_key_id' => 'required',
            'name' => 'required|min:5|max:25|unique:tips,name,' . ($this->model->id ?? 'NULL') . ',id',
            'prompt' => 'required|min:100',
            'schedule_type' => 'required',
            'cronExpression' => [
                'required_if:scheduleType,custom',
                'valid_cron'
            ],
        ], [
            'schedule_type.required' => 'The Frequency field is required.',
            'api_key_id.required' => 'The LLM field is required.',
            'cronExpression.valid_cron' => 'The cron expression is invalid.',
        ]);

        if ($this->model->exists) {
            $this->model->update([
                'api_key_id' => $this->api_key_id,
                'prompt' => trim($this->prompt),
                'schedule_type' => $this->schedule_type,
                'schedule_data' => $this->schedule_type === 'custom' ? ['cron' => trim($this->cronExpression)] : null,
            ]);
        } else {
            Tip::query()->create([
                'api_key_id' => $this->api_key_id,
                'prompt' => trim($this->prompt),
                'schedule_type' => $this->schedule_type,
                'schedule_data' => $this->schedule_type === 'custom' ? ['cron' => trim($this->cronExpression)] : null,
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
        $this->cronExpression = $tip->schedule_data['cron'] ?? null;

        $this->dispatch('showModal', ['id' => 'tipModal']);
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
