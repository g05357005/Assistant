<?php

namespace App\Handlers;

use Illuminate\Support\Facades\Log;
use App\Services\WeatherService;
use App\Services\UserService;
use App\ParserModule\WeatherModule;
use App\Helper\AiHelper;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use Line\LINEBot\Event\MessageEvent\TextMessage;
use Line\LINEBot\Event\MessageEvent\StickerMessage;

class EventHandler
{
    private $events = [];

    private $httpClient;

    private $bot;

    public function __construct($body, $signature)
    {
        $this->httpClient = new CurlHTTPClient(env('BOT_CHANNEL_ACCESS_TOKEN'));
        $this->bot        = new LINEBot($this->httpClient, ['channelSecret' => env('BOT_CHANNEL_SECRET')]);
        $this->events     = $this->bot->parseEventRequest($body, $signature);
    }

    public function progress()
    {
        $aiHelper = new AiHelper();

        foreach ($this->events as $event) {
            $resText = '';
            if ($event instanceof TextMessage) {
                $aiHelper->ask($event->getText());
                if ($aiHelper->getAction() === 'register' and $aiHelper->getParameter('service') === 'account') {
                    $resText = $this->registerProgress($event);
                } else if ($aiHelper->getAction() === 'get' and $aiHelper->getParameter('service') === 'weather') {
                    $locations = $aiHelper->getParameter('geo-city');
                    $resText = $this->weatherProgress($event, $locations[0]);
                } else {
                    $resText = $this->echoProgress($event);
                }
            }

            if ($event instanceof StickerMessage) {
                $resText = $this->stickerProgress($event);
            }

            $messageHandler = new MessageHandler($this->bot, $event->getReplyToken());
            $response = $messageHandler->replyText($resText);
            if (!$response->isSucceeded()) {
                // Logging
                Log::warning($response->getHTTPStatus() . ' ' . $response->getRawBody());
            }
        }
    }

    private function registerProgress(TextMessage $textMessage)
    {
        $userService = new UserService();
        $res = $this->bot->getProfile($textMessage->getUserId());
        if ($res->isSucceeded()) {
            $profile = $res->getJSONDecodedBody();
            if ($userService->register($profile['displayName'], $textMessage->getUserId(), $userService::SERVICES_WEATHER)) {
                return '已完成註冊';
            } else {
                return '註冊失敗，請重試';
            }
        }
    }

    private function weatherProgress(TextMessage $textMessage, $location)
    {
        $weatherModule  = new WeatherModule(env('WEATHER_SERVICE_TOKEN'), $location);
        $weatherService = new WeatherService($weatherModule);

        return $weatherService->getInfo();
    }

    private function echoProgress(TextMessage $textMessage)
    {
        $normalService = new NormalService();
        return $normalService->doNotGetIt();
    }

    private function stickerProgress(StickerMessage $stickerMessage)
    {
        $resText   = '我現在沒有貼圖QQ...';
        $packageId = $stickerMessage->getPackageId();
        $stickerId = $stickerMessage->getStickerId();

        return $resText;
    }
}