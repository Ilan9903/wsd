<?php

namespace App\Livewire\Tenants;

use App\Models\TenantContract;
use App\Models\User;
use App\Services\Tenant\TenantFilament;
use Filament\Widgets\ChartWidget;

class UserChart extends ChartWidget
{
    protected ?string $heading = 'Stockage global';

    public static function canView(): bool
    {
        return TenantFilament::availableForNavigation();
    }

    protected function getData(): array
    {
        TenantFilament::initializeTenant();

        $users = User::query()
            ->where('used_storage', '>', 0)
            ->orderByDesc('used_storage')
            ->get();

        $contract = TenantContract::first();

        $totalAllocated = (float) $contract->allocated_storage;

        $usersUsed = $users
            ->map(fn ($user) => round($user->used_storage / 1024 ** 3, 2));

        $totalUsed = $usersUsed->sum();

        $freeStorage = max(
            round($totalAllocated - $totalUsed, 2),
            0
        );

        $labels = $users->pluck('name')->toArray();
        $labels[] = 'Stockage libre';

        $userData = $usersUsed->toArray();
        $userData[] = $freeStorage;

        $colors = $users
            ->map(fn () => $this->randomColor())
            ->toArray();

        $colors[] = '#808080';

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Répartition du stockage (Go)',
                    'data' => $userData,
                    'backgroundColor' => $colors,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    private function randomColor(): string
    {
        return sprintf(
            '#%02X%02X%02X',
            rand(80, 220),
            rand(80, 220),
            rand(80, 220),
        );
    }
}
