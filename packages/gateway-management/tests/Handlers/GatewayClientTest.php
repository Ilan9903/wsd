<?php

namespace Tests\Unit\Hopla\GatewayManagement\Handlers;

use Hopla\GatewayManagement\Handlers\GatewayClient;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class GatewayClientTest extends TenancyTestCase
{
    #[Test]
    public function check_client_we_drop_return_array(): void
    {
        Http::fake([
            '*' => Http::response([
                'global_id_wdp' => 'abc',
                'client' => 'Test',
            ], 200),
        ]);

        $manager = new GatewayClient;

        $resultCheckClient = $manager->checkClientWeDrop('tenant-123');

        $this->assertEquals([
            'global_id_wdp' => 'abc',
            'client' => 'Test',
        ], $resultCheckClient);
    }

    #[Test]
    public function get_client_we_drop_returns_empty_if_no_bucket(): void
    {
        Http::fake([
            '*' => Http::response([], 200),
        ]);

        $manager = new GatewayClient;

        $resultGetClient = $manager->getClientWeDrop('tenant-123');

        $this->assertEquals([], $resultGetClient);
    }
}
