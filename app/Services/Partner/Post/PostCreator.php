<?php


namespace App\Services\Partner\Post;

use App\Exceptions\PartnerNotExists;
use App\Exceptions\Post\TitleCantBeEmpty;
use App\Repositories\RepositoryInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class PostCreator
{

    private $postRepo;

    public function __construct(RepositoryInterface $postRepo)
    {
        $this->postRepo = $postRepo;
    }

    public function execute(array $data)
    {
        if (!array_key_exists('partner_id', $data) || !$data['partner_id']) {
            throw new PartnerNotExists();
        }

        if (!array_key_exists('title', $data) || !$data['title']) {
            throw new TitleCantBeEmpty();
        }

        $slugger = new AsciiSlugger();
        $data['slug'] = $slugger->slug($data['title'] . '-' . bin2hex(random_bytes(16)));

        if (!array_key_exists('content', $data) || !$data['content']) {
            throw new ContentCantBeEmpty();
        }

        $data['content'] = str_replace("'", "`", $data['content']);

        $published = false;
        if (array_key_exists('published', $data) && $data['published']) {
            $published = true;
        }

        $data['published'] = $published;

        return $this->postRepo->create($data);
    }

}