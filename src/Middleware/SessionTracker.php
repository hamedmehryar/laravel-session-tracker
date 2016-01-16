<?php namespace Hamedmehryar\SessionTracker\Middleware;

use Closure;
use Hamedmehryar\SessionTracker\SessionTrackerFacade;
use \Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Config;

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
			SessionTrackerFacade::endSession(true);
		}else{
			if(SessionTrackerFacade::isSessionBlocked() || SessionTrackerFacade::isSessionInactive()){
				if ($request->ajax())
				{
					return response('Unauthorized.', 401);
				}
				else
				{
					return redirect()->route(Config::get('sessionTracker.logout_route_name'));
				}
			}
			elseif(SessionTrackerFacade::isSessionLocked()){
				return redirect()->route(Config::get('sessionTracker.login_code_route_name'));
			}
			SessionTrackerFacade::refreshSession($request);
			SessionTrackerFacade::logSession($request);
		}
		return $next($request);
	}

}
