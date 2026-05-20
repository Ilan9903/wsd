<?php

namespace App\Console\Commands;

use App\Models\Client;
use Hopla\GatewayManagement\Facade\GatewayManager;
use Illuminate\Console\Command;

class SyncWedropClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-wedrop-client';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting SyncWedropClient');

        $clients = Client::where('wedrop_url', null)->get();

        $count = 0;
        foreach ($clients as $client) {
            $url = GatewayManager::getClientWeDrop($client->tenant);
            if ($url == null) {
                $this->info("{$client->name} n'as pas wedrop");

                continue;
            }

            tenancy()->end();

            GatewayManager::syncClientWeDrop($url);
            $this->info("{$client->name}  a été synchro ");
            $count++;
        }
        $this->info($count.' client  on été synchro');
    }
}
