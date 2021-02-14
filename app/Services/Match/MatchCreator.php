<?php


namespace App\Services\Match;

use App\Exceptions\PartnerNotExists;
use App\Exceptions\UserNotExists;
use App\Repositories\RepositoryInterface;

class MatchCreator
{

    private $matchRepo;
    private $userRepo;
    private $partnerRepo;

    public function __construct(RepositoryInterface $matchRepo, RepositoryInterface $userRepo, RepositoryInterface $partnerRepo)
    {
        $this->matchRepo = $matchRepo;
        $this->userRepo = $userRepo;
        $this->partnerRepo = $partnerRepo;
    }

    public function execute(?int $userId, ?int $partnerId)
    {
        if (!$this->userRepo->exists($userId)) {
            throw new UserNotExists();
        }

        if (!$this->partnerRepo->exists($partnerId)) {
            throw new PartnerNotExists();
        }

        $this->matchRepo->create(['user_id' => $userId, 'partner_id' => $partnerId]);
    }

}