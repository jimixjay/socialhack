<?php


namespace App\Services\User;

use App\Exceptions\TokenNotValid;

class TokenAuthChecker
{

    public function __construct()
    {
    }

    public function execute(?string $token)
    {
        if (!$token) {
            throw new TokenNotValid();
        }

        $client = new \Google_Client(['client_id' => getenv('GOOGLE_API_CLIENT_ID')]);
        $payload = $client->verifyIdToken($token);

        if (!$payload) {
            throw new TokenNotValid();
        }

        $userId = $payload['sub'];
        return $userId;
    }

}