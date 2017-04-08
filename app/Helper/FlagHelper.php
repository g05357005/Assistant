<?php

namespace App\Helper;

class FlagHelper
{

    /**
     * Get flag in binary string
     * @param $baseValue
     * @return string
     */
    public static function getBinaryStr($baseValue)
    {
        return decbin($baseValue);
    }

    public static function isOn($value, $base)
    {
        return $base & $value;
    }

    public static function isOff($value, $base)
    {
        return !self::isOn($value, $base);
    }

    public static function setOn($value, $base)
    {
        return $base | $value;
    }

    public static function setOff($value, $base)
    {
        return $base & (~$value);
    }
}
