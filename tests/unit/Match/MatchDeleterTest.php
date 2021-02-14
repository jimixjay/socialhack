<?php

use App\Exceptions\MatchAlreadyExists;
use App\Exceptions\MatchNotExists;
use App\Exceptions\PartnerNotExists;
use App\Exceptions\UserNotExists;
use App\Repositories\RepositoryInterface;
use App\Services\Match\MatchDeleter;
use PHPUnit\Framework\TestCase;

class MatchDeleterTest extends TestCase
{
    public function testUserNotExits()
    {
        try {
            $matchRepo = new MockMatchDeleterMatchRepository();
            $userRepo = new MockMatchDeleterUserRepository();
            $partnerRepo = new MockMatchDeleterPartnerRepository();
            $matchCreator = new MatchDeleter($matchRepo, $userRepo, $partnerRepo);

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
            $matchRepo = new MockMatchDeleterMatchRepository();
            $userRepo = new MockMatchDeleterUserRepository();
            $partnerRepo = new MockMatchDeleterPartnerRepository();
            $matchCreator = new MatchDeleter($matchRepo, $userRepo, $partnerRepo);

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

    public function testMatchNotExists()
    {
        try {
            $matchRepo = new MockMatchDeleterMatchRepository();
            $userRepo = new MockMatchDeleterUserRepository();
            $partnerRepo = new MockMatchDeleterPartnerRepository();
            $matchCreator = new MatchDeleter($matchRepo, $userRepo, $partnerRepo);

            $userRepo->setExists(true);
            $partnerRepo->setExists(true);
            $matchRepo->setExists(false);
            $matchCreator->execute(1, 1);

            $this->fail();
        } catch (MatchNotExists $e) {
            $this->assertEquals('1', '1');
        } catch (Exception $e) {
            $this->fail();
        }
    }

    public function testMatchExists()
    {
        try {
            $matchRepo = new MockMatchDeleterMatchRepository();
            $userRepo = new MockMatchDeleterUserRepository();
            $partnerRepo = new MockMatchDeleterPartnerRepository();
            $matchCreator = new MatchDeleter($matchRepo, $userRepo, $partnerRepo);

            $userRepo->setExists(true);
            $partnerRepo->setExists(true);
            $matchRepo->setExists(true);
            $matchCreator->execute(1, 1);

            $this->assertEquals('1', '1');
        } catch (Exception $e) {
            $this->fail();
        }
    }
}

class MockMatchDeleterMatchRepository implements RepositoryInterface
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

    public function delete($data)
    {
        if (!$this->exists(1)) {
            throw new MatchNotExists();
        }
    }
}

class MockMatchDeleterUserRepository implements RepositoryInterface
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

class MockMatchDeleterPartnerRepository implements RepositoryInterface
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
