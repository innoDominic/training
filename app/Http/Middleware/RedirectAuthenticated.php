<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectAuthenticated
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
            
        if(session()->has('user_no')){
           
            $url = explode('/', url()->current());
            $pages_required_authentication = ['admin', 'teacher', 'student'];

            if(in_array($url[3], $pages_required_authentication)){

                if(session()->get('user_type') === 0 && $url[3] === 'admin'){
                    return $next($request); 
                }else if(session()->get('user_type') !== 0 && $url[3] === 'admin'){
                    return $this->redirectSpecificUser(session()->get('user_type'));
                }

                if(session()->get('user_type') === 1 && $url[3] === 'teacher'){
                    return $next($request); 
                }else if(session()->get('user_type') !== 1 && $url[3] === 'teacher'){
                    return $this->redirectSpecificUser(session()->get('user_type'));
                }

                dd("Unkown User && INVALID ROUTE");

            }

        }else{
            return redirect('/');
        }

        return $next($request);

    }

    public function redirectSpecificUser($user_type){
        # Admin : 0 / Teacher : 1 / Student : 2
        if($user_type === 0){
            return redirect('admin/student');
        }else if($user_type === 1){
            return redirect('teacher/attendance');
        }else{
            dd("Unkown User: " . $user_type);
        }
    }

}
