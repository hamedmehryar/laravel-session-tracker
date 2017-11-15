<?php namespace Hamedmehryar\SessionTracker\Models;

use Carbon\Carbon;
use Hamedmehryar\SessionTracker\SessionTrackerFacade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Jenssegers\Agent\Agent;

class Session extends Model {

    
	
    protected $table = 'sessiontracker_sessions';

    protected $fillable = ['user_id','browser','browser_version','platform','platform_version','mobile','device','robot','device_uid', 'ip','last_activity'];

    const STATUS_DEFAULT = NULL;
    const STATUS_BLOCKED = 1;

    public function requests(){
        return $this->hasMany('Hamedmehryar\SessionTracker\Models\SessionRequest')->orderBy('id', 'desc');
    }

    public static function start(){

	$deviceId = Cookie::get('d_i', NULL);
        $userId = Auth::user()->id;
        $dateNow = Carbon::now();
        if($deviceId){
            self::where('device_uid', $deviceId)->where('user_id', $userId)->whereNull('end_date')->update(['end_date' => $dateNow]);
        }
	    
	$agent = new Agent();
	    
        $session =  self::create([
            'user_id' => Auth::user()->id,
            "browser" =>  $agent->browser(),
            "browser_version" => $agent->version($agent->browser()),
            "platform" => $agent->platform(),
            "platform_version" => $agent->version($agent->platform()),
            "mobile" => $agent->isMobile(),
            "device" =>  $agent->device(),
            "robot" => $agent->isRobot(),
            "device_uid" => Cookie::get('d_i', NULL),
            'ip'      => $_SERVER['REMOTE_ADDR'],
            'last_activity'=> Carbon::now()
        ]);
        \Illuminate\Support\Facades\Session::put('dbsession.id', $session->id);
		
        return $session;
    }

    public static function end($forgetSession){
        if(\Illuminate\Support\Facades\Session::has('dbsession.id')){
            try {
                $session = self::findOrFail(\Illuminate\Support\Facades\Session::get('dbsession.id'));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Session::forget('dbsession.id');
                return false;
            }
            $session->end_date = Carbon::now();
            $session->save();
            if($forgetSession){
                \Illuminate\Support\Facades\Session::forget('dbsession.id');
            }
            return true;
        }
        return false;
    }

    public static function renew(){
        if(\Illuminate\Support\Facades\Session::has('dbsession.id')){
            try {
                $session = self::findOrFail(\Illuminate\Support\Facades\Session::get('dbsession.id'));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Session::forget('dbsession.id');
                return false;
            }
            $session->last_activity = Carbon::now();
            $session->end_date = null;
            $session->save();
            return true;
        }
        return false;
    }

    public static function refreshSes($request){
        foreach(Config::get('sessionTracker.ignore_refresh', array()) as $ignore){
            if(($request->route()->getName() == $ignore['route'] || $request->route()->getUri() == $ignore['route']) && $request->route()->methods()[0] == $ignore['method']){
                break;
            }else{
                if(\Illuminate\Support\Facades\Session::has('dbsession.id')){
                    try {
                        $session = self::findOrFail(\Illuminate\Support\Facades\Session::get('dbsession.id'));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Session::forget('dbsession.id');
                        return false;
                    }
                    $session->last_activity = Carbon::now();
                    $session->save();
                    return true;
                }
            }
        }
        return false;
    }

    public static function log($request){
        if(self::latestRequest() == null ||$request->getRequestUri() != self::latestRequest()->uri){
            foreach(Config::get('sessionTracker.ignore_log', array()) as $ignore){
                if(($request->route()->getName() == $ignore['route'] || $request->route()->getUri() == $ignore['route']) && $request->route()->methods()[0] == $ignore['method']){
                    break;
                }else{
                    if(\Illuminate\Support\Facades\Session::has('dbsession.id')){
                        $sessionRequest = SessionRequest::create([
                            'session_id' => \Illuminate\Support\Facades\Session::get('dbsession.id'),
                            'route' => $request->route()->getUri(),
                            'uri' => $request->getRequestUri(),
                            'method' => count($request->route()->getMethods())>0?$request->route()->getMethods()[0]:NULL,
                            'name' => $request->route()->getName(),
                            'parameters' => count($request->route()->parameters())>0?json_encode($request->route()->parameters()):NULL,
                            'type'       => $request->ajax()?1:0,
                        ]);
                        if($sessionRequest){
                            return true;
                        }else{
                            return false;
                        }
                    }
                }
            }
        }
        return false;
    }

