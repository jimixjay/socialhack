<?php

namespace App\Repositories;

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
            SELECT user_id, username, email, name, avatar
            FROM "user"
            WHERE user_id = \'' . $userId . '\'';

        $user = DB::selectOne($query);

        if (!$user) {
            throw new UserNotExists();
        }

        $user->budgets = $this->getBudgets($userId);
        $user->donations = $this->getDonations($userId);

        return $user;
    }

    public function getBudgets(int $userId): array
    {
        $query = '
            SELECT b.budget_id, b.src
            FROM budget b 
            INNER JOIN budget_user bu ON b.budget_id = bu.budget_id
            WHERE bu.user_id = ' . $userId . '
            AND b.active = true';

        $budgets = DB::select($query);

        if (!$budgets) {
            return [];
        }

        return $budgets;
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
}