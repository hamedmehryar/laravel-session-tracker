<?php namespace Hamedmehryar\SessionTracker;


class AuthenticationHandler {


    /**
     * Handle user login events.
     */
    public function onLogin($event)
    {
        if(SessionTrackerFacade::forgotSession()){
            SessionTrackerFacade::startSession();
        }else{
            SessionTrackerFacade::renewSession();
        }
    }

    /**
     * Handle user logout events.
     */
    public function onLogout($event)
    {
        SessionTrackerFacade::endSession();
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
        $events->listen('auth.login', 'Hamedmehryar\SessionTracker\AuthenticationHandler@onLogin');

        $events->listen('auth.logout', 'Hamedmehryar\SessionTracker\AuthenticationHandler@onLogout');
    }
}