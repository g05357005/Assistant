<?php

namespace App\Services;

class NormalService
{
    private $arrAgain = [
        '我聽不懂你說的',
        '我沒有這個指令喔',
        '恩?',
    ];

    public function __construct()
    {
    }

    public function echo($text)
    {
        return $text;
    }

    public function doNotGetIt()
    {
        $index = mt_rand(0, count($this->arrAgain) - 1);
        return $this->arrAgain[$index];
    }
}