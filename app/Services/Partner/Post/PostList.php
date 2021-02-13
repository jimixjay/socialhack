<?php


namespace App\Services\Partner\Post;

use App\Repositories\RepositoryInterface;

class PostList
{

    private $postRepo;

    public function __construct(RepositoryInterface $postRepo)
    {
        $this->postRepo = $postRepo;
    }

    public function execute()
    {
        return $this->postRepo->all();
    }

}