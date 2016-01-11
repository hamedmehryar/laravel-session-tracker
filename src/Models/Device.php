<?php namespace Hamedmehryar\SessionTracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

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

    public static function addUserDevice($uid, $browser, $platform, $device){
        $device = self::create([
            'user_id' => Auth::user()->id,
            'uid' => $uid,
            'browser' => $browser,
            'platform'=> $platform,
            'device' => $device
        ]);
        if($device){
            return true;
        }else{
            return false;
        }
    }
}
