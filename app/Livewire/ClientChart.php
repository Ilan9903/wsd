<?php

namespace App\Livewire;

use App\Models\Client;
use App\Services\Tenant\TenantFilament;
use Filament\Widgets\ChartWidget;

class ClientChart extends ChartWidget
{
    protected ?string $heading = 'Clients - Stockage';

    protected function getType(): string
    {
        return 'bar';
    }

    public static function canView(): bool
    {
        return ! TenantFilament::availableForNavigation();
    }

    protected bool $isCollapsible = true;

    protected function getData(): array
    {
        $clients = Client::all();

        $labels = $clients->pluck('name')->toArray();

        $usedData = $clients->map(fn ($client) => round($client->used_storage / 1024 ** 3, 2))->toArray();
        $allocatedData = $clients->map(fn ($client) => round($client->allocated_storage / 1024 ** 3, 2))->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Utilisé (Go)',
                    'data' => $usedData,
                    'borderColor' => $clients->map(function ($client) {
                        $percentage = $client->allocated_storage > 0
                            ? (($client->used_storage / 1024 ** 3) / $client->allocated_storage) * 100
                            : 0;

                        return match (true) {
                            $percentage >= 90 => '#dc2626',
                            $percentage >= 70 => '#f97316',
                            default => '#16a34a',
                        };
                    })->toArray(),
                    'backgroundColor' => $clients->map(function ($client) {
                        $percentage = $client->allocated_storage > 0
                            ? (($client->used_storage / 1024 ** 3) / $client->allocated_storage) * 100
                            : 0;

                        return match (true) {
                            $percentage >= 90 => '#dc2626',
                            $percentage >= 70 => '#f97316',
                            default => '#16a34a',
                        };
                    })->toArray(),
                ],
                [
                    'label' => 'Alloué (Go)',
                    'data' => $allocatedData,
                    'backgroundColor' => '#2563eb',
                    'borderColor' => '#2563eb',
                ],
            ],
        ];
    }
}
