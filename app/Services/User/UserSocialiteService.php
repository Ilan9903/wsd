<?php

namespace App\Services\User;

use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class UserSocialiteService
{
    /**
     * @param  User  $user
     * @param  array<string, string|null>  $fields
     * @return void
     */
    public function updateUserFieldsIfNotNull(User $user, array $fields): void
    {
        foreach ($fields as $attribute => $attributeValue) {
            if (! is_null($attributeValue)) {
                $user->$attribute = $attributeValue;
            }
        }

        $user->save();
    }

    /**
     * @param  string  $accessToken
     * @return array<string, mixed>
     *
     * @throws GuzzleException
     *
     * @codeCoverageIgnore
     */
    public function fetchGoogleUserDetails(string $accessToken): array
    {
        $client = new Client;

        $response = $client->get('https://people.googleapis.com/v1/people/me', [
            'query' => [
                'personFields' => 'addresses,phoneNumbers',
            ],
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken,
            ],
        ]);

        $userData = json_decode($response->getBody(), true);

        return [
            'city' => $userData['addresses'][0]['city'] ?? null,
            'country' => $userData['addresses'][0]['country'] ?? null,
            'phone' => $userData['phoneNumbers'][0]['value'] ?? null,
        ];
    }

    /**
     * @param  string  $accessToken
     * @param  string  $id
     * @return string|null
     *
     * @throws ConnectionException
     *
     * @codeCoverageIgnore
     */
    public function fetchMicrosoftUserDetails(string $accessToken, string $id): ?string
    {
        $response = Http::withToken($accessToken)
            ->get('https://graph.microsoft.com/v1.0/me/photo/$value');
        if ($response->successful()) {
            $fileName = 'avatars/'.tenant()->id.'/'.$id.'.jpg';
            Storage::disk('public')->put($fileName, $response->body());
            $avatarPath = $fileName;

            return $avatarPath;
        }

        return null;
    }
}
