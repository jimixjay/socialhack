<?php


namespace App\Services\Http;


interface HttpClientInterface
{
    const BODY_TYPE_JSON            = 'json';
    const BODY_TYPE_FORM_PARAMS     = 'form_params';
    const BODY_TYPE_MULTIPART       = 'multipart';
    const BODY_ARRAY_KEY_FILES      = 'files';

    public function request(
        $method,
        $endpoint,
        $body = [],
        $options = [],
        $bodyType = self::BODY_TYPE_JSON,
        $debug = true
    );

    public function getResponseBody($asAssocArray);
}