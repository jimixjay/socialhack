<?php


namespace App\Services\Partner;

use App\Repositories\RepositoryInterface;

class PartnerCreator
{

    private $partnerRepo;

    public function __construct(RepositoryInterface $partnerRepo)
    {
        $this->partnerRepo = $partnerRepo;
    }

    public function execute($userInfo, string $clientId)
    {
        if ($this->partnerRepo->existsByClientId($clientId)) {
            return $this->partnerRepo->getOneByClientId($clientId);
        }

        $this->partnerRepo->createFromGoogle($userInfo, $clientId);

        return $this->partnerRepo->getOneByClientId($clientId);
    }

}