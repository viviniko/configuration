<?php

namespace Viviniko\Configuration\Services\Configable;

use ArrayAccess;
use Countable;
use Illuminate\Database\Eloquent\Model;
use Viviniko\Configuration\Contracts\ConfigableService;
use Viviniko\Configuration\Enums\VariableType;

class Value implements ArrayAccess, Countable
{
    /**
     * @var ConfigableService
     */
    protected $configableService;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var null
     */
    protected $default;

    /**
     * @var array
     */
    protected $values = [];

    /**
     * Value constructor.
     * @param ConfigableService $configableService
     * @param Model $model
     * @param null $default
     */
    public function __construct(ConfigableService $configableService, Model $model, $default = null)
    {
        $this->configableService = $configableService;
        $this->model = $model;
        $this->default = $default;
    }

    public function __get($name)
    {
        return isset($this->values[$name]) ?
            $this->values[$name] :
            ($this->values[$name] = $this->configableService->value($this->model, $name, $this->default));
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return $this->configableService->has($offset);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->configableService->set($this->model, $offset, $value, VariableType::type($value));
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        $this->configableService->delete($this->model, $offset);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return $this->configableService->count($this->model);
    }
}