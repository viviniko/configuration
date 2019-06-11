<?php

namespace Viviniko\Configuration\Repositories\Variable;

use Illuminate\Support\Facades\Config;
use Viviniko\Repository\EloquentRepository;

class EloquentVariable extends EloquentRepository implements VariableRepository
{
    public function __construct()
    {
        parent::__construct(Config::get('configuration.variable'));
    }
}