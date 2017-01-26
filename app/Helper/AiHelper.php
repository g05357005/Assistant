<?php

namespace App\Helper;

use GuzzleHttp\Client;

class AiHelper
{
    private $client;

    private $response;

    private $endpoint = 'https://api.api.ai/v1/';

    private $headers = [];

    public function __construct()
    {
        $this->client = new Client();
        $this->headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('API_CLIENT_ACCESS_TOKEN'),
        ];
    }

    public function ask($text)
    {
        $body = [
            'query' => $text,
            'v' => '20150910',
            'lang' => 'zh-TW',
            'sessionId' => 'userid',
        ];

        $options = $this->buildOptions($body);
        $this->response = $this->client->request('post', $this->endpoint . 'query', $options);
    }

    public function isSucceeded()
    {
        if ($this->response) {
            return $this->response->getStatusCode() === 200;
        }

        return false;
    }

    public function getStatusCode()
    {
        if ($this->response) {
            return $this->response->getStatusCode();
        }

        return false;
    }

    public function isMatch()
    {
        if ($this->getBody()->result->action === 'input.unknown') {
            return false;
        }

        return true;
    }

    public function getBody()
    {
        return json_decode($this->response->getBody());
    }

    public function getAction()
    {
        $body = $this->getBody();
        return $body->result->action;
    }

    public function getParameter($attr = null)
    {
        $body = $this->getBody();
        $param = $body->result->parameters;
        if (isset($param->$attr)) {
            return $param->$attr;
        }

        return false;
    }

    private function buildOptions($body)
    {
        return [
            'headers' => $this->headers,
            'body' => json_encode($body),
            'http_errors' => false,
        ];
    }
}