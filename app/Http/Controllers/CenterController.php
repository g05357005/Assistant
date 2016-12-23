<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot;

class CenterController extends Controller
{
    const CHANNEL_ACCESS_TOKEN = '5/e6JfcucgEbWqGb7+pfaAErw3BbhzM9VGA3fMOdP0F9m20q2DwAe7jSazDDGD9pL/5Z2lAAhluaD+zC6mxOjzbJbX/amlNDqaZuuiaAWn9BE9KbpE4nCIWazHiCv7VVqsZBcJU0Lwl6HmEhRSjlIgdB04t89/1O/w1cDnyilFU=';
    const CHANNEL_SECRET = '8e0ee0e6b36483a1dd4a2a410147962c';

    private $httpClient;
    private $bot;

    public function __construct()
    {
        // Initialize
        $this->httpClient = new CurlHTTPClient(self::CHANNEL_ACCESS_TOKEN);
        $this->bot        = new LINEBot($this->httpClient, ['channelSecret' => self::CHANNEL_SECRET]);
    }

    public function echo(Request $request)
    {
        // Check header from LINE
        $signature = $request->header(HTTPHeader::LINE_SIGNATURE);
        if (empty($signature)) {
            return $request->withStatus(400, 'Bad Request');
        }

        // Get event
        $events = $this->bot->parseEventRequest(file_get_contents('php://input'), $signature);

        foreach ($event as $event) {
            if ($event instanceof MessageEvent) {
                $text = $event->getText();

                $response = $bot->replyText($event->getReplyToken(), $text);
            }
        }
    }
}
