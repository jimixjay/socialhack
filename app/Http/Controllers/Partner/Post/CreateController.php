<?php

namespace App\Http\Controllers\Partner\Post;


use App\Exceptions\PartnerNotExists;
use App\Exceptions\Post\ContentCantBeEmpty;
use App\Exceptions\Post\TitleCantBeEmpty;
use App\Http\Controllers\Controller;
use App\Repositories\PostRepository;
use App\Services\Partner\Post\PostCreator;
use Illuminate\Http\Request;


class CreateController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function execute(PostRepository $postRepo)
    {
        try {
            $postCreator = new PostCreator($postRepo);

            $postId = $postCreator->execute($this->request->all());

            return response()->json(['post_id' => $postId]);

        } catch (ContentCantBeEmpty $e) {
            return $this->exceptionErrorResponse($e, 500, 'ppc503', 'El cuerpo del post es un campo obligatorio');
        } catch (TitleCantBeEmpty $e) {
            return $this->exceptionErrorResponse($e, 500, 'ppc502', 'El título es un campo obligatorio');
        } catch (PartnerNotExists $e) {
            return $this->exceptionErrorResponse($e, 500, 'ppc501', 'No existe el partner');
        } catch (\Exception $e) {
            return $this->exceptionErrorResponse($e, 500, 'ppc500', 'No se ha podido crear el post');
        }
    }
}