<?php

namespace Tests\Unit\Hopla\GatewayManagement\Handlers;

use Hopla\GatewayManagement\Handlers\GatewayClient;
use Hopla\GatewayManagement\Handlers\GatewayManager;
use Hopla\GatewayManagement\Handlers\GatewayUser;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TestCase;

class GatewayManagerTest extends TestCase
{
    protected GatewayClient $gatewayClient;

    protected GatewayManager $gatewayManager;

    protected GatewayUser $gatewayUser;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            '*' => Http::response([], 200),
        ]);

        $this->gatewayClient = $this->createMock(GatewayClient::class);
        $this->gatewayUser = $this->createMock(GatewayUser::class);
        $this->gatewayManager = new GatewayManager($this->gatewayClient, $this->gatewayUser);
    }

    #[Test]
    public function test_check_client_we_drop_returns_array(): void
    {
        $tenantId = 'tenant-123';
        $expected = [
            'global_id_wdp' => 'abc-456',
            'client' => 'ClientTestName',
        ];

        $this->gatewayClient
            ->expects($this->once())
            ->method('checkClientWeDrop')
            ->with($tenantId)
            ->willReturn($expected);

        $resultManager = $this->gatewayManager->checkClientWeDrop($tenantId);

        $this->assertSame($expected, $resultManager);
        $this->assertArrayHasKey('global_id_wdp', $resultManager);
        $this->assertArrayHasKey('client', $resultManager);
    }

    #[Test]
    public function test_get_client_we_drop_returns_json(): void
    {
        $tenantId = 'tenant-123';
        $expected = [
            'message' => 'Client trouvé sur WeDrop',
            'client_wsd' => 'https://client.wesend.fr',
            'client_wdp' => 'https://client.wedrop.fr',
            'global_id_wsd_wdp' => 'abc-456',
        ];

        $this->gatewayClient
            ->expects($this->once())
            ->method('getClientWeDrop')
            ->with($tenantId)
            ->willReturn($expected);

        $resultManager = $this->gatewayManager->getClientWeDrop($tenantId);

        $this->assertSame($expected, $resultManager);

        $this->assertArrayHasKey('message', $expected);
        $this->assertArrayHasKey('client_wsd', $expected);
        $this->assertArrayHasKey('client_wdp', $expected);
        $this->assertArrayHasKey('global_id_wsd_wdp', $expected);
    }
}
