<?php

namespace App\Http\Controllers\User;


use App\Exceptions\UserNotExists;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\User\UserGetter;
use Illuminate\Http\Request;


class GetOneController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function execute(string $slug, UserRepository $userRepo)
    {
        try {
            $userGetter = new UserGetter($userRepo);
            $user = $userGetter->execute($slug);

            return response()->json(['user' => $user]);

        } catch (UserNotExists $e) {
            return $this->exceptionErrorResponse($e, 500, 'gu501', 'El usuario no existe');
        } catch (\Exception $e) {
            return $this->exceptionErrorResponse($e, 500, 'gu500', 'No se ha podido realizar la consulta de usuario');
        }
    }
}