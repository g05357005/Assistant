<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\ParserModule\BaseModule;

class WeatherService
{
    private $module;

    private $token;

    public function __construct(BaseModule $module)
    {
        $this->module = $module;
        $this->token = env('WEATHER_SERVICE_TOKEN');
    }

    public function getInfo()
    {
        $client  = new Client();
        $res     = $client->get($this->module->getUrl());
        
        if ($res->getStatusCode() === 200) {
            return $this->module->parse($res->getBody());
        }

        return false;
    }
}