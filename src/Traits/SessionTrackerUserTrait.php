<?php namespace Hamedmehryar\SessionTracker\Traits;


use Illuminate\Support\Facades\Session;
use Hamedmehryar\SessionTracker\Models\Session as SessionTrack;

trait SessionTrackerUserTrait{

    public function activeSessions($exceptSelf = false){
        $query =  $this->sessions()->where('end_date', null)->where('block', SessionTrack::STATUS_DEFAULT);
        if($exceptSelf){
            if(Session::has('dbsession.id')){
                $query->where('id', '!=', Session::get('dbsession.id'));
            }
        }
        return $query;
    }

    public function sessions(){
        return $this->hasMany('Hamedmehryar\SessionTracker\Models\Session');
    }

    public function getFreshestSession(){
        return $this->sessions()->orderBy('last_activity', 'desc')->first();
    }

    public function devices(){
        return $this->hasMany('Hamedmehryar\SessionTracker\Models\Device');
    }

    public function devicesUids(){
        return $this->devices()->lists('uid');
    }
}