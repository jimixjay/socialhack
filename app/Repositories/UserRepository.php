<?php

namespace App\Repositories;

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