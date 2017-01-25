<?php

namespace App\Http\Controllers;

use App\Handlers\BroadcastHandler;
use App\Handlers\EventHandler;
use App\Repositories\UserRepo;
use Illuminate\Http\Request;
use LINE\LINEBot\Constant\HTTPHeader;
use Predis\Client as RedisClient;
use GuzzleHttp\Client;
use App\Helper\AiHelper;
use App\ParserModule\WeatherModule;

class CenterController extends Controller
{
    public function __construct()
    {
    }

    public function center(Request $request)
    {
        $signature = $request->header(HTTPHeader::LINE_SIGNATURE);
        $body      = $request->getContent();
        $handler   = new EventHandler($body, $signature);

        $handler->progress();
    }

    public function push(Request $request)
    {
        $handler = new BroadcastHandler();

        $handler->progress();
    }

    public function test()
    {
//        $normalService = new \App\Services\NormalService();
//        echo $normalService->doNotGetIt();
//
//        exit();

//        $module = new WeatherModule(env('WEATHER_SERVICE_TOKEN'), '宜蘭');
//        $client = new Client();
//        $res = $client->get($module->getUrl());
//
//        echo $module->parse($res->getBody());
//        exit();

        /*$api = new AiHelper();
        $api->ask('我要註冊警報器');
        // $api->ask('hahaha');

        // echo '<pre>';
        // print_r(json_decode($api->getResponse()->getBody()));
        // echo '</pre>';

        // echo $api->getAction();
        echo $api->getParameter('service')

        exit();*/

//        $redisClient = new RedisClient([
//            'scheme' => 'tcp',
//            'host' => '127.0.0.1',
//            'port' => 6379,
//        ]);
//
//        $redisClient->connect();
//        // $redisClient->set('test', 'value');
//        // $redisClient->expire('test', 5);
//
//        if ($redisClient->isConnected()) {
//            echo $redisClient->get('test');
//        } else {
//            echo 'no';
//        }
//
//        exit();
    }
}
