<?php


namespace App\Services\User;

use App\Exceptions\LoginFail;
use App\Exceptions\MatchAlreadyExists;
use App\Exceptions\MatchNotCreated;
use App\Exceptions\PartnerNotExists;
use App\Exceptions\UserNotExists;
use App\Repositories\RepositoryInterface;

class UserLogin
{

    private $userRepo;

    public function __construct(RepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function execute(array $data)
    {
        try {
            return $this->userRepo->getOneByUserId($data['user_id']);
        } catch (UserNotExists $e) {
            throw new LoginFail();
        }
    }

}