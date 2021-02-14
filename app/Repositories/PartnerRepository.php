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
            $partners[$partnerIndex]->badges = $this->getBadges($partner->partner_id);
        }

        return $partners;
    }

    public function getBadges(int $partnerId): array
    {
        $query = '
            SELECT b.badge_id, b.src
            FROM badge b 
            INNER JOIN badge_partner bp ON b.badge_id = bp.badge_id
            WHERE bp.partner_id = ' . $partnerId . '
            AND b.active = true
            ORDER BY b.type ASC, b.code ASC';

        $badges = DB::select($query);

        if (!$badges) {
            return [];
        }

        return $badges;
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
            SELECT partner_id, slug, name, description, logo, stripe_account_id,
            CONCAT(
                to_char(DATE_TRUNC(\'day\', created_at), \'DD\'), \'/\', 
                to_char(DATE_TRUNC(\'month\', created_at), \'MM\'), \'/\', 
                to_char(DATE_TRUNC(\'year\', created_at), \'YYYY\')) as created_at
            FROM "partner"
            WHERE partner_id = \'' . $partnerId . '\'';

        $partner = DB::selectOne($query);

        if (!$partner) {
            throw new PartnerNotExists();
        }

        $partner->badges = $this->getBadges($partnerId);

        return $partner;
    }

    public function getOneBySlug(string $slug)
    {
        $query = '
            SELECT partner_id, slug, name, description, logo, stripe_account_id,
            CONCAT(
                to_char(DATE_TRUNC(\'day\', created_at), \'DD\'), \'/\', 
                to_char(DATE_TRUNC(\'month\', created_at), \'MM\'), \'/\', 
                to_char(DATE_TRUNC(\'year\', created_at), \'YYYY\')) as created_at
            FROM "partner"
            WHERE slug = \'' . $slug . '\'';

        $partner = DB::selectOne($query);

        if (!$partner) {
            throw new PartnerNotExists();
        }

        $partner->badges = $this->getBadges($partner->partner_id);

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

    public function create($data)
    {
        $query = '
            INSERT INTO "partner"
        ';

        $this->addInsertData($query, $data);

        DB::insert($query);
    }

    public function getLimitRandom($columns = ['*'], $limit = 10)
    {
        $query = '
            SELECT ' . implode(',', $columns) . '
            FROM "partner"
            ORDER BY RANDOM()
            LIMIT ' . $limit;

        $partners = DB::select($query);

        foreach ($partners as $partnerIndex => $partner) {
            $partners[$partnerIndex]->badges = $this->getBadges($partner->partner_id);
        }

        return $partners;
    }
}