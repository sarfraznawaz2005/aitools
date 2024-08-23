<?php

namespace App\Livewire\Pages;

use App\Helpers\CronHelper;
use App\Models\Tip;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

class TipsNotifier extends Component
{
    protected $listeners = ['apiKeysUpdated' => '$refresh'];

    public $content = '';
    public $scheduleType = 'every_minute';
    public $scheduleData = [
        'minute' => '*',
        'hour' => '*',
        'day' => '*',
        'weekday' => '*',
        'month' => '*',
    ];
    public $tips = [];

    public $customFieldHints = [
        'minute' => 'Enter 0-59, */5 for every 5 minutes, or 1,11,21,31,41,51 for specific minutes',
        'hour' => 'Enter 0-23, */2 for every 2 hours, or 9,12,15 for specific hours',
        'day' => 'Enter 1-31, */3 for every 3 days, or 1,15 for specific days',
        'weekday' => 'Enter 0-6 (0 is Sunday), 1-5 for weekdays, or 6,0 for weekends',
        'month' => 'Enter 1-12, */2 for every 2 months, or 3,6,9,12 for specific months',
    ];

    private function getCustomSchedulePreview()
    {
        $parts = [];
        $cronParts = ['minute', 'hour', 'day', 'weekday', 'month'];

        foreach ($cronParts as $part) {
            if ($this->scheduleData[$part] !== '*') {
                $parts[] = $this->getPartDescription($part, $this->scheduleData[$part]);
            }
        }

        return empty($parts) ? 'Every minute' : 'Every ' . implode(', ', $parts);
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
            return 'on ' . Carbon::create()->weekday($value)->format('l') . 's';
        }

        if ($part === 'month') {
            return 'in ' . Carbon::create()->month($value)->format('F');
        }

        return "at $value " . Str::singular($part);
    }

    private function formatListOfValues($part, $values)
    {
        if ($part === 'weekday') {
            $days = array_map(function($day) {
                return Carbon::create()->weekday($day)->format('l');
            }, $values);
            return 'on ' . implode(', ', $days);
        }

        if ($part === 'month') {
            $months = array_map(function($month) {
                return Carbon::create()->month($month)->format('F');
            }, $values);
            return 'in ' . implode(', ', $months);
        }

        return "at " . implode(', ', $values) . ' ' . Str::plural($part);
    }

    #[Computed]
    public function schedulePreview()
    {
        $preview = match ($this->scheduleType) {
            'every_minute' => 'Every Minute',
            'every_hour' => 'Every Hour',
            'every_day' => 'Every Day',
            'every_week' => 'Every Week',
            'every_month' => 'Every Month',
            'custom' => $this->getCustomSchedulePreview(),
        };

        return $preview;
    }

    #[Computed]
    public function nextRuns()
    {
        $nextRuns = [];
        $date = now();
        $cronExpression = $this->getCronExpression();

        for ($i = 0; $i < 5; $i++) {
            $nextRun = CronHelper::getNextRunDate($cronExpression, $date);
            $nextRuns[] = $nextRun->format('Y-m-d H:i:s');
            $date = $nextRun->addMinute();
        }

        return $nextRuns;
    }

    private function getCronExpression()
    {
        if ($this->scheduleType === 'custom') {
            return implode(' ', [
                $this->scheduleData['minute'],
                $this->scheduleData['hour'],
                $this->scheduleData['day'],
                $this->scheduleData['weekday'],
                $this->scheduleData['month'],
            ]);
        }

        return match ($this->scheduleType) {
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
        ]);

        Tip::create([
            'content' => $this->content,
            'schedule_type' => $this->scheduleType,
            'schedule_data' => $this->scheduleData,
        ]);

        $this->reset(['content', 'scheduleType', 'scheduleData']);
    }

    public function deleteTip($id)
    {
        Tip::destroy($id);
    }

}
