<?php

namespace App\Http\Controllers\Partner;


use App\Http\Controllers\Controller;
use App\Repositories\PartnerRepository;
use App\Services\Partner\PartnerList;
use Illuminate\Http\Request;


class ListController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function execute(PartnerRepository $partnerRepo)
    {
        try {
            $partnerList = new PartnerList($partnerRepo);

            $partners = $partnerList->execute();

            return response()->json($partners);

        } catch (\Exception $e) {
            return $this->exceptionErrorResponse($e, 500, 'pl500', 'No se ha podido consultar el listado de partners');
        }
    }
}