<?php

namespace App\Handlers;

use App\Services\EarthquakeService;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

class BroadcastHandler
{

    private $httpClient;

    private $bot;

    public function __construct()
    {
        $this->httpClient = new CurlHTTPClient(env('BOT_CHANNEL_ACCESS_TOKEN'));
        $this->bot        = new LINEBot($this->httpClient, ['channelSecret' => env('BOT_CHANNEL_SECRET')]);
    }

    public function progress()
    {
        $earthquakeService = new EarthquakeService();
        $users = $earthquakeService->getRegisteredUser();

        $this->pushAlert($users);
    }

    private function pushAlert($users)
    {
        $message = new LINEBot\MessageBuilder\TextMessageBuilder('hello~');

        foreach ($users as $user) {
            $res = $this->bot->pushMessage($user->mid, $message);

            echo '<pre>';
            print_r($res);
            echo '</pre>';
        }
    }
}