# Laravel Session Tracker
This package provides session tracking functionalities, multisession management and user device management features for laravel applications.


## Features
* Session Management
* Session Log
* Multiple session for users
* Request Log
* User Devices


## Installation (Laravel 5.x)
In composer.json:

    "require": {
        "hamedmehryar/laravel-session-tracker" "0.0.0"
    }

Run:

    composer update

Add the service provider to `config/app.php` under `providers`:

    'providers' => [
        Hamedmehryar\SessionTracker\SessionTrackerServiceProvider::class,
    ]

Add the SessionTracker alias to `config/app.php` under `aliases`:

        'aliases' => [
            'SessionTracker' => 'Hamedmehryar\SessionTracker\SessionTrackerFacade',
        ]
	
Update config file to reference your login and logout route names:

	config/sessionTracker.php

Migrate your database:

    php artisan migrate

Add the trait to your user model:

    use Hamedmehryar\SessionTracker\Traits\SessionTrackerUserTrait;
    
    class User extends Model {
    	use SessionTrackerUserTrait;
    }


Add the DeviceCheck middleware in your kernel.php file:

    protected $middleware = [
    		'Hamedmehryar\SessionTracker\Middleware\DeviceCheck',
    	];


In Your routes.php file you should add 'session' middleware for routes which you want to keep track of:

    Route::group(['middleware'=>'session'], function(){

        Route::get('your-route', 'YourController@yourAction');

    });



## Author

- [Hamed Mehryar](https://github.com/hamedmehryar)

