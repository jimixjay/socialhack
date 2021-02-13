<?php

namespace App\Repositories;

use App\Exceptions\MatchAlreadyExists;
use App\Exceptions\MatchNotExists;
use Illuminate\Support\Facades\DB;

class MatchRepository extends Repository implements RepositoryInterface
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
            WHERE deleted_at IS NULL AND ';

        $this->addWhereFromData($query, $data);

        $result = DB::selectOne($query);

        return !is_null($result);
    }

    public function existsWithDeletedAt(array $data): bool
    {
        if (!count($data)) {
            return false;
        }

        $query = '
            SELECT *
            FROM match 
            WHERE deleted_at IS NOT NULL AND ';

        $this->addWhereFromData($query, $data);

        $result = DB::selectOne($query);

        return !is_null($result);
    }

    public function create(array $data)
    {
        if ($this->exists($data)) {
            throw new MatchAlreadyExists();
        }

        if ($this->existsWithDeletedAt($data)) {
            $this->removeDeletedAt($data);
            return;
        }

        $query = '
            INSERT INTO match
        ';

        $this->addInsertData($query, $data);

        DB::insert($query);
    }

    private function removeDeletedAt(array $data)
    {
        $query = '
            UPDATE match
            SET deleted_at = NULL, updated_at = \'' . $this->getNow() . '\'
            WHERE 
        ';

        $this->addWhereFromData($query, $data);

        DB::update($query);
    }

    public function delete(array $data)
    {
        if (!$this->exists($data)) {
            throw new MatchNotExists();
        }

        $query = '
            UPDATE match
            SET deleted_at = \'' . $this->getNow() . '\'
            WHERE 
        ';

        $this->addWhereFromData($query, $data);

        DB::update($query);
    }
}