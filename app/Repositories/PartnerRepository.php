<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class PartnerRepository implements RepositoryInterface
{
    public function all($columns = ['*'])
    {
        $query = '
            SELECT ' . implode(',', $columns) . '
            FROM partner
            WHERE deleted_at IS NULL    
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
            FROM partner 
            WHERE partner_id = ' . $id;

        $result = DB::selectOne($query);

        return !is_null($result);
    }
}