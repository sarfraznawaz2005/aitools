<?php

namespace App\Livewire\Pages;

use App\Helpers\CronHelper;
use App\Models\Tip;
use Carbon\Carbon;
use Cron\CronExpression;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Lorisleiva\CronTranslator\CronTranslator;

class TipsNotifier extends Component
{
    protected $listeners = ['apiKeysUpdated' => '$refresh'];

    public $content = '';
    public $scheduleType = 'every_minute';
    public $cronExpression = '* * * * *';
    public $tips = [];

    #[Computed]
    public function schedulePreview()
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


    private function getCustomSchedulePreview()
    {
        try {
            $humanReadable = CronTranslator::translate($this->cronExpression);
            return ucfirst($humanReadable);
        } catch (\Exception $e) {
            return 'Invalid cron expression';
        }
    }

    private function getPartDescription($part, $value)
    {
        if (strpos($value, '/') !== false) {
            list($_, $step) = explode('/', $value);
            return "every $step " . Str::plural($part);
        }

        if (strpos($value, ',') !== false) {
            $values = explode(',', $value);
            return $this->formatListOfValues($part, $values);
        }

        if ($part === 'weekday') {
            return 'on ' . $this->getDayName((int)$value);
        }

        if ($part === 'month') {
            return 'in ' . $this->getMonthName((int)$value);
        }

        return "at $value " . Str::singular($part);
    }

    private function formatListOfValues($part, $values)
    {
        if ($part === 'weekday') {
            $days = array_map(function($day) {
                return $this->getDayName((int)$day);
            }, $values);
            return 'on ' . implode(', ', $days);
        }

        if ($part === 'month') {
            $months = array_map(function($month) {
                return $this->getMonthName((int)$month);
            }, $values);
            return 'in ' . implode(', ', $months);
        }

        return "at " . implode(', ', $values) . ' ' . Str::plural($part);
    }

    private function getDayName(int $day): string
    {
        return Carbon::create()->weekday($day)->format('l');
    }

    private function getMonthName(int $month): string
    {
        return Carbon::create()->month($month)->format('F');
    }

    #[Computed]
    public function nextRuns()
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

    private function getCronExpression()
    {
        return $this->scheduleType === 'custom' ? $this->cronExpression : match ($this->scheduleType) {
            'every_minute' => '* * * * *',
            'every_hour' => '0 * * * *',
            'every_day' => '0 0 * * *',
            'every_week' => '0 0 * * 0',
            'every_month' => '0 0 1 * *',
            default => '* * * * *',
        };
    }


    #[Title('Tips Notifier')]
    public function render(): View|Factory|Application
    {
        $this->tips = Tip::all();
        return view('livewire.pages.tips-notifier');
    }

    public function saveTip()
    {
        $this->validate([
            'content' => 'required|min:3',
            'scheduleType' => 'required|in:every_minute,every_hour,every_day,every_week,every_month,custom',
            'cronExpression' => 'required_if:scheduleType,custom|regex:/^(\*|([0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])) (\*|([0-9]|1[0-9]|2[0-3])) (\*|([1-9]|1[0-9]|2[0-9]|3[0-1])) (\*|([1-9]|1[0-2])) (\*|([0-6]))$/',
        ]);

        Tip::create([
            'content' => $this->content,
            'schedule_type' => $this->scheduleType,
            'schedule_data' => $this->scheduleType === 'custom' ? ['cron' => $this->cronExpression] : null,
        ]);

        $this->reset(['content', 'scheduleType', 'cronExpression']);
    }

    public function deleteTip($id)
    {
        Tip::destroy($id);
    }
}
