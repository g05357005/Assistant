<?php

namespace App\ParserModule;

class WeatherModule implements BaseModule
{

    private $endpointBase = 'http://opendata.cwb.gov.tw/opendataapi';

    private $dataIds = [
        'taipei' => 'F-C0032-009'
    ];

    private $token;

    private $resMessage;

    public function __construct($token = null)
    {
        $this->token = $token ? $token : env('WEATHER_SERVICE_TOKEN');
    }

    public function getKey()
    {
        return 'weatherAPI-' . $this->dataIds['taipei'];
    }

    public function getUrl()
    {
        return sprintf($this->endpointBase . '?dataid=%s&authorizationkey=%s',$this->dataIds['taipei'] , $this->token);
    }

    public function parse($body)
    {
        $xmlData  = simplexml_load_string($body);
        $dataSet  = $xmlData->dataset;
        $wDescArr = $dataSet->parameterSet->parameter;

        foreach($wDescArr as $desc) {
            $this->resMessage .= $desc->parameterValue;
        }

        return $this->resMessage;
    }
}