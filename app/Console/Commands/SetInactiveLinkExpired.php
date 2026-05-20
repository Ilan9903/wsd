<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Link;
use App\Models\Tenant;
use App\Services\Minio\Bucket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SetInactiveLinkExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:set-inactive-link-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set is_active to false for Expired Links';

    /**
     * Execute the console command.
     *
     * @throws \Throwable
     */
    public function handle(): void
    {
        $this->info('Starting analysis Links');
        /** @var Tenant $tenant */
        Client::all()->each(function ($tenant) {
            tenancy()->initialize($tenant->tenant);
            (new Bucket)->getBucket();

            $linkInTrash = Link::withTrashed()
                ->where('expired_at', '<', now()->subDays(30))
                ->where('is_active', true)
                ->get();

            $count = $linkInTrash->count();

            if ($count > 0) {

                Link::withoutEvents(function () use ($linkInTrash) {
                    $linkInTrash->each(function ($link) {
                        $link->update(['is_active' => false]);
                    });
                });
            }

            Log::info("{$count} links have been deactivated from tenant {$tenant->tenant}");
            $this->info("{$count} links have been deactivated from tenant {$tenant->tenant}");
        });

        tenancy()->end();
    }
}
