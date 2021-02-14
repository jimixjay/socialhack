<?php

namespace App\Http\Controllers\User;


use App\Exceptions\LoginFail;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\User\UserLogin;
use Illuminate\Http\Request;


class LoginController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function execute(UserRepository $userRepo)
    {
        try {
            $userLogin = new UserLogin($userRepo);
            $user = $userLogin->execute($this->request->all());

            return response()->json(['user' => $user]);

        } catch (LoginFail $e) {
            return $this->exceptionErrorResponse($e, 500, 'ul501', 'Usuario y/o contraseña incorrectos');
        } catch (\Exception $e) {
            return $this->exceptionErrorResponse($e, 500, 'ul500', 'No se ha podido inciar sesión por un error');
        }
    }
}