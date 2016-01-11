<?php namespace Hamedmehryar\SessionTracker\Middleware;

use Closure;
use Illuminate\Support\Facades\Cookie;

class DeviceCheck {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if(!Cookie::has('d_i')){
			Cookie::queue(Cookie::forever('d_i', str_random(60)));
		}
		return $next($request);
	}

}
