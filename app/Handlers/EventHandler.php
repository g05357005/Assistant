<?php

namespace App\Handlers;

use Illuminate\Support\Facades\Log;
use LINE\LINEBot;
use Line\LINEBot\Event\MessageEvent\TextMessage;
use Line\LINEBot\Event\MessageEvent\StickerMessage;
use App\Services\WeatherService;
use App\Services\UserService;
use App\ParserModule\WeatherModule;

class EventHandler
{
    private $events = [];

    private $bot;

    public function __construct(LINEBot $bot, $body, $signature)
    {
        $this->bot    = $bot;
        $this->events = $this->bot->parseEventRequest($body, $signature);
    }

    public function progress()
    {
        foreach ($this->events as $event) {
            $resText = '';
            if ($event instanceof TextMessage) {
                if ($event->getText() === '註冊會員') {
                    $resText = $this->registerProgress($event);
                } else if ($event->getText() === '天氣' or $event->getText() === 'weather') {
                    $resText = $this->weatherProgress($event);
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
            if ($userService->register($profile['displayName'], $userService::SERVICES_WEATHER)) {
                return '已完成註冊';
            } else {
                return '註冊失敗，請重試';
            }
        }
    }

    private function weatherProgress(TextMessage $textMessage)
    {
        $weatherService = new WeatherService(new WeatherModule());

        return $weatherService->getInfo();
    }

    private function echoProgress(TextMessage $textMessage)
    {
        return $textMessage->getText();
    }

    private function stickerProgress(StickerMessage $stickerMessage)
    {
        $resText   = '>/////<';
        $packageId = $stickerMessage->getPackageId();
        $stickerId = $stickerMessage->getStickerId();

        return $resText;
    }
}