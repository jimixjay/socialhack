<?php


namespace App\Services\User;

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

    public function execute($userInfo, string $clientId)
    {
        if ($this->userRepo->existsByClientId($clientId)) {
            return true;
        }

        $this->userRepo->createFromGoogle($userInfo, $clientId);
    }

}