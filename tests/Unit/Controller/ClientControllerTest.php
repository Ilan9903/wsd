<?php

namespace Controller;

use App\Models\Client;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\Datasets\Test\EmptyDataSet;
use Tests\Utils\TestCase;

#[Group('controller')]
#[Group('client')]
class ClientControllerTest extends TestCase
{
    #[Test]
    public function get_client()
    {
        $client = Client::where('name', '=', 'test')->first();
        $url = url()->to('api/checktenant');
        $response = $this->postJson($url, [
            'tenant_name' => 'test',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'client',
            'global_id',
            'bucket',
            'theme' => [
                'id',
                'name',
                'background',
                'primary_color',
                'secondary_color',
                'button_primary_color',
                'button_secondary_color',
                'button_text_primary_color',
                'button_text_secondary_color',
                'text_primary_color',
                'text_secondary_color',
                'logo_light_theme',
                'logo_dark_theme',
                'status',
            ],
        ]);

    }

    #[Test]
    public function it_requires_tenant_name()
    {
        $url = url()->to('api/checktenant');
        $response = $this->postJson($url);
        $response->assertStatus(422)
            ->assertJsonValidationErrors('tenant_name');
    }

    #[Test]
    public function get_client_not_found()
    {

        $url = url()->to('api/checktenant');
        $response = $this->postJson($url, [
            'tenant_name' => 'nonexistenttenant',
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'error' => __('client.not-found'),
        ]);
    }

    protected function getDatasetClass(): string
    {
        return EmptyDataSet::class;
    }
}
