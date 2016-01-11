<?php namespace Hamedmehryar\SessionTracker;

/**
 * This file is part of SessionTracker
 *
 * @license MIT
 * @package hamedmehryar\session-tracker
 */

use Illuminate\Support\Facades\Facade;

class SessionTrackerFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'sessionTracker';
    }
}
