<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PropertyAccessor extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'property_accessor';
    }
}