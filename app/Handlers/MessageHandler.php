<?php

namespace App\Handlers;

use LINE\LINEBot;

class MessageHandler
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
}