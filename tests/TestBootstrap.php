<?php

namespace Tests;

use App\Models\Client;
use Carbon\Carbon;

class TestBootstrap
{
    public $app;

    public function createTestingTenant(): void
    {
        $this->removeTestingTenant();
        Client::factory()->create([
            'name' => 'test',
            'bucket' => 'test',
            'wedrop_url' => 'test.api.wedrop.xyz',
        ]);
    }

    public function removeTestingTenant(): void
    {
        Client::where('name', 'test')->update([
            'deleted_at' => Carbon::now()->subDays(31),
        ]);
        \Artisan::call('model:prune');
    }
}
