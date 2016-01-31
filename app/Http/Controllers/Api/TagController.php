<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;

/**
 * API endpoint for blog tag
 * @package App\Http\Controllers
 */
class TagController extends ApiController
{
    /**
     * @inheritdoc
     */
    protected function getDefaultRepositoryName()
    {
        return 'App\Model\Tag';
    }

    /**
     * @inheritdoc
     */
    public function getDefaultListSerializerGroup()
    {
        return array('tag_list');
    }

    /**
     * @inheritdoc
     */
    public function getDefaultDetailsSerializerGroup()
    {
        return array('tag_details');
    }
}