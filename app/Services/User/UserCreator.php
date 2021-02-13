<?php


namespace App\Services\Match;

use App\Exceptions\MatchAlreadyExists;
use App\Exceptions\MatchNotCreated;
use App\Exceptions\PartnerNotExists;
use App\Exceptions\UserNotExists;
use App\Repositories\RepositoryInterface;

class UserCreator
{

    private $userRepo;

    public function __construct(RepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function execute(?int $userId, ?int $partnerId)
    {
        if ($this->userRepo->exists($userId)) {
            throw new UserAlreadyExists();
        }

        if (!$this->partnerRepo->exists($partnerId)) {
            throw new PartnerNotExists();
        }

        $this->matchRepo->create(['user_id' => $userId, 'partner_id' => $partnerId]);
    }

}