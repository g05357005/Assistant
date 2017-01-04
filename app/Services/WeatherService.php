<?php

namespace App\Services;

use GuzzleHttp\Client;
use LINE\LINEBot\Event\MessageEvent\TextMessage;

class WeatherService
{
    private $event;

    public function __construct(TextMessage $event)
    {
        $this->event = $event;
    }

    public function getInfo()
    {
        $message = '';

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

            foreach($wDescArr as $desc) {
                $message .= $desc->parameterValue;
            }

            return $message;
        }

        return false;
    }
}