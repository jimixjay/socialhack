<?php

namespace App\Http\Controllers\User;


use App\Exceptions\TokenNotValid;
use App\Exceptions\UserCantBeCreated;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\Http\HttpClient;
use App\Services\User\TokenAuthChecker;
use App\Services\User\TokenInfoGetter;
use App\Services\User\UserCreator;
use Illuminate\Http\Request;


class TokenAuthController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function execute(TokenAuthChecker $tokenAuthChecker, UserRepository $userRepo)
    {
        try {
            $token = $this->request->get('idtoken');

            $clientId = $tokenAuthChecker->execute($token);

            $httpClient = new HttpClient(['base_uri' => 'https://oauth2.googleapis.com']);
            $tokenInfoGetter = new TokenInfoGetter($httpClient);
            $userInfo = $tokenInfoGetter->execute($token);

            try {
                $userCreator = new UserCreator($userRepo);
                $user = $userCreator->execute($userInfo, $clientId);
            } catch (\Exception $e) {
                throw new UserCantBeCreated($e->getMessage());
            }

            return response()->json(['user' => $user]);

        } catch (TokenNotValid $e) {
            return $this->exceptionErrorResponse($e, 500, 'uta501', 'No se ha podido validar el login');
        } catch (UserCantBeCreated $e) {
            return $this->exceptionErrorResponse($e, 500, 'uta502', 'No se ha podido crear el usuario');
        } catch (\Exception $e) {
            return $this->exceptionErrorResponse($e, 500, 'uta500', 'No se ha podido realizar la comprobación del token');
        }
    }
}