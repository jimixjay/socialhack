<?php

namespace App\Repositories;

use App\Exceptions\MatchAlreadyExists;
use App\Exceptions\UserNotExists;
use Illuminate\Support\Facades\DB;
use Symfony\Component\String\Slugger\AsciiSlugger;

class UserRepository extends Repository implements RepositoryInterface
{
    public function all($columns = ['*'])
    {
        $query = '
            SELECT ' . implode(',', $columns) . '
            FROM "user"
        ';

        return DB::select($query);
    }

    public function exists(?int $id): bool
    {
        if (!$id) {
            return false;
        }

        $query = '
            SELECT *
            FROM "user" 
            WHERE user_id = ' . $id;

        $result = DB::selectOne($query);

        return !is_null($result);
    }

    public function getOneByUserId(int $userId)
    {
        $query = '
            SELECT user_id, username, email, name, slug, avatar, 
            CONCAT(
                to_char(DATE_TRUNC(\'day\', created_at), \'DD\'), \'/\', 
                to_char(DATE_TRUNC(\'month\', created_at), \'MM\'), \'/\', 
                to_char(DATE_TRUNC(\'year\', created_at), \'YYYY\')) as created_at
            FROM "user"
            WHERE user_id = \'' . $userId . '\'';

        $user = DB::selectOne($query);

        if (!$user) {
            throw new UserNotExists();
        }

        $user->badges = $this->getBadges($userId);
        $user->donations = $this->getDonations($userId);
        $user->matches = $this->getMatches($userId);

        return $user;
    }

    public function getOneByUsername(string $username)
    {
        $query = '
            SELECT user_id, username, email, name, slug, avatar, 
            CONCAT(
                to_char(DATE_TRUNC(\'day\', created_at), \'DD\'), \'/\', 
                to_char(DATE_TRUNC(\'month\', created_at), \'MM\'), \'/\', 
                to_char(DATE_TRUNC(\'year\', created_at), \'YYYY\')) as created_at
            FROM "user"
            WHERE username = \'' . $username . '\'';

        $user = DB::selectOne($query);

        if (!$user) {
            throw new UserNotExists();
        }

        $user->badges = $this->getBadges($user->user_id);
        $user->donations = $this->getDonations($user->user_id);
        $user->matches = $this->getMatches($user->user_id);

        return $user;
    }

    public function getOneBySlug(string $slug)
    {
        $query = '
            SELECT user_id, username, email, name, slug, avatar, 
            CONCAT(
                to_char(DATE_TRUNC(\'day\', created_at), \'DD\'), \'/\', 
                to_char(DATE_TRUNC(\'month\', created_at), \'MM\'), \'/\', 
                to_char(DATE_TRUNC(\'year\', created_at), \'YYYY\')) as created_at
            FROM "user"
            WHERE slug = \'' . $slug . '\'';

        $user = DB::selectOne($query);

        if (!$user) {
            throw new UserNotExists();
        }

        $user->badges = $this->getBadges($user->user_id);
        $user->donations = $this->getDonations($user->user_id);
        $user->matches = $this->getMatches($user->user_id);

        return $user;
    }

    public function getBadges(int $userId): array
    {
        $query = '
            SELECT b.badge_id, b.src
            FROM badge b 
            INNER JOIN badge_user bu ON b.badge_id = bu.badge_id
            WHERE bu.user_id = ' . $userId . '
            AND b.active = true
            ORDER BY b.type ASC, b.code ASC';

        $badges = DB::select($query);

        if (!$badges) {
            return [];
        }

        return $badges;
    }

    public function getDonations(int $userId): array
    {
        $query = '
            SELECT d.amount, d.is_public, d.is_public_amount, d.created_at, 
            p.name as partner_name, p.partner_id, p.slug as partner_slug
            FROM donation d
            INNER JOIN partner p ON d.partner_id = p.partner_id
            WHERE d.user_id = ' . $userId;

        $donations = DB::select($query);

        if (!$donations) {
            return [];
        }

        return $donations;
    }

    public function getMatches(int $userId): array
    {
        $query = '
            SELECT m.updated_at as match_date_at, 
            p.name as partner_name, p.partner_id, p.slug as partner_slug
            FROM match m
            INNER JOIN partner p ON m.partner_id = p.partner_id
            WHERE m.user_id = ' . $userId . '
            AND m.deleted_at IS NULL
            AND p.deleted_at IS NULL';

        $matches = DB::select($query);

        if (!$matches) {
            return [];
        }

        return $matches;
    }

    public function getOneByClientId(string $clientId)
    {
        $query = '
            SELECT user_id, username, email, name, avatar
            FROM "user"
            WHERE client_id = \'' . $clientId . '\'';

        return DB::selectOne($query);
    }

    public function existsByClientId(?string $clientId): bool
    {
        if (!$clientId) {
            return false;
        }

        $query = '
            SELECT *
            FROM "user" 
            WHERE client_id = \'' . $clientId . '\'';

        $result = DB::selectOne($query);

        return !is_null($result);
    }

    public function createFromGoogle($userInfo, string $clientId)
    {
        $username = explode('@', $userInfo->email)[0];

        $slugger = new AsciiSlugger();
        $slug = $slugger->slug($username);

        $query = '
            INSERT INTO "user"
            ("email", "name", "avatar", "slug", "username", "password", "client_id", "created_at", "updated_at")
            VALUES
            (
                \'' . $userInfo->email . '\',
                \'' . $userInfo->name . '\',
                \'' . $userInfo->picture . '\',
                \'' . $slug . '\',
                \'' . $username . '\',
                \'\',
                \'' . $clientId . '\',
                \'' . $this->getNow() . '\',
                \'' . $this->getNow() . '\'
            )
        ';

        DB::insert($query);
    }

    public function create($data)
    {
        $query = '
            INSERT INTO "user"
        ';

        $this->addInsertData($query, $data);

        DB::insert($query);
    }
}