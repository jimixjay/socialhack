<?php

namespace App\Exceptions\Post;


use Exception;
use Throwable;

class TitleCantBeEmpty extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}