<?php

namespace Viviniko\Configuration\Services\Variable;

use Illuminate\Support\Str;
use Viviniko\Configuration\Contracts\VariableService;
use Viviniko\Configuration\Enums\VariableType;
use Viviniko\Configuration\Repositories\Variable\VariableRepository;

class VariableServiceImpl implements VariableService
{
    /**
     * @var \Viviniko\Configuration\Repositories\Variable\VariableRepository
     */
    protected $variables;

    /**
     * VariableServiceImpl constructor.
     * @param \Viviniko\Configuration\Repositories\Variable\VariableRepository
     */
    public function __construct(VariableRepository $variables)
    {
        $this->variables = $variables;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return $this->variables->exists('key', $key);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        $value = 'value';
        if (Str::contains($key, '->')) {
            $arr = explode('->', $key);
            $key = array_shift($arr);
            array_unshift($arr, $value);
            $value = implode('.', $arr);
        }

        return data_get($this->variables->findByKey($key), $value, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $type = VariableType::T_STRING, array $options = [])
    {
        if (Str::contains($key, '->')) {
            $type = VariableType::T_JSON;
            $arr = explode('->', $key);
            $key = array_shift($arr);
            $oldValue = $this->get($key);
            $value = data_set($oldValue, implode('.', $arr), $value);
        }

        $data = array_merge($options, ['type' => $type, 'raw_value' => $value]);

        if ($variable = $this->variables->findByKey($key)) {
            $this->variables->update($variable->id, $data);
        } else {
            $data['key'] = $key;
            $this->variables->create($data);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return $this->variables->deleteByKey($key);
    }
}