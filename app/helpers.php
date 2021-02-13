<?php

function responseError($errorCode, $errorMessage, $exception, $httpStatusCode = 500)
{
    $responseArray = [
        'errorCode' => $errorCode,
        'msg'       => $errorMessage,
        'msg_complete' => $exception->getMessage(),
        'trace' => $exception->getTraceAsString(),
    ];

    return response()->json([
            'error' => $responseArray
        ]
        , $httpStatusCode);
}