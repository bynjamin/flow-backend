<?php

namespace App\Http\Middleware;

use Closure;


class TenantConfirm
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
        if (auth()->check()) {
            
            $payload = auth()->payload();
    
            if ($payload->get('fqdn') != \App\Tenant\User::getFQDN()) {
                auth()->logout();
    
                throw new \GraphQL\Error\Error('Not Authorized!');

                //header("HTTP/1.1 401 Unauthorized");
                exit;
            }   
                 
        }

        return $next($request);
    }
}
