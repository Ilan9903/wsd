<?php

namespace App\Listeners\Eloquent\Client;

use App\Jobs\Client\ProcessClientCreation;
use App\Models\Client;

class HandleClientCreated
{
    /**
     * @param  Client  $client
     * @return void
     */
    public function handle(Client $client): void
    {

        ProcessClientCreation::dispatch($client);
    }
}
