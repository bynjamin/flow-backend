<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 *
 */
class Cors
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
        $response = $next($request);
        
        $origin = $request->headers->get('origin');
        $requestHeaders = $request->headers->get('Access-Control-Request-Headers');

        // if ($request->getMethod() === "OPTIONS") {
        //     return response('')->withHeaders([
        //         'Access-Control-Allow-Origin' => $origin,
        //         'Access-Control-Allow-Methods' => 'GET, POST, PATCH, PUT, DELETE, OPTIONS',
        //         'Access-Control-Allow-Headers' =>  $requestHeaders,
        //         'Access-Control-Allow-Credentials' =>  'true',
        //         'Access-Control-Max-Age' => '86400',
        //     ]);;
        // }

        $response->header('Access-Control-Allow-Origin', $origin)
                ->header('Access-Control-Allow-Methods','GET, POST, PATCH, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', $requestHeaders)
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Access-Control-Max-Age','86400');

        return $response;


        
        /*$origin = $request->header('origin');

        // prerobit na regexp? takto by mohla zbehnut url xxxx.yyyy.com/.flowato.local
        // if (strpos($origin, env('TENANT_URL_BASE')) === false) {
        //     return $next($request);
        // }

        if ($request->getMethod() === 'OPTIONS') {

            return response(null, 200)
                    ->withHeaders([
                        'Access-Control-Allow-Origin' => $origin,
                        'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
                        'Access-Control-Allow-Headers' => 'X-Requested-With, Content-Type, X-Token-Auth, X-Auth-Token, Authorization','Origin',
                        'Access-Control-Allow-Credentials' => 'true',
                        'Access-Control-Max-Age'           => '0', //86400
                    ]);


        }

        return $next($request)
                    ->header('Access-Control-Allow-Origin',  $origin)
                    ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                    ->header('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, X-Token-Auth, X-Auth-Token, Authorization', 'Origin')
                    ->header('Access-Control-Allow-Credentials','true');
                    // ->header('Set-Cookie','sessionid=38afes7a8; HttpOnly; Path=/; Domain=zynctos.devo');
*/
    }
}
