<?php


namespace App\Services\User;

use App\Services\Http\HttpClientInterface;

class TokenInfoGetter
{

    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function execute(string $token)
    {
        $this->httpClient->request('POST', 'tokeninfo?id_token=' . $token);

        return $this->httpClient->getResponseBody(false);
    }

}