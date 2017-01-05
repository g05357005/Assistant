<?php

namespace App\ParserModule;

interface BaseModule
{
    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param $body
     * @return string
     */
    public function parse($body);
}