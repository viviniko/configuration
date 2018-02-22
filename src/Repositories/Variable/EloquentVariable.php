<?php

namespace Viviniko\Configuration\Repositories\Variable;

use Viviniko\Repository\SimpleRepository;

class EloquentVariable extends SimpleRepository implements VariableRepository
{
    protected $modelConfigKey = 'configuration.variable';

    /**
     * {@inheritdoc}
     */
    public function findByKey($key)
    {
        return $this->createModel()->where('key', $key)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByKey($key)
    {
        return $this->createModel()->where('key', $key)->delete();
    }
}