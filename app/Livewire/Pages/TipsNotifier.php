<?php

namespace App\Livewire\Pages;

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

    public string $content = '';
    public string $scheduleType = 'every_minute';
    public string $cronExpression = '* * * * *';

    #[Computed]
    public function tips(): Collection
    {
        return Tip::all();
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
            $humanReadable = CronTranslator::translate($this->cronExpression);
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
        $nextRuns = [];
        $date = now();
        $cronExpression = new CronExpression($this->getCronExpression());

        for ($i = 0; $i < 5; $i++) {
            $nextRun = $cronExpression->getNextRunDate($date);
            $nextRuns[] = $nextRun->format('Y-m-d H:i:s');
            $date = $nextRun;
        }

        return $nextRuns;
    }

    private function getCronExpression(): string
    {
        return $this->scheduleType === 'custom' ? $this->cronExpression : match ($this->scheduleType) {
            'every_minute' => '* * * * *',
            'every_hour' => '0 * * * *',
            'every_day' => '0 0 * * *',
            'every_week' => '0 0 * * 0',
            'every_month' => '0 0 1 * *',
        };
    }


    #[Title('Tips Notifier')]
    public function render(): View|Factory|Application
    {
        return view('livewire.pages.tips-notifier');
    }

    public function saveTip(): void
    {
        $this->validate([
            'content' => 'required|min:3',
            'scheduleType' => 'required|in:every_minute,every_hour,every_day,every_week,every_month,custom',
            'cronExpression' => 'required_if:scheduleType,custom|regex:/^(\*|([0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])) (\*|([0-9]|1[0-9]|2[0-3])) (\*|([1-9]|1[0-9]|2[0-9]|3[0-1])) (\*|([1-9]|1[0-2])) (\*|([0-6]))$/',
        ]);

        Tip::query()->create([
            'content' => $this->content,
            'schedule_type' => $this->scheduleType,
            'schedule_data' => $this->scheduleType === 'custom' ? ['cron' => $this->cronExpression] : null,
        ]);

        $this->reset(['content', 'scheduleType', 'cronExpression']);
    }

    public function deleteTip($id): void
    {
        Tip::destroy($id);
    }
}
