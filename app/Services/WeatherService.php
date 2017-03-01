<?php

namespace App\Services;

use App\Repositories\UserRepo;
use GuzzleHttp\Client;
use App\ParserModule\BaseModule;
use Predis\Client as RedisClient;

class WeatherService
{
    const REDIS_WEATHER_INFO_KEY = 'weatherInfo-%s';

    /**
     * module
     * @var BaseModule
     */
    private $module;

    /**
     * Redis cache ttl (3 hr)
     * @var int
     */
    private $ttl = 10800;

    public function __construct(BaseModule $module)
    {
        $this->module = $module;
    }

    public static function register($name, $userMid)
    {
        $userRepo = new UserRepo();
        return $userRepo->registerNewUser($name, $userMid, UserRepo::SERVICES_WEATHER);
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