<?php

namespace App\Livewire;

use App\Models\Client;
use App\Services\Tenant\TenantFilament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        return ! TenantFilament::availableForNavigation();
    }

    protected function getStats(): array
    {
        $clients = Client::all();

        $totalUsedGb = round($clients->sum('used_storage') / 1024 ** 3, 2);
        $totalAllocatedGb = $clients->sum('allocated_storage');

        $usagePercentage = $totalAllocatedGb > 0
            ? ($totalUsedGb / $totalAllocatedGb) * 100
            : 0;

        $color = match (true) {
            $usagePercentage >= 90 => 'danger',
            $usagePercentage >= 70 => 'warning',
            default => 'success',
        };

        $criticalClients = $clients
            ->filter(fn (Client $client) => $client->allocated_storage > 0 &&
                (($client->used_storage / 1024 ** 3) / $client->allocated_storage) >= 0.9
            )
            ->map(fn (Client $client) => $client->name)
            ->implode(', ');

        return [

            Stat::make('Total clients', $clients->count())
                ->color('success'),

            Stat::make(
                'Stockage utilisé (global)',
                $totalUsedGb.' Go'
            )
                ->description(
                    sprintf(
                        '%s Go / %s Go (%.1f%%)',
                        $totalUsedGb,
                        $totalAllocatedGb,
                        $usagePercentage
                    )
                )
                ->descriptionIcon('heroicon-m-circle-stack')
                ->chart(
                    $clients->map(
                        fn (Client $client) => round($client->used_storage / 1024 ** 3, 2)
                    )->toArray()
                )
                ->color($color),

            Stat::make(
                'Clients à +90% de stockage utiliser',
                $criticalClients ?: 'Aucun'
            )
                ->description('Seuil critique dépassé')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($criticalClients ? 'danger' : 'success'),
        ];
    }
}
