<?php

namespace Viviniko\Configuration\Services\Variable;

use Viviniko\Configuration\Contracts\VariableService;
use Viviniko\Configuration\Enums\VariableType;
use Viviniko\Configuration\Repositories\Variable\VariableRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class VariableServiceImpl implements VariableService
{
    /**
     * @var \Viviniko\Configuration\Repositories\Variable\VariableRepository
     */
    protected $variables;

    /**
     * VariableServiceImpl constructor.
     * @param \Viviniko\Configuration\Repositories\Variable\VariableRepository $variables
     */
    public function __construct(VariableRepository $variables)
    {
        $this->variables = $variables;
    }

    /**
     * Variable exists.
     *
     * @param mixed $model
     * @param null $key
     * @return bool
     */
    public function has($model, $key = null)
    {
        if ($key && Str::contains($key, '->')) {
            $default = __CLASS__ . spl_object_hash($this). time();
            return $this->get($model, $key, $default) !== $default;
        }

        $result = $this->get($model, $key);
        return $result instanceof Collection ? $result->isNotEmpty() : !!$result;
    }

    /**
     * Get variable(s).
     *
     * @param $model
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function get($model, $key = null, $default = null)
    {
        $where = $model instanceof Model ? [
            'variable_type' => $model->getMorphClass(),
            'variable_id' => $model->id,
        ] : $model;

        $variables = Cache::remember("configuration.variables?:{$where['variable_type']},{$where['variable_id']}", Config::get('cache.ttl', 10), function () use ($where) {
            return $this->variables->findBy([
                'variable_type' => $where['variable_type'],
                'variable_id' => $where['variable_id'],
            ]);
        });

        if ($key && Str::contains($key, '->')) {
            $arr = explode('->', $key);
            $key = array_shift($arr);
            array_unshift($arr, 'value');
            return data_get($variables->where('key', $key)->first(), implode('.', $arr), $default);
        }
        
        return $key ? $variables->where('key', $key)->first() : Collection::fromVariables($variables);
    }

    /**
     * Set variable.
     *
     * @param $model
     * @param $key
     * @param $value
     * @param string $type
     * @param array $options
     * @return $this
     */
    public function set($model, $key, $value, $type = VariableType::T_STRING, array $options = [])
    {
        if (!is_null($value)) {
            if (Str::contains($key, '->')) {
                $type = VariableType::T_JSON;
                $arr = explode('->', $key);
                $key = array_shift($arr);
                $oldValue = $this->value($model, $key);
                $value = data_set($oldValue, implode('.', $arr), $value);
            }

            $data = array_merge($options, ['type' => $type, 'raw_value' => $value]);

            if ($variable = $this->get($model, $key)) {
                $this->variables->update($variable->id, $data);
            } else {
                $data['key'] = $key;
                $data = array_merge($data, $model instanceof Model ? [
                    'variable_type' => $model->getMorphClass(),
                    'variable_id' => $model->id,
                ] : $model);

                $this->variables->create($data);
            }

            $this->flushCache($model);
        } else {
            $this->delete($model, $key);
        }

        return $this;
    }

    /**
     * Delete variable.
     *
     * @param $model
     * @param null $key
     */
    public function delete($model, $key = null)
    {
        $where = $model instanceof Model ? [
            'variable_type' => $model->getMorphClass(),
            'variable_id' => $model->id,
        ] : $model;

        if ($key) {
            if (Str::contains($key, '->')) {
                $arr = explode('->', $key);
                $lastKey = array_pop($arr);
                $key = implode('->', $arr);
                $value = $this->value($model, $key);
                if (isset($value[$lastKey])) {
                    unset($value[$lastKey]);
                }
                $this->set($model, $key, $value, VariableType::T_JSON);
                return ;
            }
            $where['key'] = $key;
        }

        $this->variables->deleteBy($where);

        $this->flushCache($model);
    }

    /**
     * Get variable value.
     *
     * @param $model
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function value($model, $key = null, $default = null)
    {
        if ($key) {
            $value = 'value';
            if (Str::contains($key, '->')) {
                $arr = explode('->', $key);
                $key = array_shift($arr);
                array_unshift($arr, $value);
                $value = implode('.', $arr);
            }

            return data_get($this->get($model, $key), $value, $default);
        }

        return new Value($this, $model, $default);
    }

    /**
     * @param $model
     * @return mixed
     */
    public function count($model)
    {
        return $this->variables->count($model instanceof Model ? [
            'variable_type' => $model->getMorphClass(),
            'variable_id' => $model->id,
        ] : $model);
    }

    protected function flushCache($model)
    {
        $where = $model instanceof Model ? [
            'variable_type' => $model->getMorphClass(),
            'variable_id' => $model->id,
        ] : $model;

        Cache::forget("configuration.variables?:{$where['variable_type']},{$where['variable_id']}");
    }
}