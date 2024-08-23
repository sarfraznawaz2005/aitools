<?php

namespace App\Livewire\Pages;

use App\Models\ApiKey;
use App\Models\Tip;
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
    protected $listeners = ['apiKeysUpdated' => '$refresh'];

    public string $apiKey = '';
    public string $prompt = '';
    public string $scheduleType = '';
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
        if ($this->scheduleType !== 'custom') {
            return match ($this->scheduleType) {
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
        return $this->scheduleType === 'custom' ? trim($this->cronExpression) : match ($this->scheduleType) {
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
            'apiKey' => 'required',
            'prompt' => 'required|min:100',
            'scheduleType' => 'required',
            'cronExpression' => [
                'required_if:scheduleType,custom',
                'valid_cron'
            ],
        ], [
            'scheduleType.required' => 'The Frequency field is required.',
            'apiKey.required' => 'The LLM field is required.',
            'cronExpression.valid_cron' => 'The cron expression is invalid.',
        ]);

        Tip::query()->create([
            'api_key_id' => $this->apiKey,
            'prompt' => trim($this->prompt),
            'schedule_type' => $this->scheduleType,
            'schedule_data' => $this->scheduleType === 'custom' ? ['cron' => trim($this->cronExpression)] : null,
        ]);

        $this->reset();
    }

    public function deleteTip($id): void
    {
        Tip::destroy($id);
    }
}
