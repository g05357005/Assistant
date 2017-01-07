<?php

namespace App\Helper;

class FlagHelper
{
    /**
     * @var int $base
     */
    private $base;

    /**
     * @param $base int base value for flag
     * @return void
     */
    public function __construct($base)
    {
        $this->base = $base;
    }

    public function getValue()
    {
        return $this->base;
    }

    /**
     * Get flag in binary string
     * @return string
     */
    public function getBinaryStr()
    {
        return decbin($this->base);
    }

    public function isOn($value)
    {
        return $this->base & $value;
    }

    public function isOff($value)
    {
        return !$this->isOn($value);
    }

    public function setOn($value)
    {
        $this->base = $this->base | $value;
    }

    public function setOff($value)
    {
        $this->base = $this->base & (~$value);
    }
}