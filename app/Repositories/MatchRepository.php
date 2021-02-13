<?php

namespace App\Repositories;

use App\Exceptions\MatchAlreadyExists;
use Illuminate\Support\Facades\DB;

class MatchRepository implements RepositoryInterface
{
    public function all($columns = ['*'])
    {
        $query = '
            SELECT ' . implode(',', $columns) . '
            FROM match
        ';

        return DB::select($query);
    }

    public function exists(array $data): bool
    {
        if (!count($data)) {
            return false;
        }

        $query = '
            SELECT *
            FROM match 
            WHERE 1=1 ';

        $where = [];
        foreach ($data as $field => $value) {
            $where[] = $field . ' = \'' . $value . '\'';
        }

        if (count($where) > 0) {
            $query .= 'AND ' . implode(' AND ', $where);
        }

        $result = DB::selectOne($query);

        return !is_null($result);
    }

    public function create(array $data)
    {
        if ($this->exists($data)) {
            throw new MatchAlreadyExists();
        }

        $query = '
            INSERT INTO match
        ';

        $fields = [];
        $values = [];
        foreach ($data as $field => $value) {
            $fields[] = '"' . $field . '"';
            $values[] = '\'' . $value . '\'';
        }

        $query .= '(' . implode(',', $fields) . ')';
        $query .= '
            VALUES
            ('. implode(',', $values) . ')';

        DB::insert($query);
    }
}