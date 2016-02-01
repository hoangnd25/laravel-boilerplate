<?php

namespace App\Http\Controllers\Http;

use App\Http\Controllers\Controller;
use App\Model\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Validator\Constraints\NotBlank;

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
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $posts = $this->getRepository()->findAll();
        return view('postList', ['posts'=>$posts]);
    }

    /**
     * Create new post if the id is given otherwise update it
     *
     * @param Request $request
     * @param integer $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createOrEdit(Request $request, $id = null)
    {
        $post = $id ? $this->getRepository()->find($id) : new Post();
        $form = $this->getForm($post, $id != null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $obj = $form->getData();
            EntityManager::persist($obj);
            EntityManager::flush();
            return Redirect::route('post.list');
        }

        return view('postDetails', ['form'=>$form->createView()]);
    }

    /**
     * Remove post with given id
     *
     * @param $id
     * @return mixed
     */
    public function remove($id){
        $post = $this->getRepository()->find($id);
        EntityManager::remove($post);
        EntityManager::flush();
        return Redirect::route('post.list');
    }

    /**
     * Convenient method for creating post form
     *
     * @param Post $post
     * @param bool $isEditing
     * @return \Symfony\Component\Form\Form
     */
    protected function getForm(Post $post, $isEditing = false){
        return $this->createFormBuilder($post)
            ->add('title')
            ->add('body')
            ->add('tags')
            ->add('submit', SubmitType::class, array(
                'label' => $isEditing ? "Save" : "Create"
            ))
            ->getForm();
    }
}