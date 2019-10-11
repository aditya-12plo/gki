<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Support\Facades\Auth;


class CheckUserStatus
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
        if(Auth::check() && Auth::user()->status != 'active'){

            Auth::logout();

            $request->session()->flash('alert-danger', 'Your Account is not activated yet.');

            return redirect('/')->with('erro_login', 'Your Account is not activated yet.');

        }

        return $response;
    }
}
