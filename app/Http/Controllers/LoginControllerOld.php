<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
	public function onetime_login_token(Request $request, $token) {
		date_default_timezone_set('Asia/Jakarta');
		$is_valid = DB::table('users')->where('autologin_token',$token)
						->where('autologin_token_expire','>',date("Y-m-d H:i:s"))
						->first();

		if($is_valid) {
			$user = User::where('id', $is_valid->id)->first();
			if($user) {
				//langsung set null setelah digunakan
				DB::table('users')->where('id',$user->id)->update(['autologin_token'=>null,'autologin_token_expire'=>null]);
				Auth::login($user);

                // cek type service from helpdesk
                if ($request->query('type_service')) {
                    if ($request->query('type_service') == 'service') {
                        return redirect('/request-service/service-catalog/2');
                    }

                    if ($request->query('type_service') == 'incident') {
                        return redirect('/request-incident/incident-catalog/2');
                    }
                } else {
                    return redirect()->intended('home');
                }
			} else {
				return redirect('/login');
			}
		} else {
			return redirect('/login');
		}
	}

    public function authenticate(Request $request)
    {
		//echo "TES";

        $credentials = $request->only('username', 'password');
		//var_dump($credentials);



		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://sso.nabatisnack.co.id/api/authentication',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => 'username='.$credentials['username'].'&password='.$credentials['password'],
		  CURLOPT_HTTPHEADER => array(
			'Content-Type: application/x-www-form-urlencoded',
			'Cookie: PHPSESSID=aube8408o8kbh11lj85av9va06'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		echo $response;
		$res_json = json_decode($response);
		if(isset($res_json->success) && $res_json->success == true) {
			//VALID API USERNAME PASSWORD
			//CHECK FROM DB
			//CREATE USER IF NOT EXIST
			//AUTOMATICLOGIN
			$user = User::where('username', $credentials['username'])->first();
			if(!$user) {
				$user = User::where('email', $credentials['username'])->first();
			}

			if($user) {
				Auth::login($user);

				return $this->directLogin();
				//return redirect()->intended('home')->with('status', 206);
			} else {
				//CREATE USER
				$data = new User();
				$data->name = $request->username;
				$data->username = $request->username;
				$data->email = "";
				$data->password = "use_api_password";//Hash::make($request->password);
				$data->created_at = Carbon::now();
				$data->updated_at = Carbon::now();
				$data->save();
				$user = $data;

				Auth::login($user);

				return $this->directLogin();
				//return redirect()->intended('home');
			}
		} else {
			//CHECK FROM LOCAL DB (REGULAR AUTHENTICATION)
			if (isActiveNik($request->username) == "Active") {//$credentials
				if(Auth::attempt(['username'=>$request->username,'password'=>$request->password])) {
					$request->session()->regenerate();
					return $this->directLogin();//return redirect()->intended('home')->with('status', 206);
				} else {
					return back()->withErrors([
						'general' => 'The provided username/password do not match our records.',
					]);
				}
			} else if (in_array(isActiveNik($request->username), ["Inactive","Deleted"])) {
				return back()->withErrors([
					'general' => 'Your account is not active or not available in this application',
				]);
			}

            if (isActiveEmailUser($request->username) == "Active") {
				if(Auth::attempt(['email' => $request->username, 'password' => $request->password])) {
					$request->session()->regenerate();
					return $this->directLogin();//return redirect()->intended('home')->with('status', 206);
				} else {
					return back()->withErrors([
						'general' => 'The provided username/password do not match our records.',
					]);
				}
			} else if (in_array(isActiveEmailUser($request->username), ["Inactive","Deleted"])) {
				return back()->withErrors([
					'general' => 'Your account is not active or not available in this application',
				]);
			}

			return back()->withErrors([
				'general' => 'The provided username/password do not match our records.',
			]);

		}
		die;

    }
    public function directLogin() {
		return redirect()->intended('home')->with('status', 206);
	}
    public function logout(Request $request)
	{
		Auth::logout();

		$request->session()->invalidate();
		$request->session()->regenerateToken();

		$url = env('GRP_HELPDESK_URL');
		return redirect($url."/logout");//arahin ke helpdesk

		////return redirect('/login');
	}
}
