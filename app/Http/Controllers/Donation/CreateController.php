<?php

namespace App\Http\Controllers\Donation;


use App\Exceptions\Donation\IncorrectAmount;
use App\Exceptions\PartnerNotExists;
use App\Exceptions\UserNotExists;
use App\Http\Controllers\Controller;
use App\Repositories\DonationRepository;
use App\Repositories\PartnerRepository;
use App\Repositories\UserRepository;
use App\Services\Donation\DonationCreator;
use Illuminate\Http\Request;


class CreateController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function execute(DonationRepository $donationRepo, UserRepository $userRepo, PartnerRepository $partnerRepo)
    {
        try {
            $donationCreator = new DonationCreator($donationRepo, $userRepo, $partnerRepo);

            $donationCreator->execute($this->request->all());

            return response()->json(['msg' => 'OK']);

        } catch (IncorrectAmount $e) {
            return $this->exceptionErrorResponse($e, 500, 'dc503', 'La cantidad a donar es incorrecta');
        } catch (UserNotExists $e) {
            return $this->exceptionErrorResponse($e, 500, 'dc501', 'El usuario no existe');
        } catch (PartnerNotExists $e) {
            return $this->exceptionErrorResponse($e, 500, 'dc502', 'La asociación no existe');
        } catch (\Exception $e) {
            return $this->exceptionErrorResponse($e, 500, 'dc503', 'No se ha podido realizar la donación. Por favor, inténtalo de nuevo');
        }
    }
}