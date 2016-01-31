<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use JMS\Serializer\Construction\DoctrineObjectConstructor;
use JMS\Serializer\Construction\UnserializeObjectConstructor;
use JMS\Serializer\SerializerBuilder;

class SerializerServiceProvider extends ServiceProvider
{
    public function register()
    {
        \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');
        $this->app->singleton('serializer', function($app){
            return SerializerBuilder::create()
                ->setObjectConstructor(
                    new DoctrineObjectConstructor(
                        $app->make('registry'), new UnserializeObjectConstructor()
                    )
                )
                ->build();
        });
    }
}