<?php

use App\Exceptions\MatchAlreadyExists;
use App\Exceptions\PartnerNotExists;
use App\Exceptions\UserNotExists;
use App\Repositories\RepositoryInterface;
use App\Services\Match\MatchCreator;
use PHPUnit\Framework\TestCase;

class MatchCreatorTest extends TestCase
{
    public function testUserNotExits()
    {
        try {
            $matchRepo = new MockMatchCreatorMatchRepository();
            $userRepo = new MockMatchCreatorUserRepository();
            $partnerRepo = new MockMatchCreatorPartnerRepository();
            $matchCreator = new MatchCreator($matchRepo, $userRepo, $partnerRepo);

            $userRepo->setExists(false);
            $partnerRepo->setExists(true);
            $matchCreator->execute(1, 1);

            $this->fail();
        } catch (UserNotExists $e) {
            $this->assertEquals('1', '1');
        } catch (Exception $e) {
            $this->fail();
        }
    }

    public function testPartnerNotExits()
    {
        try {
            $matchRepo = new MockMatchCreatorMatchRepository();
            $userRepo = new MockMatchCreatorUserRepository();
            $partnerRepo = new MockMatchCreatorPartnerRepository();
            $matchCreator = new MatchCreator($matchRepo, $userRepo, $partnerRepo);

            $userRepo->setExists(true);
            $partnerRepo->setExists(false);
            $matchCreator->execute(1, 1);

            $this->fail();
        } catch (PartnerNotExists $e) {
            $this->assertEquals('1', '1');
        } catch (Exception $e) {
            $this->fail();
        }
    }

    public function testMatchAlreadyExists()
    {
        try {
            $matchRepo = new MockMatchCreatorMatchRepository();
            $userRepo = new MockMatchCreatorUserRepository();
            $partnerRepo = new MockMatchCreatorPartnerRepository();
            $matchCreator = new MatchCreator($matchRepo, $userRepo, $partnerRepo);

            $userRepo->setExists(true);
            $partnerRepo->setExists(true);
            $matchRepo->setExists(true);
            $matchCreator->execute(1, 1);

            $this->fail();
        } catch (MatchAlreadyExists $e) {
            $this->assertEquals('1', '1');
        } catch (Exception $e) {
            $this->fail();
        }
    }

    public function testMatchAlreadyExistsButDeleted()
    {
        try {
            $matchRepo = new MockMatchCreatorMatchRepository();
            $userRepo = new MockMatchCreatorUserRepository();
            $partnerRepo = new MockMatchCreatorPartnerRepository();
            $matchCreator = new MatchCreator($matchRepo, $userRepo, $partnerRepo);

            $userRepo->setExists(true);
            $partnerRepo->setExists(true);
            $matchRepo->setExists(false);
            $matchRepo->setExistsWithDeletedAt(true);
            $matchCreator->execute(1, 1);

            $this->assertEquals('1', '1');
        } catch (Exception $e) {
            $this->fail();
        }
    }

    public function testMatchNotExists()
    {
        try {
            $matchRepo = new MockMatchCreatorMatchRepository();
            $userRepo = new MockMatchCreatorUserRepository();
            $partnerRepo = new MockMatchCreatorPartnerRepository();
            $matchCreator = new MatchCreator($matchRepo, $userRepo, $partnerRepo);

            $userRepo->setExists(true);
            $partnerRepo->setExists(true);
            $matchRepo->setExists(false);
            $matchRepo->setExistsWithDeletedAt(false);
            $matchCreator->execute(1, 1);

            $this->assertEquals('1', '1');
        } catch (Exception $e) {
            $this->fail();
        }
    }
}

class MockMatchCreatorMatchRepository implements RepositoryInterface
{
    private $exists = false;
    private $existsWithDeletedAt = false;

    public function setExists($exists)
    {
        $this->exists = $exists;
    }

    public function setExistsWithDeletedAt($exists)
    {
        $this->existsWithDeletedAt = $exists;
    }

    public function all($columns = array('*'))
    {
    }

    public function create($data)
    {
        if ($this->exists($data)) {
            throw new MatchAlreadyExists();
        }

        if ($this->existsWithDeletedAt($data)) {
            $this->removeDeletedAt($data);
            return;
        }
    }

    public function exists($data)
    {
        return $this->exists;
    }

    public function existsWithDeletedAt($data)
    {
        return $this->existsWithDeletedAt;
    }

    public function removeDeletedAt($data)
    {
    }
}

class MockMatchCreatorUserRepository implements RepositoryInterface
{
    private $exists = false;

    public function setExists($exists)
    {
        $this->exists = $exists;
    }

    public function all($columns = array('*'))
    {
    }

    public function exists($data)
    {
        return $this->exists;
    }
}

class MockMatchCreatorPartnerRepository implements RepositoryInterface
{
    private $exists = false;

    public function setExists($exists)
    {
        $this->exists = $exists;
    }

    public function all($columns = array('*'))
    {
    }

    public function exists($data)
    {
        return $this->exists;
    }
}
