<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class CheckUserIfAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if( session()->get('user_no') === 0 ){

            return $next($request);
             
        }

        if( session()->get('user_type') === 1 ){

            return redirect()->route('attendance', [
                'result' => ''
            ]);

        }else if( session()->get('user_type') === 2 ){
        
            return redirect()->route('enroll', [
                'result' => ''
            ]);

        }

        return $next($request);
    }
}
