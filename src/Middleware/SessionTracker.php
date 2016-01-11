<?php namespace Hamedmehryar\SessionTracker\Middleware;

use Closure;
use \Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Config;
use Hamedmehryar\SessionTracker\Models\Session;

class SessionTracker {


	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if ($this->auth->guest())
		{
			if ($request->ajax())
			{
				return response('Unauthorized.', 401);
			}
			else
			{
				return redirect()->route(Config::get('sessionTracker.logout_route_name'));
			}
		}else{
			if(Session::isBlocked() || Session::isInactive()){
				if ($request->ajax())
				{
					return response('Unauthorized.', 401);
				}
				else
				{
					return redirect()->route(Config::get('sessionTracker.logout_route_name'));
				}
			}
			Session::refresh($request);
			Session::log($request);
		}
		return $next($request);
	}

}
