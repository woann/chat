<?php

namespace App\Http\Middleware;
use Closure;
use DB;
class Login{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $session = session("user");
        if ($session == null) {
            return redirect("/login");//跳转到完善信息页
        }
        return $next($request);
    }
}