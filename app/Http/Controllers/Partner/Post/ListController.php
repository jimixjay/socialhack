<?php

namespace App\Http\Controllers\Partner\Post;


use App\Http\Controllers\Controller;
use App\Repositories\PostRepository;
use App\Services\Partner\Post\PostList;
use Illuminate\Http\Request;


class ListController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function execute(PostRepository $postRepo)
    {
        try {
            $postList = new PostList($postRepo);

            $posts = $postList->execute();

            return response()->json(['msg' => $posts]);

        } catch (\Exception $e) {
            return $this->exceptionErrorResponse($e, 500, 'ppl500', 'No se ha podido consultar el listado de posts');
        }
    }
}