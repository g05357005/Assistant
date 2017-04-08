<?php

namespace App\Handlers;

use App\Services\EarthquakeService;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

class BroadcastHandler
{

    /**
     * Line bot sdk
     * @var LINEBot
     */
    private $bot;

    private $body;

    public function __construct($body)
    {
        $httpClient = new CurlHTTPClient(env('BOT_CHANNEL_ACCESS_TOKEN'));
        $this->bot = new LINEBot($httpClient, ['channelSecret' => env('BOT_CHANNEL_SECRET')]);
        $this->body = $body;
    }

    /**
     * Do broadcast task
     */
    public function progress()
    {
        $earthquakeService = new EarthquakeService();
        $users = $earthquakeService->getRegisteredUser();
        $message = $this->getMessage();
        $this->pushAlert($users, $message);
    }

    private function getMessage()
    {
        return $this->body->message->text;
    }

    /**
     * @param $users
     * @param $message
     */
    private function pushAlert(array $users, $message)
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
