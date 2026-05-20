<?php

namespace Tests\Unit\Controller;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class FileUploadControllerTest extends TenancyTestCase
{
    private string $url;

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->url = 'https://'.tenant()->id.config('wesend.domainApi').'/api/s3/multipart';
        $this->user = User::factory()->create();
        $this->token = auth('api')->login($this->user);

        Storage::fake('local');
    }

    #[Test]
    public function test_create_multipart_upload_validates_required_fields(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->json('POST', $this->url.'/create', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['filename']);
    }

    #[Test]
    public function test_sign_part_validates_required_fields(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->json('POST', $this->url.'/sign', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['key', 'uploadId', 'partNumber']);
    }

    #[Test]
    public function test_complete_validates_required_fields(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->json('POST', $this->url.'/complete', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['key', 'uploadId', 'parts']);
    }

    #[Test]
    public function test_abort_validates_required_fields(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->json('POST', $this->url.'/abort', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['key', 'uploadId']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        $this->user->delete();
        parent::tearDown();
    }
}
