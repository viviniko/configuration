<?php

namespace Viviniko\Configuration\Enums;

class VariableType
{
    const T_STRING = 'string';
    const T_INTEGER = 'integer';
    const T_DOUBLE = 'float';
    const T_ARRAY = 'array';
    const T_BOOL = 'boolean';
    const T_JSON = 'json';

    public static function values()
    {
        return [
            static::T_STRING      => 'String',
            static::T_INTEGER     => 'Integer',
            static::T_BOOL        => 'Boolean',
            static::T_ARRAY       => 'Array',
            static::T_DOUBLE      => 'Float',
            static::T_JSON        => 'Json',
        ];
    }

    public static function type($value)
    {
        $type = static::T_STRING;
        if (is_bool($value)) {
            $type = static::T_BOOL;
        } else if (is_integer($value)) {
            $type = static::T_INTEGER;
        } else if (is_array($value)) {
            $type = static::T_JSON;
        } else if (is_float($value)) {
            $type = static::T_DOUBLE;
        } else if (preg_match('/(\S+#\S+\s?){1,}/sm', $value)) {
            $type = static::T_ARRAY;
        }

        return $type;
    }
}