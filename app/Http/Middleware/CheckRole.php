<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
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
        // Pre-Middleware Action
        if(Auth::user()->level==1){
          return $next($request);
        }

        // Post-Middleware Action
        $response=[
          'message'=>'[STOP]Anda tidak berhak mengakses!.',
        ];
        return response($response, 401);
    }
}
