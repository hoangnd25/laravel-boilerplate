<?php

namespace App\Http\Controllers\Http;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use LaravelDoctrine\ORM\Facades\EntityManager;

class PostController extends Controller
{
    /**
     * @inheritdoc
     */
    protected function getDefaultRepositoryName()
    {
        return 'App\Model\Post';
    }

    /**
     * Show all blog post
     * @return Response
     */
    public function index()
    {
        $posts = EntityManager::getRepository('App\Model\Post')->findAll();
        return view('postList', ['posts'=>$posts]);
    }
}