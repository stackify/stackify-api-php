<?php

namespace Stackify\Utils;

class TypeConverter
{

    /**
     * Converts any PHP type to string
     * @param mixed $value
     * @return string
     */
    public static function stringify($value)
    {
        $string = '';
        if (is_scalar($value)) {
            // integer, float, string, boolean
            $string = (string)$value;
        } elseif (is_resource($value)) {
            // resource
            $string = '[resource]';
        } else {
            // array, object, null, callable
            $string = json_encode($value);
        }
        return $string;
    }

}