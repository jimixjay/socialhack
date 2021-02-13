<?php

namespace App\Repositories;

interface RepositoryInterface
{
    public function all($columns = array('*'));
}
