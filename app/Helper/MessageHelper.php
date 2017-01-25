<?php

namespace App\Helper;

use LINE\LINEBot;

class MessageHelper
{
    private $bot;

    private $token;

    function __construct(LINEBot $bot, $token)
    {
        $this->bot = $bot;
        $this->token = $token;
    }

    public function replyText($replyText)
    {
        return $this->bot->replyText($this->token, $replyText);
    }

    public function pushTextTo($to, $text)
    {
        $message = new LINEBot\MessageBuilder\TextMessageBuilder($text);
        return $this->bot->pushMessage($to, $message);
    }
}