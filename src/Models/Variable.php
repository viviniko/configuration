<?php

namespace Viviniko\Configuration\Models;

use Viviniko\Configuration\Enums\VariableType;
use Viviniko\Support\Database\Eloquent\Model;

class Variable extends Model
{
    protected $tableConfigKey = 'configuration.variables_table';

    public $timestamps = false;

    protected $fillable = [
        'variable_type', 'variable_id', 'section', 'group', 'title', 'key', 'value', 'raw_value', 'type'
    ];

    public function setRawValueAttribute($value)
    {
        $this->attributes['raw_value'] = $value;
        $this->attributes['value'] = $value;
        $this->transformValue();
    }

    public function setTypeAttribute($type)
    {
        $this->attributes['type'] = $type;
        $this->casts['value'] = $type;
        $this->transformValue();
    }

    public function getValueAttribute($value)
    {
        $this->casts['value'] = $this->type;
        return $this->castAttribute('value', $value);
    }

    /**
     * Get all of the owning configurable models.
     */
    public function Configurable()
    {
        return $this->morphTo('variable');
    }

    private function transformValue()
    {
        if (!empty($this->attributes['raw_value']) && !empty($this->attributes['type'])) {
            switch ($this->attributes['type']) {
                case VariableType::T_ARRAY:
                    $value = [];
                    foreach (explode("\r", $this->attributes['raw_value']) as $item) {
                        list($k, $v) = explode('#', $item, 2);
                        $value[trim($k)] = trim($v);
                    }
                    $this->value = $value;
                    break;

                case VariableType::T_JSON:
                    $this->value = $this->attributes['raw_value'];
                    if (!empty($this->attributes['raw_value'])) {
                        if (is_array($this->attributes['raw_value'])) {
                            $this->attributes['raw_value'] = $this->castAttributeAsJson('raw_value', $this->attributes['raw_value']);
                        } else {
                            $this->value = $this->fromJson($this->attributes['raw_value']);
                        }
                    }
                    break;

                case VariableType::T_BOOL:
                    $value = $this->attributes['raw_value'];
                    if (preg_match('/no?/i', $value)) {
                        $value = false;
                    } else {
                        $value = true;
                    }
                    $this->value = $value;
                    break;

                case VariableType::T_INTEGER:
                    $this->value = intval($this->attributes['raw_value']);
                    break;
            }
        }
    }
}