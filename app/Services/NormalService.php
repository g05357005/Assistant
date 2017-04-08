<?php

namespace App\Services;

class NormalService
{
    private static $arrAgain = [
        '好喔',
        '好的',
        'OK',
        '恩',
    ];

    public function __construct()
    {
    }

    public function reply($text)
    {
        return $text;
    }

    public function doNotGetIt()
    {
        $index = random_int(0, count(self::$arrAgain) - 1);
        return self::$arrAgain[$index];
    }
}
