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
use GuzzleHttp\client;

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

    public function echo(Request $request)
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
                // Handle about text message
                $text     = $event->getText();
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

    // Push messages to specific group of user
    public function weatherInfo(Request $request)
    {
        $message = '';
        $success  = false;

        // Get weather data
        $token  = env('WEATHER_SERVICE_TOKEN');
        $dataId = 'F-C0032-009';
        $url    = sprintf('http://opendata.cwb.gov.tw/opendataapi?dataid=%s&authorizationkey=%s',$dataId , $token);

        $client  = new client();
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
        $to                 = env('BOT_TEST_MID');

        if ($success) {
            $this->bot->pushMessage($to, $textMessageBuilder);
        }
    }

    // Message Center
    public function center()
    {

    }
}
