<?php

namespace App\Http\Controllers;

use App\Facades\Api;
use Illuminate\Http\Response;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Symfony\Component\HttpFoundation\Request;

abstract class ApiController extends Controller
{
    public function getDefaultListSerializerGroup(){
        return array();
    }

    public function getDefaultDetailsSerializerGroup(){
        return array();
    }

    /**
     * GET: Return all objects
     * @return Response
     */
    public function getAll()
    {
        $objects = $this->getRepository()->findAll();
        return Api::render($objects, $this->getDefaultListSerializerGroup());
    }

    /**
     * GET: Return single object by id
     * @return Response
     */
    public function getById($id)
    {
        $object = $this->getRepository()->find($id);
        return Api::render($object, $this->getDefaultDetailsSerializerGroup());
    }

    /**
     * DELETE: Remove single object post by id
     * @param $id
     * @return Response
     */
    public function removeById($id)
    {
        $object = $this->getRepository()->find($id);
        EntityManager::remove($object);
        EntityManager::flush();
        return Api::render($object, $this->getDefaultDetailsSerializerGroup());
    }

    /**
     * POST: Create new object if no id is presented, otherwise update the object with given id
     * @param Request $request
     * @return Response
     */
    protected function createOrUpdate(Request $request){
        $object = Api::handle($request, $this->getDefaultRepositoryName());
        EntityManager::persist($object);
        EntityManager::flush();
        return Api::render($object, $this->getDefaultDetailsSerializerGroup());
    }
}