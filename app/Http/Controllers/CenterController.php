<?php

namespace App\Http\Controllers;

use App\Handlers\EventHandler;
use Illuminate\Http\Request;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Constant\HTTPHeader;

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
        $signature = $request->header(HTTPHeader::LINE_SIGNATURE);
        $body      = file_get_contents('php://input');
        $handler   = new EventHandler($body, $signature);

        $handler->progress();
    }

    public function test()
    {

    }
}
