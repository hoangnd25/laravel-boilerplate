<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;

/**
 * API endpoint for blog post
 * @package App\Http\Controllers
 */
class PostController extends ApiController
{
    /**
     * @inheritdoc
     */
    protected function getDefaultRepositoryName()
    {
        return 'App\Model\Post';
    }

    /**
     * @inheritdoc
     */
    public function getDefaultListSerializerGroup()
    {
        return array('post_list');
    }

    /**
     * @inheritdoc
     */
    public function getDefaultDetailsSerializerGroup()
    {
        return array('post_details');
    }
}