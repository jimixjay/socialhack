<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Controller extends BaseController
{
    protected function exceptionErrorResponse($e, $httpStatus = 500, $errorCode = 500, $responseMessage = null)
    {
        Log::error($e->getMessage());
        Log::error($e->getTraceAsString());

        if( $e instanceof \JsonException ){
            $responseMessage = 'JSON input error: '. $e->getMessage();
        }

        if( $e instanceof HttpException ){
            $httpStatus      = $e->getStatusCode();
            $responseMessage = $e->getMessage();
        }

        return responseError($errorCode, $responseMessage, $e, $httpStatus);
    }
}
