<?php


namespace App\Services;

use App\Exceptions\MatchAlreadyExists;
use App\Exceptions\MatchNotCreated;
use App\Exceptions\PartnerNotExists;
use App\Exceptions\UserNotExists;
use App\Repositories\RepositoryInterface;

class MockCreator
{
    private $repo;

    public function __construct(RepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function execute($rows)
    {
        foreach ($rows as $row) {
            $this->repo->create($row);
        }
    }

}