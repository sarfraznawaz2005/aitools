<?php

namespace App\Livewire\Pages;

use App\Models\ApiKey;
use App\Models\Tip;
use App\Models\TipContent;
use App\Traits\InteractsWithToast;
use Cron\CronExpression;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Lorisleiva\CronTranslator\CronTranslator;
use Native\Laravel\Events\Settings\SettingChanged;
use Native\Laravel\Facades\Window;

class TipsNotifier extends Component
{
    use WithPagination;
    use InteractsWithToast;

    public Tip $model;

    public string $api_key_id = '';
    public string $name = '';
    public string $prompt = '';
    public string $cron = '';

    public string $searchQuery = '';

    public string $sortField = 'id';
    public bool $sortAsc = false;

    private CronExpression $CronExp;

    public function getListeners(): array
    {
        return [
            'apiKeysUpdated' => '$refresh',
            'tipContentUpdated' => '$refresh',
            'native:' . SettingChanged::class => '$refresh', // does not seem to work, maybe laravel socket must be installed
            'echo-private:' . SettingChanged::class => '$refresh', // does not seem to work, maybe laravel socket must be installed
            SettingChanged::class => '$refresh',
        ];
    }

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }

    #[Computed]
    public function tips(): Collection
    {
        return Tip::query()->latest()->get();
    }

    #[Computed]
    public function contents(): LengthAwarePaginator
    {
        return TipContent::query()
            ->with('tip')
            ->where('content', 'like', '%' . $this->searchQuery . '%')
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate(10);
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

        $this->success($this->model->exists ? 'Tip schedule saved successfully!' : 'Tip schedule added successfully!');

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

    public function toggleContentFavoriteStatus(TipContent $tipContent): void
    {
        $tipContent->favorite = !$tipContent->favorite;

        $tipContent->save();
    }

    public function deleteContent(TipContent $tipContent): void
    {
        $tipContent->delete();

        $this->success('Deleted successfully!');
    }

    public function deleteTip($id): void
    {
        Tip::destroy($id);

        $this->success('Tip schedule deleted successfully!');
    }

    public function viewContents(TipContent $content): void
    {
        try {
            Window::close('tipView');
        } catch (Exception) {
        } finally {
            openWindow(
                'tipView', 'tip-window', ['id' => $content->id, 'hideActions' => true],
                true, true, true, false, 1024, 700
            );
        }
    }

    public function resetForm(): void
    {
        $this->reset();

        $this->resetErrorBag();

        $this->model = new Tip();
    }
}
