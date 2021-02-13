<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class UserRepository implements RepositoryInterface
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
}