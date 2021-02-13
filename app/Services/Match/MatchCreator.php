<?php


namespace App\Services\Match;

use App\Exceptions\MatchAlreadyExists;
use App\Exceptions\MatchNotCreated;
use App\Repositories\RepositoryInterface;

class MatchCreator
{

    private $matchRepository;

    public function __construct(RepositoryInterface $matchRepository)
    {
        $this->matchRepository = $matchRepository;
    }

    public function execute(int $userId, int $partnerId)
    {
        try {
            $this->matchRepository->create(['user_id' => $userId, 'partner_id' => $partnerId]);
        } catch (MatchAlreadyExists $e) {
            throw new MatchAlreadyExists($e->getMessage());
        } catch (\Exception $e) {
            throw new MatchNotCreated($e->getMessage());
        }
    }

}