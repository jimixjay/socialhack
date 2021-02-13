<?php

namespace App\Repositories;

use App\Exceptions\MatchAlreadyExists;
use Illuminate\Support\Facades\DB;
use Symfony\Component\String\Slugger\AsciiSlugger;

class PostRepository extends Repository implements RepositoryInterface
{
    public function all($columns = ['*'])
    {
        $query = '
            SELECT ' . implode(',', $columns) . '
            FROM "post"
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
            FROM "post" 
            WHERE post_id = ' . $id;

        $result = DB::selectOne($query);

        return !is_null($result);
    }

    public function create(array $data)
    {
        $query = '
            INSERT INTO "post"
        ';

        if ($data['published']) {
            $data['published_at'] = $this->getNow();
        }

        $this->addInsertData($query, $data);

        $insert = DB::selectOne($query . ' RETURNING post_id');

        return $insert->post_id;
    }
}