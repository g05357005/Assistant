<?php

namespace App\Http\Controllers;

use App\Handlers\EventHandler;
use Illuminate\Http\Request;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

class CenterController extends Controller
{
    private $httpClient;
    private $bot;

    public function __construct()
    {
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

        $body    = file_get_contents('php://input');
        $handler = new EventHandler($this->bot, $body, $signature);
        $handler->progress();
    }

    public function test()
    {

    }
}
