<?php namespace Hamedmehryar\SessionTracker;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class SessionTrackerServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->publishes([
			base_path('vendor/hamedmehryar/laravel-session-tracker/src/config/config.php') => config_path('sessionTracker.php'),
			base_path('vendor/hamedmehryar/laravel-session-tracker/src/migrations') => base_path('database/migrations')
		]);

		$router = $this->app['router'];
		$router->middleware('session', 'Hamedmehryar\SessionTracker\Middleware\SessionTracker');
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->mergeConfigFrom(
			base_path('vendor/hamedmehryar/laravel-session-tracker/src/config/config.php'), 'sessionTracker'
		);
		$this->registerSessionTracker();
		$this->registerAuthenticationEventHandler();
	}

	/**
	 * Register the the sessionTracker facade.
	 *
	 * @return void
	 */
	private function registerSessionTracker()
	{
		$this->app->bind('sessionTracker', function ($app) {
			return new SessionTracker($app);
		});
	}

	private function registerAuthenticationEventHandler(){

		Event::subscribe(new AuthenticationHandler());
	}

}
