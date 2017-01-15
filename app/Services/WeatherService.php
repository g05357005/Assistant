<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\ParserModule\BaseModule;
use Predis\Client as RedisClient;

class WeatherService
{
    const REDIS_WEATHER_INFO_KEY = 'weatherInfo-%s';

    private $module;

    private $ttl = 7200;

    public function __construct(BaseModule $module)
    {
        $this->module = $module;
    }

    public function getInfo()
    {
        $client      = new Client();
        $redisClient = new RedisClient([
            'scheme' => 'tcp',
            'host' => '127.0.0.1',
            'port' => 6379,
        ]);

        $res = $client->get($this->module->getUrl());

        $key = sprintf(self::REDIS_WEATHER_INFO_KEY, $this->module->getKey());
        $redisClient->connect();
        if ($redisClient->exists($key)) {
            return $redisClient->get($key);
        }
        
        if ($res->getStatusCode() === 200) {
            $result = $this->module->parse($res->getBody());

            $redisClient->set($key, $result);
            $redisClient->expire($key, $this->ttl);

            return $result;
        }

        return false;
    }
}