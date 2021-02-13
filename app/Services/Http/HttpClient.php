<?php

namespace App\Services\Http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Psr7\Response;

final class HttpClient implements HttpClientInterface
{
    private $guzzleClient;
    private $logChannel;
    private $options = [];
    private $response;
    private $responseBody;

    public function __construct(array $guzzleClientConfig = [], $logChannel = null)
    {
        $this->guzzleClient = new GuzzleClient($guzzleClientConfig);
        $this->logChannel   = $logChannel ? $logChannel : config('app.default_guzzle_log_channel');
    }

    public function request( $method, $endpoint, $body = [], $options = [], $bodyType = self::BODY_TYPE_JSON, $debug = true)
    {
        $this->options = !empty($options) ? array_merge($this->options, $options) : $this->options;
        $this->setOptionsHeaders();
        $this->setOptionsBody($body, $bodyType);
        if($debug) {
            $this->setDebugger();
        }

        if (empty($this->options['timeout'])) {
            $this->options['timeout'] = config('app.http_default_TTL');
        }

        $this->response = $this->guzzleClient->request($method, $endpoint, $this->options);

        if ($this->response->getStatusCode() != 200) {
            throw new \Exception('Error response code ' . $this->response->getStatusCode());
        }

        return $this;
    }

    public function setJwt($jwt)
    {
        $this->options['headers']['Authorization'] = 'Bearer ' . $jwt;
    }

    public function getResponseBody($asAssocArray = false)
    {
        $this->responseBody = (string)$this->response->getBody();
        $header             = $this->response->getHeader('content-type');

        if (!empty($header[0]) && 'application/json' == substr($header[0], 0, 16)) {

            $this->responseBody = json_decode($this->responseBody, $asAssocArray);
        }

        return $this->responseBody;
    }

    private function setOptionsHeaders()
    {
        $headers = [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json'
        ];

        $this->options['headers'] = !empty($this->options['headers']) ? array_merge($headers, $this->options['headers']) : $headers;
    }

    private function setOptionsBody($body = [], $bodyType)
    {
        if(empty($body)){
            return;
        }

        switch ($bodyType) {

            case self::BODY_TYPE_MULTIPART:

                foreach ($body as $key => $value) {

                    if ($key == self::BODY_ARRAY_KEY_FILES && !empty($body[self::BODY_ARRAY_KEY_FILES])) {

                        foreach ($body[self::BODY_ARRAY_KEY_FILES] as $fileData) {
                            $this->options['multipart'][] = [
                                'name'     => $fileData['name'],
                                'contents' => fopen($fileData['file'], 'r'),
                                'filename' => basename($fileData['file']),
                            ];
                        }

                    } else {

                        $this->options['multipart'][] = [
                            'name'     => $key,
                            'contents' => is_array($value) ? json_encode($value) : $value,
                        ];
                    }
                }
                break;

            case self::BODY_TYPE_FORM_PARAMS:
                $this->options['form_params'] = $body;
                break;

            case self::BODY_TYPE_JSON:
                $this->options['json'] = $body;
                break;
        }
    }

    /**
     * Fija en GuzzleClient un único logger para cada petición
     * https://github.com/guzzle/guzzle/blob/master/src/MessageFormatter.php#L12
     */
    private function setDebugger()
    {
        $requestId = uniqid('req_', true);

        /** @var HandlerStack $clientHandler */
        $clientHandler = $this->guzzleClient->getConfig('handler');

        $loggerTemplate = "\n" . str_repeat("=", 80) . "\n";
        $loggerTemplate .= str_repeat(" ", 34) . "NEW REQUEST\n";
        $loggerTemplate .= str_repeat("=", 80) . "\n";
        $loggerTemplate .= date('Y-m-d\TH:i:sP') . "\n";
        $loggerTemplate .= "RequestId: $requestId \n";
        $loggerTemplate .= "Request headers [$requestId] \n";
        $loggerTemplate .= str_repeat("-", 45) . "\n";
        $loggerTemplate .= "{req_headers}\n";
        $loggerTemplate .= "Request body [$requestId]\n";
        $loggerTemplate .= str_repeat("-", 45) . "\n";
        $loggerTemplate .= "{req_body}\n";
        $loggerTemplate .= "\n" . str_repeat("=", 80) . "\n";
        $loggerTemplate .= "\n{date_iso_8601}\n";
        $loggerTemplate .= "Response headers [$requestId]\n";
        $loggerTemplate .= str_repeat("-", 45) . "\n";
        $loggerTemplate .= "{res_headers}\n";
        $loggerTemplate .= "Response body [$requestId]\n";
        $loggerTemplate .= str_repeat("-", 45) . "\n";
        $loggerTemplate .= "{res_body}\n";

        $clientHandler->remove('guzzleLogger');
        $clientHandler->push(
            Middleware::log(
                Log::channel($this->logChannel),
                new MessageFormatter($loggerTemplate)
            ),
            'guzzleLogger'
        );

        $this->options['handler'] = $clientHandler;
    }

}