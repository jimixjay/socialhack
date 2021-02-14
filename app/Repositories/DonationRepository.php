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

    public function create(array $data, string $payload)
    {
        $query = '
            INSERT INTO donation
        ';

        $data['payload'] = $payload;

        $this->addInsertData($query, $data);

        DB::insert($query);
    }

}