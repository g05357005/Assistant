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

    public function progress($body)
    {
        $earthquakeService = new EarthquakeService();
        $users = $earthquakeService->getRegisteredUser();

        $message = $body->message->text;

        $this->pushAlert($users, $message);
    }

    private function pushAlert($users, $message)
    {
        $message = new LINEBot\MessageBuilder\TextMessageBuilder($message);

        foreach ($users as $user) {
            $res = $this->bot->pushMessage($user->mid, $message);

            if (!$res->isSucceeded()) {
                Log::warning($res->getHTTPStatus() . ' ' . $res->getRawBody());
            }
        }
    }
}