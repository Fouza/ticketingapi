<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

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
        // if($request->getMethod()) return response('hqkou',200)->header('Access-Control-Allow-Origin','wwwww');


        return $next($request)
            ->header('Access-Control-Allow-Origin', ['http://localhost:3000'])
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', ['X-Requested-With, Content-Type, X-Token-Auth, Authorization'])
            ->header('Access-Control-Allow-Credentials', 'true');

    }
}
