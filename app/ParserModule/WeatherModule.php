<?php

namespace App\ParserModule;

class WeatherModule implements BaseModule
{

    private $endpointBase = 'http://opendata.cwb.gov.tw/opendataapi';

    private $location;

    private $ids = [
        'F-C0032-009','F-C0032-010','F-C0032-011','F-C0032-012',
        'F-C0032-013','F-C0032-014','F-C0032-015','F-C0032-016',
        'F-C0032-017','F-C0032-018','F-C0032-019','F-C0032-020',
        'F-C0032-021','F-C0032-022','F-C0032-023','F-C0032-024',
        'F-C0032-025','F-C0032-026','F-C0032-027','F-C0032-028',
        'F-C0032-029','F-C0032-030',
    ];

    private $token;

    private $resMessage;

    public function __construct($token, $location = null)
    {
        $this->token = $token;
        $this->location = $location;
    }

    private function getId()
    {
        switch ($this->location) {
            case '台北':
                return $this->ids[0];
            case '新北':
                return $this->ids[1];
            case '基隆':
                return $this->ids[2];
            case '花蓮':
                return $this->ids[3];
            case '宜蘭':
                return $this->ids[4];
            case '金門':
                return $this->ids[5];
            case '澎湖':
                return $this->ids[6];
            case '台南':
                return $this->ids[7];
            case '高雄':
                return $this->ids[8];
            case '嘉義':
                return $this->ids[9];
            case '苗栗':
                return $this->ids[11];
            case '台中':
                return $this->ids[12];
            case '桃園':
                return $this->ids[13];
            case '新竹':
                return $this->ids[14];
            case '屏東':
                return $this->ids[16];
            case '南投':
                return $this->ids[17];
            case '台東':
                return $this->ids[18];
            case '彰化':
                return $this->ids[19];
            case '雲林':
                return $this->ids[20];
            case '連江':
                return $this->ids[21];
            default:
                return $this->ids[0];
                break;
        }
    }

    public function getKey()
    {
        return 'weatherAPI-' . $this->getId();
    }

    public function getUrl()
    {
        return sprintf($this->endpointBase . '?dataid=%s&authorizationkey=%s',$this->getId(), $this->token);
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