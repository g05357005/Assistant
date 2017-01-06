<?php

namespace App\Http\Controllers;

use App\Handlers\EventHandler;
use Illuminate\Http\Request;
use LINE\LINEBot\Constant\HTTPHeader;
use Predis\Client as RedisClient;

class CenterController extends Controller
{
    public function __construct()
    {
    }

    public function center(Request $request)
    {
        $signature = $request->header(HTTPHeader::LINE_SIGNATURE);
        $body      = file_get_contents('php://input');
        $handler   = new EventHandler($body, $signature);

        $handler->progress();
    }

    public function test()
    {
        $redisClient = new RedisClient([
            'scheme' => 'tcp',
            'host' => '127.0.0.1',
            'port' => 6379,
        ]);

        $redisClient->connect();
        // $redisClient->set('test', 'value');
        // $redisClient->expire('test', 5);

        if ($redisClient->isConnected()) {
            echo $redisClient->get('test');
        } else {
            echo 'no';
        }

        exit();
    }
}
