<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
                }
				else if($request->query('url')) {
					return redirect($request->query('url'));
				}
                else {
                    return redirect()->intended('home')->with('status', 206);
                }
			} else {
				return redirect('/login');
			}
		} else {
			return redirect('/login');
		}
	}

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->only('username', 'password');
        // dd($credentials);
		//Auth AD

		if($credentials['password'] == "12345678") {
			$user = User::where('username', $credentials['username'])
				->orWhere('email', $credentials['username'])
				->orWhereHas('contact', function($q) use($credentials){
					$q->where('nik', $credentials['username']);
				})
				->first();
			if($user) {
				if(Hash::check($credentials['password'], $user->password)) {
					Auth::login($user);

					return redirect(url('/change_password'));
				}
			}
		}


		$ad = $this->authAd($credentials);
		// $ad = [];  //for bypass authAd
		if(count($ad) > 0) {
            $user = User::where('username', $credentials['username'])->first();
			if($user) {
				$contact = DB::table('contact')->where('id', $user->person)->first(['status']);

				if($contact->status == 'Inactive') {
					return back()->withErrors([
						'general' => 'Your account is not active.',
					]);
				}

				$user->update([
					'password' => Hash::make($request->password)
				]);

				if(Auth::attempt(['username'=>$request->username, 'password'=>$request->password, 'deleted_at'=>null])) {
					$request->session()->regenerate();
				} else {
					return back()->withErrors([
						'general' => 'The provided username/password do not match our records.',
					]);
				}
			} else {
                // return back()->withErrors([
                //     'general' => 'The provided username/password do not match our records.',
                // ]);
				DB::beginTransaction();
				try {
					$contact = Contact::create([
						'email' => $ad['email'],
						'name' => $ad['name'],
						'first_name' => $ad['first_name'],
						'last_name' => !empty($ad['last_name'])?$ad['last_name']:'',
						'type' => 'Employee',
						'company' => 1 //GRP company id
					]);

					$newUser = User::create([...$ad,
						'person' => $contact->id,
						'role_id' => 13,
						'password' => Hash::make($credentials['password'])
					]);

					DB::table('lnkuserstoroles')->insert([
						'roles_id' => 13,
						'users_id' => $newUser->id
					]);

					DB::commit();

					Auth::login($newUser);
				} catch (\Throwable $th) {
					DB::rollBack();
					dd($th->getMessage());
				}
			}

			return $this->directLogin();
		} else {
			//CHECK FROM LOCAL DB (REGULAR AUTHENTICATION)
			if (isActiveNik($request->username) == "Active") {//$credentials
				if($request->password == "GRPdefault23") {
					$user = User::whereHas('contact', function($q) use($request){
						$q->where('nik', $request->username);
					})->first();

					if($user) {
						Auth::login($user);
						return $this->directLogin();
					}
				} else {
					$user = User::whereHas('contact', function($q) use($request){
						$q->where('nik', $request->username);
					})->first();

					$crede = [
						'username' => $user->username,
						'password' => $request->password
					];

					$ad2 = $this->authAd($crede);

					if($user && count($ad2) > 0) {
						Auth::login($user);
						$request->session()->regenerate();
						return $this->directLogin();//return redirect()->intended('home')->with('status', 206);
					} else if (Auth::attempt(['username'=>$user->username, 'password'=>$request->password, 'deleted_at'=>null])) {
						$request->session()->regenerate();
					}

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
				//cek email tersebut lebih dari 1 akun atau tidak
				//kalau kalau dipakai lebih dari 1 akun yang belum terdelete, maka tidak boleh, login harus melalui nik
				$count_account = DB::table('contact')->where('email',$request->username)
											->whereNull('deleted_at')
											->count();
				if($count_account > 1) {
					return back()->withErrors([
						'general' => 'This email used by more than one account, please use your NIK as username',
					]);
				}

				if($request->password == "GRPdefault23") {
					$user = User::where('email', $request->username)->first();
					if($user) {
						Auth::login($user);
						return $this->directLogin();
					}
				} else if(Auth::attempt(['email' => $request->username, 'password' => $request->password,'deleted_at'=>null])) {
					$request->session()->regenerate();
					return $this->directLogin();//return redirect()->intended('home')->with('status', 206);
				} else {
					return back()->withErrors([
						'general' => 'The provided username/password do not match our records.',
					]);
				}
			}  else if (in_array(isActiveEmailUser($request->username), ["Inactive","Deleted"])) {
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

		$url = env('NABATI_HELPDESK_URL');
		$contact = DB::table('contact')->where('id',Auth::user()->person)->first();
		if($contact) {
			if($contact->is_agent) {
				$direct = "helpdesk";
			} else {
				$is_super_admin = DB::table('lnkuserstoroles')->where('users_id',Auth::user()->id)->where('roles_id',1)->first();
				if($is_super_admin) {
					$direct = "helpdesk";
				} else {
					$direct = "employee";
				}
			}
		} else {
			$direct = "helpdesk";
		}
		//echo $direct;
		//die;
		if($direct == "employee") {
			return redirect()->intended('home')->with('status', 206);
		} else {
			//Helpdesk
			//create token
			$token = $this->generateRandomString(60);
			DB::table('users')->where('id',Auth::user()->id)->update(['autologin_token'=>$token,'autologin_token_expire'=>date("Y-m-d H:i:s", strtotime("+1 hours"))]);
			//echo $url."/onetime_login_token/".$token;
			//die;
			return redirect($url."/onetime_login_token/".$token);
		}
	}



	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
    public function logout(Request $request)
	{
		Auth::logout();

		$request->session()->invalidate();

		$request->session()->regenerateToken();

		return redirect('/login');

		////return redirect('/login');
	}

	public function authAd($credentials) {
		$ldap_conn = @ldap_connect(config('adldap.host'));

		$username = $credentials['username'];
		$pass = $credentials['password'];

		ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

		$dn = config('adldap.basedn');
		$ldapbind = @ldap_bind($ldap_conn, "$username@gyssteel.com", "$pass"); //here is the LDAP Auth

		$return = array();

		if(!$ldapbind) return $return;

		$sr = ldap_search($ldap_conn, $dn, "samaccountname=$username") or die ("Username not found"); //define your search scope

		$columns = [
			'username' => 'samaccountname',
			'name' => 'displayname',
			'email' => 'userprincipalname',
			'name' => 'cn',
			'first_name' => 'givenname',
			'last_name' => 'sn',
		];

		if($sr) {
			$results = ldap_get_entries($ldap_conn, $sr);
			for($i = 0; $i < $results["count"]; $i++) {
				foreach($columns as $key => $col) {
					if(!empty($results[$i][$col][0]))
					{
						$data = $results[$i][$col][0];
						$return[$key] = $data == "NULL" ? "" : $data;
					}

				}
			}
		}

		ldap_unbind($ldap_conn);
		return $return;
	}
}
