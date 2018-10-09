<?php

namespace Viviniko\Configuration\Repositories\Configable;

use Illuminate\Support\Facades\Config;
use Viviniko\Repository\EloquentRepository;

class EloquentConfigable extends EloquentRepository implements ConfigableRepository
{
    public function __construct()
    {
        parent::__construct(Config::get('configuration.configable'));
    }

    /**
     * @param $where
     * @return mixed
     */
    public function deleteBy($where)
    {
        return $this->createQuery()->where($where)->delete();
    }
}