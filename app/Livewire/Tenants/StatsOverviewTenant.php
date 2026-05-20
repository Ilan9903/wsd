<?php

namespace App\Livewire\Tenants;

use App\Models\TenantContract;
use App\Models\User;
use App\Services\Tenant\TenantFilament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewTenant extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        return TenantFilament::availableForNavigation();
    }

    protected function getStats(): array
    {
        TenantFilament::initializeTenant();

        $contract = TenantContract::query()->firstOrFail();

        $usersCount = User::count();

        $usedGo = round($contract->used_storage / 1024 ** 3, 2);
        $allocatedGo = $contract->allocated_storage;
        $freeGo = max($allocatedGo - $usedGo, 0);

        $usagePercentage = $allocatedGo > 0
            ? round(($usedGo / $allocatedGo) * 100, 1)
            : 0;

        $color = match (true) {
            $usagePercentage >= 90 => 'danger',
            $usagePercentage >= 70 => 'warning',
            default => 'success',
        };

        $usersStorageChart = User::query()
            ->where('used_storage', '>', 0)
            ->pluck('used_storage')
            ->map(fn ($bytes) => round($bytes / 1024 ** 3, 2))
            ->toArray();

        return [

            Stat::make('Utilisateurs', $usersCount)
                ->icon('heroicon-m-users')
                ->color('success'),

            Stat::make(
                'Contrat',
                "{$usedGo} / {$allocatedGo} Go"
            )
                ->description(
                    "Libre : {$freeGo} Go\n".
                    "Utilisateurs max : {$contract->allocated_users}\n".
                    "Bucket : {$contract->bucket}"
                )
                ->icon('heroicon-m-circle-stack')
                ->color($color),

            Stat::make(
                'Stockage global',
                "{$usedGo} Go"
            )
                ->description(
                    "{$usedGo} / {$allocatedGo} Go ({$usagePercentage}%)"
                )
                ->descriptionIcon('heroicon-m-chart-bar')
                ->chart($usersStorageChart)
                ->color($color),
        ];
    }
}
