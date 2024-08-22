<?php

namespace App\Livewire\Pages;

use App\Models\Tip;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Title;
use Livewire\Component;

class TipsNotifier extends Component
{
    protected $listeners = ['apiKeysUpdated' => '$refresh'];

    public $schedule_type = 'hourly';
    public $minute;
    public $hour;
    public $day_of_week;
    public $day_of_month;
    public $month;
    public $recurring_days = [];
    public $selected_tips = [];
    public $batch_action = '';

    protected $rules = [
        'tip' => 'required|string',
        'schedule_type' => 'required|string',
        'minute' => 'nullable|integer|between:0,59',
        'hour' => 'nullable|integer|between:0,23',
        'day_of_week' => 'nullable|integer|between:0,6',
        'day_of_month' => 'nullable|integer|between:1,31',
        'month' => 'nullable|integer|between:1,12',
        'recurring_days' => 'nullable|array',
        'recurring_days.*' => 'integer|between:1,31',
    ];

    public function mount(): void
    {
        $this->month = $this->month ?? Carbon::now()->month;
    }

    public function submit(): void
    {
        $this->validate();

        Tip::query()->create(['tip' => $this->tip,
            'schedule_type' => $this->schedule_type,
            'minute' => $this->minute,
            'hour' => $this->hour,
            'day_of_week' => $this->day_of_week,
            'day_of_month' => $this->day_of_month,
            'month' => $this->month,
            'recurring_days' => $this->recurring_days,
        ]);

        session()->flash('message', 'Tip scheduled successfully!');

        $this->reset(['tip', 'minute', 'hour', 'day_of_week', 'day_of_month', 'recurring_days']);
    }

    public function deleteTip($id)
    {
        Tip::query()->find($id)->delete();
    }

    public function batchDelete(): void
    {
        Tip::whereIn('id', $this->selected_tips)->delete();
        $this->reset(['selected_tips']);
        session()->flash('message', 'Selected tips deleted successfully!');
    }

    public function getTipsProperty(): Collection
    {
        return Tip::all();
    }

    public function getDaysInMonthProperty(): int
    {
        return Carbon::createFromDate(null, $this->month)->daysInMonth;
    }

    public function getPreviewProperty(): string
    {
        $preview = '';

        if ($this->schedule_type === 'custom') {
            $preview .= $this->hour ? "{$this->hour}:00" : '*';
            $preview .= $this->minute ? " at :{$this->minute}" : '';
            $preview .= $this->day_of_week !== null ? ' on ' . Carbon::createFromFormat('w', $this->day_of_week)->format('l') : '';
            $preview .= $this->day_of_month ? " on day {$this->day_of_month}" : '';
            $preview .= $this->month ? " of " . Carbon::createFromFormat('m', $this->month)->format('F') : '';
        } elseif ($this->schedule_type === 'recurring') {
            $days = implode(', ', $this->recurring_days);
            $preview .= "Every {$days} of each month at {$this->hour}:{$this->minute}";
        } else {
            $preview .= ucfirst(str_replace('_', ' ', $this->schedule_type));
        }

        return $preview;
    }

    public function triggerNotification($tip)
    {
        // send mail or SMS notification
    }

    public function handleFailure($reason)
    {
        Log::error('Task scheduling failed: ' . $reason);
        session()->flash('error', 'Task scheduling failed: ' . $reason);
    }

    #[Title('Tips Notifier')]
    public function render(): View|Factory|Application
    {
        return view('livewire.pages.tips-notifier', [
            'tips' => $this->tips,
            'daysInMonth' => $this->daysInMonth,
            'preview' => $this->preview,
        ]);
    }
}
