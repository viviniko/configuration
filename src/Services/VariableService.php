<?php

namespace Viviniko\Configuration\Services;

use Viviniko\Configuration\Enums\VariableType;

/**
 * Interface VariableService
 * @package Viviniko\Configuration\Contracts
 */
interface VariableService
{
    /**
     * Variable exists.
     *
     * @param $key
     * @return bool
     */
    public function has($key);

    /**
     * Get variable(s).
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Set variable.
     *
     * @param $key
     * @param $value
     * @param string $type
     * @param array $options
     * @return $this
     */
    public function set($key, $value, $type = VariableType::T_STRING, array $options = []);

    /**
     * Delete variable.
     *
     * @param $key
     */
    public function delete($key);
}