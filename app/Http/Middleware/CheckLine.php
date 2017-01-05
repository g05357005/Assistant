<?php

namespace App\Http\Middleware;

use Closure;
use LINE\LINEBot\Constant\HTTPHeader;

class CheckLine
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Check header from LINE
        $signature = $request->header(HTTPHeader::LINE_SIGNATURE);
        if (empty($signature)) {
            return $request->withStatus(400, 'Bad Request');
        }

        return $next($request);
    }
}
