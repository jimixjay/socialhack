<?php

namespace App\Http\Controllers;

use App\Repositories\DonationRepository;
use App\Repositories\MatchRepository;
use App\Repositories\PartnerRepository;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;
use App\Services\MockCreator;
use Faker\Factory;
use Illuminate\Support\Facades\DB;
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

            $mockCreator = new MockCreator($partnerRepo);
            $mockCreator->execute($this->generateDataForPartner());

            $mockCreator = new MockCreator($matchRepo);
            $mockCreator->execute($this->generateDataForMatch());

            $mockCreator = new MockCreator($donationRepo);
            $mockCreator->execute($this->generateDataForDonations());

            $mockCreator = new MockCreator($postRepo);
            $mockCreator->execute($this->generateDataForPosts());

            return response()->json(['msg' => 'OK']);

        } catch (\Exception $e) {
            return $this->exceptionErrorResponse($e, 500, 'mock500', 'No se ha podido hacer la ingesta de datos mockeados');
        }
    }

    private function generateDataForUser()
    {
        $slugger = new AsciiSlugger();

        $rows = [];
        for ($i = 0; $i < 50; $i++) {
            $faker = Factory::create('es_ES');
            $data = [];
            $createdAt = $faker->dateTimeThisYear->format('Y-m-d H:i:s');
            $days = rand(0, 10);
            $updatedAt = $faker->dateTimeThisYear->add(new \DateInterval('P' . $days . 'D'))->format('Y-m-d H:i:s');
            $data['name'] = $faker->firstName . ' ' . $faker->lastName;
            $data['username'] = $faker->userName;
            $data['slug'] = $slugger->slug($data['username']);
            $data['password'] = password_hash($faker->password, PASSWORD_DEFAULT, [20]);
            $data['email'] = $faker->email;
            $data['avatar'] = $faker->imageUrl(200, 200, 'people');
            $data['created_at'] = $createdAt;
            $data['updated_at'] = $updatedAt;

            $rows[] = $data;
        }

        return $rows;
    }

    private function generateDataForPartner()
    {
        $slugger = new AsciiSlugger();

        $rows = [];
        for ($i = 0; $i < 20; $i++) {
            $faker = Factory::create('es_ES');
            $data = [];
            $createdAt = $faker->dateTimeThisYear->format('Y-m-d H:i:s');
            $days = rand(0, 10);
            $updatedAt = $faker->dateTimeThisYear->add(new \DateInterval('P' . $days . 'D'))->format('Y-m-d H:i:s');
            $data['name'] = $faker->company;
            $data['username'] = $faker->userName;
            $data['slug'] = $slugger->slug($data['username']);
            $data['password'] = password_hash($faker->password, PASSWORD_DEFAULT, [20]);
            $data['email'] = $faker->companyEmail;
            $data['logo'] = $faker->imageUrl(200, 200, 'people');
            $data['description'] = $faker->paragraph(2) . '<br><br>' . $faker->paragraph(5) . '<br><br>' . $faker->paragraph(3);
            $data['created_at'] = $createdAt;
            $data['updated_at'] = $updatedAt;

            $rows[] = $data;
        }

        return $rows;
    }

    private function generateDataForMatch()
    {
        $query = '
            SELECT user_id FROM "user"
        ';

        $users = DB::select($query);

        $rows = [];
        foreach ($users as $user) {
            $numMatches = rand(0, 20);
            $query = '
                SELECT partner_id
                FROM partner
                ORDER BY RANDOM() LIMIT ' . $numMatches;

            $partners = DB::select($query);

            foreach ($partners as $partner) {
                $faker = Factory::create('es_ES');
                $days = rand(0, 10);
                $rows[] = [
                    'user_id' => $user->user_id,
                    'partner_id' => $partner->partner_id,
                    'created_at' => $faker->dateTimeThisYear->format('Y-m-d H:i:s'),
                    'updated_at' => $faker->dateTimeThisYear->add(new \DateInterval('P' . $days . 'D'))->format('Y-m-d H:i:s')
                ];
            }
        }

        return $rows;
    }

    private function generateDataForDonations()
    {
        $query = '
            SELECT user_id FROM "user"
        ';

        $users = DB::select($query);

        $rows = [];
        foreach ($users as $user) {
            $numDonations = rand(0, 10);
            $query = '
                SELECT partner_id
                FROM partner
                ORDER BY RANDOM() LIMIT ' . $numDonations;

            $partners = DB::select($query);

            foreach ($partners as $partner) {
                $faker = Factory::create('es_ES');
                $amount = rand(10, 1000);
                $rows[] = [
                    'user_id' => $user->user_id,
                    'partner_id' => $partner->partner_id,
                    'amount' => $amount,
                    'is_public' => ($user->user_id + $partner->partner_id) % 2,
                    'is_public_amount' => ($user->user_id + $partner->partner_id * 3) % 2,
                    'payload' => 'payloadstring',
                    'created_at' => $faker->dateTimeThisYear->format('Y-m-d H:i:s'),
                    'updated_at' => $faker->dateTimeThisYear->format('Y-m-d H:i:s')
                ];
            }
        }

        return $rows;
    }

    private function generateDataForPosts()
    {
        $slugger = new AsciiSlugger();

        $query = '
            SELECT partner_id FROM "partner"
        ';

        $partners = DB::select($query);

        $rows = [];
        foreach ($partners as $partner) {
            $numPosts = rand(0, 10);
            for ($i = 0; $i < $numPosts; $i++) {
                $faker = Factory::create('es_ES');
                $numWords = rand(3, 15);
                $numWordsContent = rand(100, 200);
                $title = $faker->sentence($numWords);
                $slug = $slugger->slug($title);
                $rows[] = [
                    'title' => $title,
                    'slug' => $slug,
                    'partner_id' => $partner->partner_id,
                    'content' => $faker->text($numWordsContent),
                    'published' => true,
                    'published_at' => $faker->dateTimeThisYear->format('Y-m-d H:i:s'),
                    'created_at' => $faker->dateTimeThisYear->format('Y-m-d H:i:s'),
                    'updated_at' => $faker->dateTimeThisYear->format('Y-m-d H:i:s')
                ];
            }
        }

        return $rows;
    }
}