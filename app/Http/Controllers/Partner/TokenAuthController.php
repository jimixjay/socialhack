<?php

namespace App\Http\Controllers\Partner;


use App\Exceptions\PartnerCantBeCreated;
use App\Exceptions\TokenNotValid;
use App\Exceptions\UserCantBeCreated;
use App\Http\Controllers\Controller;
use App\Repositories\PartnerRepository;
use App\Services\Http\HttpClient;
use App\Services\Partner\PartnerCreator;
use App\Services\Partner\TokenAuthChecker;
use App\Services\Partner\TokenInfoGetter;
use Illuminate\Http\Request;


class TokenAuthController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function execute(TokenAuthChecker $tokenAuthChecker, PartnerRepository $partnerRepo)
    {
        try {
            $token = $this->request->get('idtoken');

            $clientId = $tokenAuthChecker->execute($token);

            $httpClient = new HttpClient(['base_uri' => 'https://oauth2.googleapis.com']);
            $tokenInfoGetter = new TokenInfoGetter($httpClient);
            $userInfo = $tokenInfoGetter->execute($token);

            try {
                $partnerCreator = new PartnerCreator($partnerRepo);
                $partner = $partnerCreator->execute($userInfo, $clientId);
            } catch (\Exception $e) {
                throw new PartnerCantBeCreated($e->getMessage());
            }

            return response()->json(['partner' => $partner]);

        } catch (TokenNotValid $e) {
            return $this->exceptionErrorResponse($e, 500, 'pta501', 'No se ha podido validar el login');
        } catch (PartnerCantBeCreated $e) {
            return $this->exceptionErrorResponse($e, 500, 'pta502', 'No se ha podido crear el partner');
        } catch (\Exception $e) {
            return $this->exceptionErrorResponse($e, 500, 'pta500', 'No se ha podido realizar la comprobación del token');
        }
    }
}