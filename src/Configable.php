<?php

namespace Viviniko\Configuration;

use Viviniko\Configuration\Enums\VariableType;
use Illuminate\Support\Facades\Config;

trait Configable
{
    protected static $configableMap = [];

    public static function configableMap(array $map = null, $merge = true)
    {
        $map = [(new static)->getTable() => $map];
        static::$configableMap = $merge ? array_merge(static::$configableMap, $map) : $map;
    }

    public function config($key, $default = null)
    {
        if (is_array($key)) {
            $map = static::$configableMap[$this->getTable()] ?? [];
            foreach ($key as $name => $value) {
                Configable::set($this, $name, $value, $map[$name]['type'] ?? VariableType::T_STRING);
            }

            return $this;
        }

        return Configable::value($this, $key, $default);
    }

    /**
     * Boot the configurable trait for a model.
     *
     * @return void
     */
    public static function bootConfigurable()
    {
        static::deleted(function ($model) {
            $model->configables()->delete();
        });
    }

    public function configables()
    {
        return $this->morphMany(Config::get('configuration.configable'), 'configable');
    }
}