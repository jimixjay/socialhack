<?php

namespace App\Http\Controllers\Match;


use App\Exceptions\MatchAlreadyExists;
use App\Exceptions\PartnerNotExists;
use App\Exceptions\UserNotExists;
use App\Http\Controllers\Controller;
use App\Repositories\MatchRepository;
use App\Repositories\PartnerRepository;
use App\Repositories\UserRepository;
use App\Services\Match\MatchCreator;
use Illuminate\Http\Request;


class CreateController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function execute(MatchRepository $matchRepo, UserRepository $userRepo, PartnerRepository $partnerRepo)
    {
        try {
            $matchCreator = new MatchCreator($matchRepo, $userRepo, $partnerRepo);

            $userId = $this->request->get('user_id');
            $partnerId = $this->request->get('partner_id');

            $matchCreator->execute($userId, $partnerId);

            return response()->json(['msg' => 'OK']);

        } catch (UserNotExists $e) {
            return $this->exceptionErrorResponse($e, 500, 'mc501', 'El usuario no existe');
        } catch (PartnerNotExists $e) {
            return $this->exceptionErrorResponse($e, 500, 'mc502', 'La asociación no existe');
        } catch (MatchAlreadyExists $e) {
            return $this->exceptionErrorResponse($e, 500, 'mc504', 'Ya existe este match');
        } catch (\Exception $e) {
            return $this->exceptionErrorResponse($e, 500, 'mc503', 'No se ha podido realizar el match. Por favor, inténtalo de nuevo');
        }
    }
}