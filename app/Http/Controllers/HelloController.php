<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class HelloController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {

        $this->request = $request;
    }

    public function hello($name)
    {
        return response()->json('hello ' . $name);
    }
}
