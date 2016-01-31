<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use LaravelDoctrine\ORM\Facades\EntityManager;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Return name for default repository
     * @return string
     */
    protected function getDefaultRepositoryName(){
        return '';
    }

    /**
     * Convenient method for returning object repository
     * @param string $name
     */
    protected function getRepository($name = null){
        if($name === null)
            $name = $this->getDefaultRepositoryName();

        return EntityManager::getRepository($name);
    }
}