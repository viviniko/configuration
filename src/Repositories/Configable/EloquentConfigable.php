<?php

namespace Viviniko\Configuration\Repositories\Configable;

use Viviniko\Repository\SimpleRepository;

class EloquentConfigable extends SimpleRepository implements ConfigableRepository
{
    protected $modelConfigKey = 'configuration.configable';

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