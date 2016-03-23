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
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
        $events->listen('Illuminate\Auth\Events\Login', 'Hamedmehryar\SessionTracker\AuthenticationHandler@onLogin');
    }
}