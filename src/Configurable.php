<?php

namespace Viviniko\Configuration;

use Viviniko\Configuration\Enums\VariableType;
use Viviniko\Configuration\Facades\Variable;
use Illuminate\Support\Facades\Config;

trait Configurable
{
    protected static $variableMap = [];

    public static function variableMap(array $map = null, $merge = true)
    {
        $map = [(new static)->getTable() => $map];
        static::$variableMap = $merge ? array_merge(static::$variableMap, $map) : $map;
    }

    public function setVariables(array $variables = [])
    {
        if (!empty($variables)) {
            $map = static::$variableMap[$this->getTable()] ?? [];
            foreach ($variables as $name => $value) {
                Variable::set($this, $name, $value, $map[$name]['type'] ?? VariableType::T_STRING);
            }
        }
    }

    public function getVariables()
    {
        return Variable::value($this);
    }

    public function variable($key)
    {
        return Variable::value($this, $key);
    }

    /**
     * Boot the configurable trait for a model.
     *
     * @return void
     */
    public static function bootConfigurable()
    {
        static::deleted(function ($model) {
            $model->variables()->delete();
        });
    }

    public function variables()
    {
        return $this->morphMany(Config::get('configuration.variable'), 'variable');
    }
}