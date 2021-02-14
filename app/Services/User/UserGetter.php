<?php


namespace App\Services\User;

use App\Exceptions\MatchAlreadyExists;
use App\Exceptions\MatchNotCreated;
use App\Exceptions\PartnerNotExists;
use App\Exceptions\UserNotExists;
use App\Repositories\RepositoryInterface;

class UserGetter
{

    private $userRepo;

    public function __construct(RepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function execute($userId)
    {
        return $this->userRepo->getOneByUserId($userId);
    }

}