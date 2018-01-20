<?php

namespace Viviniko\Configuration\Services\Variable;

use Illuminate\Support\Collection as BaseCollection;

class Collection extends BaseCollection
{
    public static function fromVariables($variables)
    {
        $items = [];
        foreach ($variables as $variable) {
            $items[$variable->key] = $variable;
        }

        return new static($items);
    }
}