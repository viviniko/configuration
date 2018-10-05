<?php

namespace Viviniko\Configuration\Services\Configable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Viviniko\Configuration\Contracts\ConfigableService;
use Viviniko\Configuration\Enums\VariableType;
use Viviniko\Configuration\Repositories\Configable\ConfigableRepository;

class ConfigableServiceImpl implements ConfigableService
{
    /**
     * @var \Viviniko\Configuration\Repositories\Configable\ConfigableRepository
     */
    protected $configables;

    /**
     * ConfigableServiceImpl constructor.
     * @param \Viviniko\Configuration\Repositories\Configable\ConfigableRepository
     */
    public function __construct(ConfigableRepository $configables)
    {
        $this->configables = $configables;
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
     * Get configable(s).
     *
     * @param $model
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function get($model, $key = null, $default = null)
    {
        $where = $model instanceof Model ? [
            'configable_type' => $model->getMorphClass(),
            'configable_id' => $model->id,
        ] : $model;

        $configables = Cache::remember("configuration.configables?:{$where['configable_type']},{$where['configable_id']}", Config::get('cache.ttl', 10), function () use ($where) {
            return $this->configables->findAllBy([
                'configable_type' => $where['configable_type'],
                'configable_id' => $where['configable_id'],
            ]);
        });

        if ($key && Str::contains($key, '->')) {
            $arr = explode('->', $key);
            $key = array_shift($arr);
            array_unshift($arr, 'value');
            return data_get($configables->where('key', $key)->first(), implode('.', $arr), $default);
        }
        
        return $key ? $configables->where('key', $key)->first() : Collection::fromVariables($configables);
    }

    /**
     * Set configable.
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

            if ($configable = $this->get($model, $key)) {
                $this->configables->update($configable->id, $data);
            } else {
                $data['key'] = $key;
                $data = array_merge($data, $model instanceof Model ? [
                    'configable_type' => $model->getMorphClass(),
                    'configable_id' => $model->id,
                ] : $model);

                $this->configables->create($data);
            }

            $this->flushCache($model);
        } else {
            $this->delete($model, $key);
        }

        return $this;
    }

    /**
     * Delete configable.
     *
     * @param $model
     * @param null $key
     */
    public function delete($model, $key = null)
    {
        $where = $model instanceof Model ? [
            'configable_type' => $model->getMorphClass(),
            'configable_id' => $model->id,
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

        $this->configables->deleteBy($where);

        $this->flushCache($model);
    }

    /**
     * Get configable value.
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
        return $this->configables->count($model instanceof Model ? [
            'configable_type' => $model->getMorphClass(),
            'configable_id' => $model->id,
        ] : $model);
    }

    protected function flushCache($model)
    {
        $where = $model instanceof Model ? [
            'configable_type' => $model->getMorphClass(),
            'configable_id' => $model->id,
        ] : $model;

        Cache::forget("configuration.configables?:{$where['configable_type']},{$where['configable_id']}");
    }
}