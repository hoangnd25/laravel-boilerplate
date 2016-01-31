<?php

namespace App\Providers;

use App\Http\Api;
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('api', function($app){
            return new Api();
        });
    }
}