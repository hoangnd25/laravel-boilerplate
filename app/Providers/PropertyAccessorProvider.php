<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\PropertyAccess\PropertyAccess;

class PropertyAccessorProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('property_accessor', function($app){
            return PropertyAccess::createPropertyAccessor();
        });
    }
}