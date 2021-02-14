<?php


namespace App\Services\Donation;

use App\Exceptions\Donation\IncorrectAmount;
use App\Exceptions\PartnerNotExists;
use App\Exceptions\UserNotExists;
use App\Repositories\RepositoryInterface;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class DonationCreator
{

    private $donationRepo;
    private $userRepo;
    private $partnerRepo;

    public function __construct(RepositoryInterface $donationRepo, RepositoryInterface $userRepo, RepositoryInterface $partnerRepo)
    {
        $this->donationRepo = $donationRepo;
        $this->userRepo = $userRepo;
        $this->partnerRepo = $partnerRepo;
    }

    public function execute(array $data)
    {
        if (!array_key_exists('user_id', $data) || !$this->userRepo->exists($data['user_id'])) {
            throw new UserNotExists();
        }

        $user = $this->userRepo->getOneByUserId($data['user_id']);

        if (!array_key_exists('partner_id', $data) || !$this->partnerRepo->exists($data['partner_id'])) {
            throw new PartnerNotExists();
        }

        $partner = $this->partnerRepo->getOneByPartnerId($data['partner_id']);

        if (!array_key_exists('amount', $data) || !is_int($data['amount']) || $data['amount'] <= 0) {
            throw new IncorrectAmount();
        }

        // Set your secret key. Remember to switch to your live secret key in production.
        // See your keys here: https://dashboard.stripe.com/account/apikeys
        Stripe::setApiKey(env('STRIPE_API_KEY'));

        $paymentInfo = PaymentIntent::create([
            'amount' => $data['amount'],
            'currency' => 'eur',
            'payment_method_types' => ['card'],
            'receipt_email' => $user->email,
        ], ['stripe_account' => $partner->stripe_account_id]);

        $data['payload'] = json_encode($paymentInfo);

        $this->donationRepo->create($data);

        return true;
    }

}