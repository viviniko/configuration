<?php

namespace Viviniko\Configuration;

use Illuminate\Support\Facades\Config;
use Viviniko\Configuration\Enums\VariableType;
use Viviniko\Configuration\Facades\Configable as ConfigableFacade;

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
                ConfigableFacade::set($this, $name, $value, $map[$name]['type'] ?? VariableType::type($value));
            }

            return $this;
        }

        return ConfigableFacade::value($this, $key, $default);
    }

    /**
     * Boot the configable trait for a model.
     *
     * @return void
     */
    public static function bootConfigable()
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