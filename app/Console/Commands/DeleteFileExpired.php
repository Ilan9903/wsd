<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\FileOrFolder;
use App\Services\Minio\Bucket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteFileExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:set-file-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired unlinked files across all tenants (older than 24h).';

    /**
     * Execute the console command.
     *
     * @throws \Throwable
     */
    public function handle(): void
    {
        $this->info('Starting expired files cleanup');

        Client::chunk(100, function ($clients) {

            foreach ($clients as $client) {

                tenancy()->initialize($client->tenant);

                (new Bucket)->getBucket();

                $files = FileOrFolder::whereNull('link_id')
                    ->where('created_at', '<', now()->subDay())
                    ->forceDelete();

                Log::info("{$files} files deleted from tenant {$client->tenant}");
                $this->info("{$files} files deleted from tenant {$client->tenant}");
            }
        });

        tenancy()->end();
    }
}