    public static function latestRequest(){
        if(\Illuminate\Support\Facades\Session::has('dbsession.id')){
            try {
                $session = self::findOrFail(\Illuminate\Support\Facades\Session::get('dbsession.id'));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Session::forget('dbsession.id');
                return null;
            }
            if($session->requests()->count() > 0){
                 return $session->requests()->orderBy('created_at', 'desc')->first();
            }else{
                return null;
            }
        }
    }
    public static function isInactive($user){
        if($user != null){
            if($user->sessions->count() > 0)
            {
                if($user->getFreshestSession()->last_activity != null && abs(strtotime($user->getFreshestSession()->last_activity)-(strtotime(date('Y-m-d H:i:s')))) > Config::get('sessionTracker.inactivity_seconds', 1200)){
                    return true;
                }else{
                    return false;
                }
            }
            return true;
        }else{
            if(\Illuminate\Support\Facades\Session::has('dbsession.id')){
                try {
                    $session = self::findOrFail(\Illuminate\Support\Facades\Session::get('dbsession.id'));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Session::forget('dbsession.id');
                    return false;
                }
                if($session->last_activity != null && abs(strtotime($session->last_activity)-(strtotime(date('Y-m-d H:i:s')))) > 1200){
                    return true;
                }else{
                    return false;
                }
            }
            return true;
        }

    }

    public static function blockById($sessionId){
        try {
            $session = self::findOrFail($sessionId);
        } catch (\Exception $e) {
            return false;
        }
        $session->block();
        return true;

    }
    public function block(){
        $this->block = self::STATUS_BLOCKED;
        $this->blocked_by = Auth::user()->id;
        $this->save();
    }

    public static function isBlocked(){
        if(\Illuminate\Support\Facades\Session::has('dbsession.id')){
            try {
                $session = self::findOrFail(\Illuminate\Support\Facades\Session::get('dbsession.id'));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Session::forget('dbsession.id');
                return true;
            }
            if($session->block == self::STATUS_BLOCKED){
                return true;
            }else{
                return false;
            }
        }
        return true;

    }


    public static function isLocked(){
        if(\Illuminate\Support\Facades\Session::has('dbsession.id')){
            try {
                $session = self::findOrFail(\Illuminate\Support\Facades\Session::get('dbsession.id'));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Session::forget('dbsession.id');
                return true;
            }
            if($session->login_code != null){
                return true;
            }else{
                return false;
            }
        }
        return true;

    }

    public static function loginCode(){
        if(\Illuminate\Support\Facades\Session::has('dbsession.id')){
            try {
                $session = self::findOrFail(\Illuminate\Support\Facades\Session::get('dbsession.id'));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Session::forget('dbsession.id');
                return null;
            }
            return $session->login_code;
        }
        return null;

    }


    public static function lockByCode(){
        if(\Illuminate\Support\Facades\Session::has('dbsession.id')){
            try {
                $session = self::findOrFail(\Illuminate\Support\Facades\Session::get('dbsession.id'));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Session::forget('dbsession.id');
                return NULL;
            }
            $code = rand(100000, 999999);
            $session->login_code = md5($code);
            $session->save();
            return $code;

        }
        return NULL;
    }


    public static function refreshCode(){
        if(\Illuminate\Support\Facades\Session::has('dbsession.id')){
            try {
                $session = self::findOrFail(\Illuminate\Support\Facades\Session::get('dbsession.id'));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Session::forget('dbsession.id');
                return NULL;
            }
            $code = rand(100000, 999999);
            $session->login_code = md5($code);
            $session->created_at = Carbon::now();
            $session->save();
            return $code;

        }
        return NULL;
    }


    public static function unlockByCode($code){
        if(\Illuminate\Support\Facades\Session::has('dbsession.id')){
            try {
                $session = self::findOrFail(\Illuminate\Support\Facades\Session::get('dbsession.id'));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Session::forget('dbsession.id');
                return -1;
            }
            if(time() - strtotime($session->created_at) > Config::get('sessionTracker.security_code_lifetime', 1200)){
                return -2;
            }else{
                if(md5($code) == $session->login_code){
                    $session->login_code = NULL;
                    $session->save();
                    return 0;
                }
            }

        }
        return -1;
    }
}
