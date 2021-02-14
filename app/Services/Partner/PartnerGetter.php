<?php


namespace App\Services\Partner;

use App\Exceptions\MatchAlreadyExists;
use App\Exceptions\MatchNotCreated;
use App\Exceptions\PartnerNotExists;
use App\Exceptions\UserNotExists;
use App\Repositories\RepositoryInterface;

class PartnerGetter
{

    private $partnerRepo;

    public function __construct(RepositoryInterface $partnerRepo)
    {
        $this->partnerRepo = $partnerRepo;
    }

    public function execute(string $slug)
    {
        return $this->partnerRepo->getOneBySlug($slug);
    }

}