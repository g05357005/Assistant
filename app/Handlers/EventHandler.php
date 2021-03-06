<?php

namespace App\Handlers;

use App\Helper\MessageHelper;
use App\Services\WeatherService;
use App\Services\NormalService;
use App\ParserModule\WeatherModule;
use App\Helper\AiHelper;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use Line\LINEBot\Event\MessageEvent\TextMessage;
use Line\LINEBot\Event\MessageEvent\StickerMessage;
use Illuminate\Support\Facades\Log;

class EventHandler
{

    /**
     * @var LINEBot\Event\BaseEvent[]
     */
    private $events;

    /**
     * @var LINEBot
     */
    private $bot;

    public function __construct($body, $signature)
    {
        $httpClient = new CurlHTTPClient(env('BOT_CHANNEL_ACCESS_TOKEN'));
        $this->bot = new LINEBot($httpClient, ['channelSecret' => env('BOT_CHANNEL_SECRET')]);
        $this->events = $this->bot->parseEventRequest($body, $signature);
    }

    public function progress()
    {
        $aiHelper = new AiHelper();

        foreach ($this->events as $event) {
            $resText = '';
            if ($event instanceof TextMessage) {
                $aiHelper->ask($event->getText());
                if ($aiHelper->getAction() === 'register' && $aiHelper->getParameter('service') === 'account') {
                    $resText = $this->registerProgress($event);
                } elseif ($aiHelper->getAction() === 'get' && $aiHelper->getParameter('service') === 'weather') {
                    $location = $aiHelper->getParameter('geo-city');
                    $resText = $this->weatherProgress($event, $location);
                } else {
                    $resText = $this->echoProgress($event);
                }
            }

            if ($event instanceof StickerMessage) {
                $resText = $this->stickerProgress($event);
            }

            $messageHelper = new MessageHelper($this->bot, $event->getReplyToken());
            $response = $messageHelper->replyText($resText);
            if (!$response->isSucceeded()) {
                // Logging
                Log::warning($response->getHTTPStatus() . ' ' . $response->getRawBody());
            }
        }
    }

    private function registerProgress(TextMessage $textMessage)
    {
        $res = $this->bot->getProfile($textMessage->getUserId());
        if ($res->isSucceeded()) {
            $profile = $res->getJSONDecodedBody();
            if (WeatherService::register($profile['displayName'], $textMessage->getUserId())) {
                return '已完成註冊';
            }

            return '註冊失敗，請重試';
        }
    }

    private function weatherProgress(TextMessage $textMessage, $location = null)
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
