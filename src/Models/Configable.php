<?php

namespace Viviniko\Configuration\Models;

class Configable extends Variable
{
    protected $tableConfigKey = 'configuration.configables_table';

    public $timestamps = false;

    protected $fillable = [
        'configable_type', 'configable_id', 'key', 'value', 'raw_value', 'type'
    ];

    /**
     * Get all of the owning configurable models.
     */
    public function Configable()
    {
        return $this->morphTo('configable');
    }
}