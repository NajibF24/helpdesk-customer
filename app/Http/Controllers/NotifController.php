<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NotifController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view_notif($notif_token)
    {
        $notif = DB::table('notification_message')->where('user_id',Auth::user()->id)->where('token',$notif_token)->first();
        if($notif) {
			DB::table('notification_message')->where('user_id',Auth::user()->id)->where('token',$notif_token)->update(['read_at'=> date("Y-m-d H:i:s")]);
			return redirect(getLink($notif->type,$notif->ref_id));
		} else {
			return redirect('/home');
		}

    }

    
}
