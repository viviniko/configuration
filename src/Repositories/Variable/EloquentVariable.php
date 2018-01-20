<?php

namespace Viviniko\Configuration\Repositories\Variable;

use Viviniko\Repository\SimpleRepository;

class EloquentVariable extends SimpleRepository implements VariableRepository
{
    protected $modelConfigKey = 'configuration.variable';

    /**
     * @param $where
     * @return mixed
     */
    public function deleteBy($where)
    {
        return $this->createModel()->where($where)->delete();
    }

    /**
     * @param $where
     * @return mixed
     */
    public function count($where)
    {
        return $this->createModel()->where($where)->count();
    }
}