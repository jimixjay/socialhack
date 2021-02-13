<?php

namespace App\Http\Controllers\Match;


use App\Exceptions\MatchAlreadyExists;
use App\Exceptions\MatchNotCreated;
use App\Exceptions\PartnerNotExists;
use App\Exceptions\UserNotExists;
use App\Http\Controllers\Controller;
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

    public function execute(MatchCreator $matchCreator, UserRepository $userRepo, PartnerRepository $partnerRepo)
    {
        try {
            $userId = $this->request->get('user_id');
            $partnerId = $this->request->get('partner_id');

            if (!$userRepo->exists($userId)) {
                throw new UserNotExists();
            }

            if (!$partnerRepo->exists($partnerId)) {
                throw new PartnerNotExists();
            }

            $matchCreator->execute($userId, $partnerId);

            return response()->json(['msg' => 'OK']);

        } catch (UserNotExists $e) {
            $this->exceptionErrorResponse($e, 500, 'mc501', 'El usuario no existe');
        } catch (PartnerNotExists $e) {
            $this->exceptionErrorResponse($e, 500, 'mc502', 'La asociación no existe');
        } catch (MatchAlreadyExists $e) {
            $this->exceptionErrorResponse($e, 500, 'mc504', 'Ya existe este match');
        } catch (MatchNotCreated $e) {
            $this->exceptionErrorResponse($e, 500, 'mc503', 'No se ha podido realizar el match. Por favor, inténtalo de nuevo');
        } catch (\Exception $e) {
            $this->exceptionErrorResponse($e, 500, 'mc500', 'Se ha producido un error en la petición');
        }
    }
}