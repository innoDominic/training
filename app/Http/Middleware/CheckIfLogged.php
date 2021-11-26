<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class CheckIfLogged
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
        if( !session()->has('user_no') ){
            return $next($request);
        }

        if( session()->get('user_type') == 0 ){

            return redirect()->route('student-list', [
                'result' => ''
            ]);

        }else if( session()->get('user_type') == 1 ){

            return redirect()->route('attendance', [
                'result' => ''
            ]);

        }else if( session()->get('user_type') == 2 ){
        
            return redirect()->route('enroll', [
                'result' => ''
            ]);

        }else{
            dd(session()->has('user_type'));
        }

    }

}
