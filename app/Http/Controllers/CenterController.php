<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\MessageEvent\StickerMessage;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Helper\FlagHelper;
use App\Services\WeatherService;
use App\Services\RegisterService;

class CenterController extends Controller
{
    private $httpClient;
    private $bot;

    public function __construct()
    {
        // Initialize
        $this->httpClient = new CurlHTTPClient(env('BOT_CHANNEL_ACCESS_TOKEN'));
        $this->bot        = new LINEBot($this->httpClient, ['channelSecret' => env('BOT_CHANNEL_SECRET')]);
    }

    public function center(Request $request)
    {
        // Check header from LINE
        $signature = $request->header(HTTPHeader::LINE_SIGNATURE);
        if (empty($signature)) {
            return $request->withStatus(400, 'Bad Request');
        }

        // Get event
        $body   = file_get_contents('php://input');
        $events = $this->bot->parseEventRequest($body, $signature);

        foreach ($events as $event) {
            if ($event instanceof TextMessage) {
                // Weather info
                if ($event->getText() === '天氣' or $event->getText() === 'weather') {
                    $weatherService = new WeatherService($event);
                    $text = $weatherService->getInfo();
                } else if ($event->getText() === '註冊') {
                    $userService = new UserService();
                    $res = $this->bot->getProfile($event->getUserId());
                    if ($res->isSucceeded()) {
                        $profile = $res->getJSONDecodedBody();
                        if ($userService->register($profile['displayName'], $userService->SERVICES_WEATHER)) {
                            $text = '已完成註冊';
                        } else {
                            $text = '註冊失敗，請重試';
                        }
                    }
                    
                } else {
                    // Echo
                    $text = $event->getText();
                }

                $response = $this->bot->replyText($event->getReplyToken(), $text);

                if ($response->isSucceeded()) {
                    echo 'succeeded';
                    continue;
                }

                // Logging
                Log::warning($response->getHTTPStatus() . ' ' . $response->getRawBody());
                continue;
            }

            if ($event instanceof StickerMessage) {
                $text      = '>/////<';
                $packageId = $event->getPackageId();
                $stickerId = $event->getStickerId();
                $response  = $this->bot->replyText($event->getReplyToken(), $text);

                if ($response->isSucceeded()) {
                    echo 'succeeded';
                    continue;
                }

                // Logging
                Log::warning($response->getHTTPStatus() . ' ' . $response->getRawBody());
                continue;
            }
        }
    }

    // only for test
    public function weatherInfo(Request $request)
    {
        $message = '';
        $success  = false;

        // Get weather data
        $token  = env('WEATHER_SERVICE_TOKEN');
        $dataId = 'F-C0032-009';
        $url    = sprintf('http://opendata.cwb.gov.tw/opendataapi?dataid=%s&authorizationkey=%s',$dataId , $token);

        $client  = new Client();
        $res     = $client->get($url);
        
        if ($res->getStatusCode() === 200) {
            // echo 'succeeded';
            $xmlData  = simplexml_load_string($res->getBody());
            $dataSet  = $xmlData->dataset;
            $wDescArr = $dataSet->parameterSet->parameter;

            // echo '<pre>';
            // print_r($dataSet);
            // echo '</pre>';

            // echo '<pre>';
            // print_r($xmlData);
            // echo '</pre>';

            foreach($wDescArr as $desc) {
                $message .= $desc->parameterValue;
            }

            $success = true;
            // echo $message;

        } else {

        }

        // $message            = $request->input('message');
        // $to                 = $request->input('userId');
        $textMessageBuilder = new TextMessageBuilder($message);
        // $to                 = env('BOT_TEST_MID');

        if ($success) {
            $this->bot->pushMessage($to, $textMessageBuilder);
        }
    }

    public function test()
    {
        $base = 0b1001;
        $value = 1;

        $flag = new FlagHelper($base);

        if ($flag->setOn($value)) {
            echo $flag->getBinaryStr();
        } else {
            echo $flag->getBinaryStr();
        }
        exit();


    }
}
