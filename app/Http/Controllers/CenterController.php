<?php

namespace App\Http\Controllers;

use App\Handlers\EventHandler;
use Illuminate\Http\Request;
use LINE\LINEBot\Constant\HTTPHeader;

class CenterController extends Controller
{
    public function __construct()
    {
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
