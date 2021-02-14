<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\DonationRepository;
use App\Repositories\MatchRepository;
use App\Repositories\PartnerRepository;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;
use App\Services\MockCreator;
use Faker\Factory;
use Faker\Generator;
use Faker\Guesser\Name;
use Faker\Provider\es_ES\Person;
use Faker\Provider\Image;
use Faker\Provider\Internet;
use Faker\UniqueGenerator;
use Symfony\Component\String\Slugger\AsciiSlugger;

class MockController extends Controller
{
    public function __construct()
    {
    }

    public function execute(UserRepository $userRepo, PartnerRepository $partnerRepo, MatchRepository $matchRepo, DonationRepository $donationRepo, PostRepository $postRepo)
    {
        try {
            $mockCreator = new MockCreator($userRepo);
            $mockCreator->execute($this->generateDataForUser());

            return response()->json(['msg' => 'OK']);

        } catch (\Exception $e) {
            return $this->exceptionErrorResponse($e, 500, 'mock500', 'No se ha podido hacer la ingesta de datos mockeados');
        }
    }

    private function generateDataForUser()
    {
        $slugger = new AsciiSlugger();

        $rows = [];
        for ($i = 0; $i < 10; $i++) {
            $faker = Factory::create('es_ES');
            $data = [];
            $data['name'] = $faker->firstName . ' ' . $faker->lastName;
            $data['username'] = $faker->userName;
            $data['slug'] = $slugger->slug($data['username']);
            $data['password'] = password_hash($faker->password, PASSWORD_DEFAULT, [20]);
            $data['email'] = $faker->email;
            $data['avatar'] = $faker->imageUrl(200, 200, 'people');

            $rows[] = $data;
        }

        return $rows;
    }
}