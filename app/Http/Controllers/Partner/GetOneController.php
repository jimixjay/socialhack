<?php

namespace App\Http\Controllers\Partner;


use App\Exceptions\PartnerNotExists;
use App\Http\Controllers\Controller;
use App\Repositories\PartnerRepository;
use App\Services\Partner\PartnerGetter;
use Illuminate\Http\Request;


class GetOneController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function execute(string $slug, PartnerRepository $partnerRepo)
    {
        try {
            $partnerGetter = new PartnerGetter($partnerRepo);
            $partner = $partnerGetter->execute($slug);

            return response()->json(['partner' => $partner]);

        } catch (PartnerNotExists $e) {
            return $this->exceptionErrorResponse($e, 500, 'gp501', 'El partner no existe');
        } catch (\Exception $e) {
            return $this->exceptionErrorResponse($e, 500, 'gp500', 'No se ha podido realizar la consulta de partner');
        }
    }
}