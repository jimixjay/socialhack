<?php

namespace App\Http\Controllers\User;


use App\Exceptions\LoginFail;
use App\Exceptions\TokenNotValid;
use App\Exceptions\UserCantBeCreated;
use App\Exceptions\UserNotExists;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\Http\HttpClient;
use App\Services\User\TokenAuthChecker;
use App\Services\User\TokenInfoGetter;
use App\Services\User\UserCreator;
use App\Services\User\UserGetter;
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