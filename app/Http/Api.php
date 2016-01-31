<?php

namespace App\Http;

use App\Facades\Serializer;
use Illuminate\Http\Response;
use JMS\Serializer\SerializationContext;

class Api
{
    public function render($data, $serializerGroups = array())
    {
        $result = is_array($data) ? array('data' => array('items' => $data)) : array('data' => $data);
        $response = new Response();
        $context = SerializationContext::create()->enableMaxDepthChecks();
        if($serializerGroups){
            $context->setGroups($serializerGroups);
        }
        $content = Serializer::serialize($result, 'json', $context);
        $response->setContent($content);
        $response->header('Content-Type', 'application/json', true);

        return $response;
    }

    public function handle($request, $class){
        $content = $request->getContent();
        return $this->deserialize($content, $class);
    }

    protected function deserialize($content, $type){
        return Serializer::deserialize($content, $type, 'json');
    }
}