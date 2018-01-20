<?php

namespace Viviniko\Configuration\Facades;

use Illuminate\Support\Facades\Facade;

class Variable extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Viviniko\Configuration\Contracts\VariableService::class;
    }

}