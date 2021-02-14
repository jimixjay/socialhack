<?php


namespace App\Services\Partner;

use App\Repositories\RepositoryInterface;

class PartnerList
{

    private $partnerRepo;

    public function __construct(RepositoryInterface $partnerRepo)
    {
        $this->partnerRepo = $partnerRepo;
    }

    public function execute()
    {
        return $this->partnerRepo->all(['partner_id', 'slug', 'name', 'description', 'logo']);
    }

}