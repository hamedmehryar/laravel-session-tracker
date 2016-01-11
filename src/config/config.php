<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Ignore routes for logging
	|--------------------------------------------------------------------------
	| This option specifies the routes which the Session Pal must not create
	| the log for them.
	| The format is:
	| 'ignore_log' => [
	|	array('method'=>'get', 'route'=>'route.name'),
	| 	array('method'=>'post','route'=>'route/uri/{param}')
	| ],
	|
	*/

	'ignore_log' => [
		array('method'=>'get', 'route'=> 'notification/getEvents/{eventTime}'),
		array('method'=>'get', 'route'=> 'notification/getEvents/{eventTime}')
	],

	/*
	|--------------------------------------------------------------------------
	| Ignore routes for refreshing the session
	|--------------------------------------------------------------------------
	| This option specifies the routes which the session must not be refreshed
	| when they are requested,(e.g. poller requests and chat requests)
	|
	| The format is:
	| 'ignore_refresh' => [
	|	array('method'=>'get', 'route'=>'route.name'),
	| 	array('method'=>'post','route'=>'route/uri/{param}')
	| ],
	|
	*/

	'ignore_refresh' => [
		array('method'=>'get', 'route'=> 'notification/getEvents/{eventTime}'),
	],

	/*
	|--------------------------------------------------------------------------
	| Session inactivity seconds
	|--------------------------------------------------------------------------
	|
	| This option allows you to easily specify the period of time in seconds that
	| the session is considered inactive or idle.
	|
	*/

	'inactivity_seconds' => 1200,

	/*
	|--------------------------------------------------------------------------
	| Login route name
	|--------------------------------------------------------------------------
	|
	| This option allows you to easily specify the route name for login form.
	| Session Pal uses this config item to redirect the request to login page.
	| Note: Your login route must have a name.
	|
	*/

	'login_route_name' => 'app.login',

	/*
	|--------------------------------------------------------------------------
	| Logout route name
	|--------------------------------------------------------------------------
	|
	| This option allows you to easily specify the route name for logout form.
	| Session Pal uses this config item to redirect the request to login page.
	| Note: Your logout route must have a name.
	|
	*/

	'logout_route_name' => 'app.logout',

];
