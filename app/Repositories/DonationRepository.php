<?php

namespace App\Repositories;

use App\Exceptions\MatchAlreadyExists;
use App\Exceptions\MatchNotExists;
use Illuminate\Support\Facades\DB;

class DonationRepository extends Repository implements RepositoryInterface
{
    public function all($columns = ['*'])
    {
        $query = '
            SELECT ' . implode(',', $columns) . '
            FROM donation
        ';

        return DB::select($query);
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