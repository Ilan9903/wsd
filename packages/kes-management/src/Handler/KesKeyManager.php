<?php

namespace Hopla\KesManagement\Handler;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class KesKeyManager
{
    protected Client $client;

    protected string $kesUrl;

    protected string $cert;

    protected string $ssl_Key;

    protected ?string $sll_verify;

    public function __construct(?Client $client = null)
    {
        $this->client = $client ?? new Client;
        $this->kesUrl = rtrim(config('kes-management.kes_server'), '/');
        $this->cert = config('kes-management.ssl_cert');
        $this->ssl_Key = config('kes-management.ssl_key');
        $this->sll_verify = config('kes-management.ssl_verify');
    }

    /**
     * Génère une clé KMS pour le bucket.
     */
    public function generateKesKey(string $bucketName): mixed
    {
        try {
            $response = $this->client->request(
                'PUT',
                $this->kesUrl.'/v1/key/create/'.urlencode($bucketName),
                [
                    'headers' => ['Content-Type' => 'application/json'],
                    'cert' => $this->cert,
                    'ssl_key' => $this->ssl_Key,
                    'verify' => $this->sll_verify ?? false,
                ]
            );

            return json_decode($response->getBody()->getContents(), true);

        } catch (GuzzleException $e) {
            Log::error("Erreur Guzzle lors de la création de la clé KES ({$bucketName}) : ".$e->getMessage());

            return null;
        } catch (\Exception $e) {
            Log::error("Erreur inattendue lors de generateKesKey ({$bucketName}) : ".$e->getMessage());

            return null;
        }
    }

    /**
     * Supprime la clé KMS d’un bucket.
     */
    public function removeKesKey(string $bucketName): bool
    {
        try {
            $response = $this->client->request(
                'DELETE',
                "{$this->kesUrl}/v1/key/delete/".urlencode($bucketName),
                [
                    'headers' => ['Content-Type' => 'application/json'],
                    'cert' => $this->cert,
                    'ssl_key' => $this->ssl_Key,
                    'verify' => $this->sll_verify ?? false,
                ]
            );

            return $response->getStatusCode() === 204;

        } catch (GuzzleException $e) {
            Log::error("Erreur Guzzle lors de la suppression de la clé KES ({$bucketName}) : ".$e->getMessage());

            return false;
        } catch (\Exception $e) {
            Log::error("Erreur inattendue lors de removeKesKey ({$bucketName}) : ".$e->getMessage());

            return false;
        }
    }
}
