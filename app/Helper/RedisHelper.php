<?php

namespace App\Helper;

use Predis\Client as RedisClient;

class RedisHelper
{

    private $redis;

    private $host;

    private $port;

    private $schema;

    private $password;

    public function __construct($options = [])
    {
        if (!empty($options)) {
            foreach ($options as $option => $value) {
                if (isset($this->{$option})) {
                    $this->{$option} = $value;
                }
            }

            return;
        }

        $this->host = env('REDIS_HOST');
        $this->port = env('REDIS_PORT');
        $this->password = env('REDIS_PASSWORD');
        $this->schema = 'tcp';
    }

    public function getRedis()
    {
        $this->redis = new RedisClient([
            'scheme' => $this->schema,
            'host' => $this->host,
            'port' => $this->port,
        ]);

        return $this->redis;
    }
}