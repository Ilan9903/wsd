<?php

namespace Hopla\HistoryManagement\Jobs;

use App\Models\History;
use App\Models\Link;
use App\Models\User;
use Hopla\HistoryManagement\Enums\HistoryActionType;
use Hopla\HistoryManagement\Enums\HistoryModelType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogHistoriesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $displayName = 'Log Histories';

    /**
     * @codeCoverageIgnore
     *
     * @return string[]
     */
    public function tags(): array
    {
        return [
            tenant()->id ?? 'Central domain',
        ];
    }

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly User $user,
        private readonly ?Link $link,
        private readonly HistoryModelType $model,
        private readonly HistoryActionType $action,
        private readonly string $description,
        private readonly string $ipAddress
    ) {}

    /**
     * @return void
     */
    public function handle(): void
    {
        History::create([
            'user_id' => $this->user->id,
            'link_id' => $this->link?->id,
            'model_type' => $this->model->value,
            'action' => $this->action->value,
            'description' => $this->description,
            'ip_address' => $this->ipAddress,
        ]);
    }
}
