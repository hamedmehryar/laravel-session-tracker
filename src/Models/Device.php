<?php namespace Hamedmehryar\SessionTracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Jenssegers\Agent\Facades\Agent;

class Device extends Model {

    protected $table = 'sessiontracker_devices';

    protected $fillable = ['user_id','browser','platform','device'];

    public static function isUserDevice(){
        if(Cookie::has('d_i')){
            $user = Auth::user();
            if(in_array(Cookie::get('d_i'), $user->devicesUids())){
                return true;
            }
        }
        return false;
    }

    public static function addUserDevice(){
        if(Cookie::has('d_i')){
             self::create([
                'user_id' => Auth::user()->id,
                'uid' => Cookie::get('d_i'),
                'browser' => Agent::browser(),
                'platform'=> Agent::platform(),
                'device' => Agent::device()
            ]);
            return true;
        }else{
            return false;
        }
    }
}
