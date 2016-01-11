<?php namespace Hamedmehryar\SessionTracker;

use Hamedmehryar\SessionTracker\Models\Device;
use Hamedmehryar\SessionTracker\Models\Session;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * This class is the main entry point of SessionTracker. Usually this the interaction
 * with this class will be done through the SessionTracker Facade
 *
 * @license MIT
 * @package hamedmehryar\session-tracker
 */

class SessionTracker
{
    /**
     * Laravel application
     *
     * @var \Illuminate\Foundation\Application
     */
    public $app;

    /**
     * Create a new confide instance.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * @return static
     */
    public function startSession(){
        return Session::start();
    }

    /**
     * @param bool $forgetSessionId
     * @return bool
     */
    public function endSession($forgetSessionId = false){
        return Session::end($forgetSessionId);
    }

    /**
     * @return bool
     */
    public function renewSession(){
        return Session::renew();
    }

    /**
     * @param $request
     * @return bool
     */
    public function refreshSession($request){
        return Session::refresh($request);
    }

    /**
     * @param $request
     * @return bool
     */
    public function logSession($request){
        return Session::log($request);
    }

    /**
     * @param null $user
     * @return bool
     */
    public function isSessionInactive($user = null){
        return Session::isInactive($user);
    }

    /**
     * @param $sessionId
     * @return bool
     */
    public function blockSession($sessionId){
        return Session::blockById($sessionId);
    }

    public function sessionRequests($sessionId){
        try {
            $session = Session::findOrFail($sessionId);
        } catch (ModelNotFoundException $e) {
            return null;
        }
        return $session->requests;
    }
    /**
     * @return bool
     */
    public function isSessionBlocked(){
        return Session::isBlocked();
    }

    /**
     * @return int|null
     */
    public function lockSessionByCode(){
        return Session::lockByCode();
    }

    /**
     * @param $code
     * @return bool
     */
    public function unlockSessionByCode($code){
        return Session::unlockByCode($code);
    }

    /**
     * @return bool
     */
    public function isUserDevice(){
        return Device::isUserDevice();
    }

    /**
     * @param $uid
     * @param $browser
     * @param $platform
     * @param $device
     * @return bool
     */
    public function addUserDevice($uid, $browser, $platform, $device){
        return Device::addUserDevice($uid, $browser,$platform, $device);
    }
}

