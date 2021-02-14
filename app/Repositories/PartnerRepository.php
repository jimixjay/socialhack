<?php

namespace App\Repositories;

use App\Exceptions\PartnerNotExists;
use App\Exceptions\UserNotExists;
use Illuminate\Support\Facades\DB;
use Symfony\Component\String\Slugger\AsciiSlugger;

class PartnerRepository extends Repository implements RepositoryInterface
{
    public function all($columns = ['*'])
    {
        $query = '
            SELECT ' . implode(',', $columns) . '
            FROM "partner"
        ';

        $partners = DB::select($query);

        foreach ($partners as $partnerIndex => $partner) {
            $partners[$partnerIndex]->budgets = $this->getBudgets($partner->partner_id);
        }

        return $partners;
    }

    public function getBudgets(int $partnerId): array
    {
        $query = '
            SELECT b.budget_id, b.src
            FROM budget b 
            INNER JOIN budget_partner bp ON b.budget_id = bp.budget_id
            WHERE bp.partner_id = ' . $partnerId . '
            AND b.active = true';

        $budgets = DB::select($query);

        if (!$budgets) {
            return [];
        }

        return $budgets;
    }

    public function exists(?int $id): bool
    {
        if (!$id) {
            return false;
        }

        $query = '
            SELECT *
            FROM "partner" 
            WHERE partner_id = ' . $id;

        $result = DB::selectOne($query);

        return !is_null($result);
    }

    public function getOneByPartnerId(int $partnerId)
    {
        $query = '
            SELECT partner_id, slug, name, description, logo, stripe_account_id
            FROM "partner"
            WHERE partner_id = \'' . $partnerId . '\'';

        $partner = DB::selectOne($query);

        if (!$partner) {
            throw new PartnerNotExists();
        }

        $partner->budgets = $this->getBudgets($partnerId);

        return $partner;
    }

    public function getOneByClientId(string $clientId)
    {
        $query = '
            SELECT partner_id, username, email, name, logo
            FROM "partner"
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
            FROM "partner" 
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
            INSERT INTO "partner"
            ("email", "name", "logo", "slug", "username", "password", "client_id", "created_at", "updated_at")
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