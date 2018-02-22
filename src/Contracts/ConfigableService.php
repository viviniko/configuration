<?php

namespace Viviniko\Configuration\Contracts;

use Viviniko\Configuration\Enums\VariableType;

/**
 * Interface ConfigableService
 * @package Viviniko\Configuration\Contracts
 */
interface ConfigableService
{
    /**
     * Variable exists.
     *
     * @param mixed $model
     * @param null $key
     * @return bool
     */
    public function has($model, $key = null);

    /**
     * Get variable(s).
     *
     * @param mixed $model
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function get($model, $key = null, $default = null);

    /**
     * Set variable.
     *
     * @param mixed $model
     * @param $key
     * @param $value
     * @param string $type
     * @param array $options
     * @return $this
     */
    public function set($model, $key, $value, $type = VariableType::T_STRING, array $options = []);

    /**
     * Delete variable.
     *
     * @param $model
     * @param null $key
     */
    public function delete($model, $key = null);

    /**
     * @param $model
     * @return mixed
     */
    public function count($model);

    /**
     * Get variable value.
     *
     * @param mixed $model
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function value($model, $key, $default = null);
}