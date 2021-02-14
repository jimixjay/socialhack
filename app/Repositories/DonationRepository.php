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
        $query = '
            INSERT INTO donation
        ';

        $this->addInsertData($query, $data);

        DB::insert($query);
    }

}