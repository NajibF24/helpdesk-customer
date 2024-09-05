<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\UserRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
//use App\Models\Wilayah;
//use App\Models\Dusun;
use App\Models\User;
//use App\Exports\UserExport;
//use Maatwebsite\Excel\Facades\Excel;
//use App\Imports\UserImport;

use Carbon\Carbon;
use Flash;
use Response;
use Redirect,DB,Config;
use Datatables;
use Exception;
use Illuminate\Support\Facades\Session;

use Validator;
use Illuminate\Validation\Rules\Password;
//use App\Helpers\h;

class UsersController extends AppBaseController
{
    /** @var  userRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
        $this->middleware('auth');
    }

    /**
     * Display a listing of the user.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
		accessv('users','list');
        $users = $this->userRepository->all();

        return view('users.index')->with('title','Users')
            ->with('users', $users);
    }

    /**
     * Show the form for creating a new user.
     *
     * @return Response
     */
    public function create()
    {
		accessv('users','create');
        return view('users.create')->with('title','Create User');
    }

    /**
     * Store a newly created user in storage.
     *
     * @param CreateuserRequest $request
     *
     * @return Response
     */
    public function store(CreateuserRequest $request)
    {
		try {
            accessv('users','create');
            
			$rules = [
				'username'          => 'required',
				'password'      => 'required|min:8|confirmed',
				'password_confirmation'      => 'required',
			];
	 
			$validator = Validator::make($request->all(), $rules);
			
			if($validator->fails()){
				return redirect()->back()->withErrors($validator)->withInput($request->all());
			}

            $person_id = $request->person;
            $person = DB::table('contact')->where('id',$person_id)->first();

            $is_exist = DB::table('users')->where('person',$person_id)->first();
            if($is_exist) {
                Flash::error('User login for this Employee has been created before. You have to use existed user account for this Contact Employee.');
                return redirect()->back()->withInput($request->all());
                
                //return redirect(route('users.index'));
            }
            $data = new User();
            $data->name = empty($person->name)?"":$person->name;//$request->name;
            $data->person = empty($person->id)?"":$person->id;
            $data->username = $request->username;
            $data->email = empty($person->email)?(empty($person->username)?$person->name:$person->username):$person->email;//$request->email;
            //$data->role_id = $request->role_id;
            $data->password = Hash::make($request->password);
            $data->created_at = Carbon::now();
            $data->updated_at = Carbon::now();
            $data->country = $person->country;
            $data->company = $person->company;
            $data->save();
        
            
            $user = $data;
            
            $input = $request->all();
            
            updateRelation($data->id,'users', ['roles'],$input);
            
            Flash::success('user saved successfully.');

            return redirect(route('users.index'))->with('status', 200);
        } catch (Exception $e) {
            Flash::error('user does exist.');

            return redirect(route('users.index'))->with('status', 400);
        }
    }

    /**
     * Display the specified user.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
		accessv('users','list');
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            Flash::error('user not found');

            return redirect(route('users.index'));
        }

        return view('users.show')->with('user', $user);
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
       accessv('users','edit');
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            Flash::error('user not found');

            return redirect(route('users.index'));
        }

        return view('users.edit')->with('title','Edit User')->with('users', $user)->with('row', $user);
    }

    /**
     * Update the specified user in storage.
     *
     * @param int $id
     * @param UpdateuserRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUserRequest $request)
    {
		//try {
            accessv('users','edit');
			$rules = [
				'username'          => 'required',
			];
	 
			$validator = Validator::make($request->all(), $rules);
			
			if($validator->fails()){
				return redirect()->back()->withErrors($validator)->withInput($request->all());
			}

            //$user = $this->userRepository->find($id);
            $user = DB::table('users')->where('id',$request->id)->get();
            $data = User::where('id',$id)->first();
            
            $person_id = $request->person;
            $person = DB::table('contact')->where('id',$person_id)->first();
            
            //$data->name = empty($person->name)?"":$person->name;//$request->name;
            //person tidak bisa diupdate lagi, sudah fix
            //$data->person = empty($person->id)?"":$person->id;
            $data->username = $request->username;
            $data->email = empty($person->email)?(empty($request->username)?"":$request->username):$person->email;//$request->email;
            //$data->role_id = $request->role_id;
            $data->updated_at = Carbon::now();
            if(isset($request->password)) {
                $request->validate([
                    'password' => 'min:8|required_with:password_confirmation|same:password_confirmation',
                    'password_confirmation' => 'min:8'
                ]);
                $data->password = Hash::make($request->password);
                

            }
            $data->save();

            $input = $request->all();
            updateRelation($data->id,'users', ['roles'],$input);
            
            if (empty($user)) {
                Flash::error('user not found');

                return redirect(route('users.index'))->with('status', 400);
            }
            else
            {
                Flash::success('User updated successfully.');
            }
            //$user = $this->userRepository->update($request->all(), $id);

            return redirect(route('users.index'))->with('status', 204);
        //} catch (Exception $e) {
            //Flash::error('User does not updated.');

            //return redirect(route('users.index'))->with('status', 400);
        //}
    }

    /**
     * Remove the specified user from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
		accessv('users','delete');
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            Flash::error('user not found');

            return redirect(route('users.index'));
        }

        $this->userRepository->delete($id);
        
        //hapus juga di tabel mt_client
		
        Flash::success('User deleted successfully.');

        return redirect(route('users.index'))->with('status', 204);
    }
	public function usersList(Request $request)
    {
		accessv('users','list');
        $users = DB::table('users')->select(
						DB::raw('users.id,users.name,users.username,users.email,roles.name as `role_name`,
							c.name AS company, co.name AS country

						'))
						->leftJoin('roles', 'roles.id', '=', 'users.role_id')->orderBy('id', 'DESC')
						->leftJoin('company as c', 'c.id', '=', 'users.company')
						->leftJoin('country as co', 'co.code', '=', 'users.country')
						//->where('users.type','admin')
						//->join('wilayah','wilayah.kode', '=', 'users.desa_id')
						//->whereRaw('(pengaturan.nama_table = "users" AND users.desa_id = ?) OR users.register_by=?', [$pejabat->desa_id,\Auth::user()->id])//
						//->groupBy('users.id')
						;
        $query2 = datatables()->of($users);
        
        return $query2
					->addColumn('action', 'users.actions')
					->filter(function ($query) use ($request) {//,$type,$join_table,$list_sample_table,$disposal)
						$input = $request->all();
						$search_value = $input['search']['value'];
						if(!empty($search_value)) {
							$type = "users";
							$join_table = ['roles'];
							//GET KOlOM2 UTAMA TABEL
							$sm = \DB::getDoctrineSchemaManager();
							$list_column = $sm->listTableColumns($type);
							$arr_column = array();
							foreach ($list_column as $c) {
								//echo $column->getName() . ': ' . $column->getType() . "\n";
								$key = $c->getName();
								if(!in_array($key,array('remember_token','password','created_at','updated_at','expire_date'
								))) {
									$arr_column[] = $type.".".$key;
								}
							}
							//kolom2 join table juga dimasukan dalam search
							foreach($join_table as $jo_table) {
								//GET KOlOM2 UTAMA TABEL
								$sm = \DB::getDoctrineSchemaManager();
								$list_column = $sm->listTableColumns($jo_table);

								foreach ($list_column as $c) {
									$key = $c->getName();
									if(!in_array($key,array('javascript','ordering','created_at','updated_at','expire_date'
									))) {
										$arr_column[] = $jo_table.".".$key;
									}
								}								
							}

								//die;
								$str = "";
								$arr_search = [];

								foreach($arr_column as $a) {
									$str .= ' ('.$a.' LIKE ?) OR';
									$arr_search[] =  "%".$search_value."%";
								}

								//var_dump($arr_search);
								//var_dump($str);


								
								//$str .= ' (DATE_FORMAT('.$type.'.created_at, "%Y-%M-%d")   LIKE ?) OR ';
								//$arr_search[] =  "%".$search_value."%";
								//$str .= ' (DATE_FORMAT('.$type.'.updated_at, "%Y-%M-%d")   LIKE ?) OR ';
								//$arr_search[] =  "%".$search_value."%";
								
								$query->whereRaw(' ('.$str.' 0)',$arr_search );
								
								//$query->whereRaw('(CONCAT(product_name,id) LIKE ?) OR ('.$type.'.id LIKE ?)', ["%".$search_value."%","%".$search_value."%"]);
								//$query->whereRaw("IF(active = 1, 'Yes', 'No') like ?", ["%{$keyword}%"]); 
						}
						

					})
                    ->addColumn('role', function($arr){
						
						//$user = $this->userRepository->find($arr->id);
						//return implode(', ',$user->getRoleNames()->all());
						//return implode(", ",$list_jabatan);
						return "";
					})
                    ->escapeColumns([])->toJson();
    }
    
   
	public function change_password()
    {
       
        $user = $this->userRepository->find(\Auth::user()->id);

        if (empty($user)) {
            Flash::error('user not found');
			echo "User Not found";
			die;
            //return redirect(route('users.index'));
        }

        return view('users.change_password')->with('users', $user)->with('row', $user);
    }

    /**
     * Update the specified user in storage.
     *
     * @param int $id
     * @param UpdateuserRequest $request
     *
     * @return Response
     */
    public function change_password_update(Request $request)
    {
		
        $id = \Auth::user()->id;
        $user = DB::table('users')->where('id',\Auth::user()->id)->first();
        $data = User::where('id',$id)->first();
        

		
        if (empty($user)) {
            Flash::error('user not found');
            return redirect(URL('/').'/change_password');
        }
        else
        {

			if (!Hash::check($request->old_password, $user->password)) { 

				//old password tidak match
				Flash::error('Old password not match. ');
				return redirect(URL('/').'/change_password');
			}
			
			
			$request->validate([
				'password' => 'min:8|required_with:password_confirmation|same:password_confirmation',//
				'password_confirmation' => 'min:8'
			]);
			$data->password = Hash::make($request->password);
			
			$data->updated_at = Carbon::now();
			
			$data->save();
			
            Flash::success('Password Updated successfully.');
        }
        //$user = $this->userRepository->update($request->all(), $id);      

        return redirect(URL('/').'/change_password');
    }
    
    
    //public function usersList()
    //{
		//$user_id = \Auth::user()->id;
        //$user = DB::table('users')->where('id', $user_id)->get();
		//$pejabat = DB::table('pejabat')->where('user_id', $user_id)->first();
        //$users = DB::table('users')->select(
						//DB::raw('users.id,users.desa_id,users.name,users.nik,users.email,users.phone,users.status,users.status_chat,users.telah_diapprove
														//,pendaftar.name AS didaftarkan,	pengaturan.approve_1 as papprove_1,
														//pengaturan.approve_2 as papprove_2,
														//pengaturan.approve_3 as papprove_3,
														//pengaturan.approve_4 as papprove_4,
														//pengaturan.approve_5 as papprove_5,
														//pengaturan.approve_6 as papprove_6,
														//pengaturan.nama_table
						//'))	
						//->leftJoin('pengaturan', 'pengaturan.desa_id', '=', 'users.desa_id')
						//->leftJoin('users as pendaftar', 'pendaftar.id', '=', 'users.register_by')
						//->where('pengaturan.nama_table','users')
						//->where('users.desa_id',$pejabat->desa_id)
						////->orWhere('users.register_by', \Auth::user()->id)
						////->whereRaw('(pengaturan.nama_table = "users" AND users.desa_id = ?) OR users.register_by=?', [$pejabat->desa_id,\Auth::user()->id])//
						//->groupBy('users.id')
						//;
        //$query2 = datatables()->of($users);
        
        //return $query2
					//->addColumn('action', 'users.actions')
                    //->addColumn('validasi_data', 'users.validasi')
					//->rawColumns(['belum_approve'])
                    //->addColumn('belum_approve', function($arr){
						//$belum_approve = array();
						//$sudah_approve = array();
						//for($i=1;$i<=6;$i++){
							//if(empty($arr->{"papprove_".$i})){
								////tidak perlu approve
							//} else {
								////perlu approve
								//if(empty($arr->{"approve_".$i})){
									////belum diapprove
									//$belum_approve[] = strtoupper($arr->{"papprove_".$i});
								//} else {
									////sudah diapprove
									//$sudah_approve[] = strtoupper($arr->{"papprove_".$i});
								//}
							//}
						//}
						//return implode(", ",$belum_approve);
					//})
                    //->addColumn('sudah_approve', function($arr){
						//$a = explode(", ",$arr->telah_diapprove);
						//$list_jabatan = array();
						//foreach($a as $jabatan_nama){
							//if(!empty($jabatan_nama)){
								//$o = explode(" : ",$jabatan_nama);
								//$jabatan = $o[0];
								//$list_jabatan[] = $jabatan;
							//}
						//}
						//return implode(", ",$list_jabatan);
						
					//})
                    
                    //->escapeColumns([])->toJson();
    //}
    //public function export_excel()
    //{
        //// $headings = [
        ////     'id', 
        ////     'field1', 
        ////     'field2', 
        //// ];
        //// $date=Carbon::now();
        //// return (new UserExport($date,$headings))->download($date.'_user.xlsx');
        //return Excel::download(new UserExport, 'user.xlsx');
    //}
    //public function import_excel(Request $request) 
    //{
        //// validasi
        //$this->validate($request, [
            //'file' => 'required|mimes:csv,xls,xlsx'
        //]);
 
        //// menangkap file excel
        //$file = $request->file('file');
 
        //// membuat nama file unik
        //$nama_file = rand().$file->getClientOriginalName();
 
        //// upload ke folder file_siswa di dalam folder public
        //$file->move('file_user',$nama_file);
 
        //// import data
        //Excel::import(new UserImport, public_path('/file_user/'.$nama_file));
 
        //// notifikasi dengan session
        //Session::flash('sukses','Data User Berhasil DiImport!');
 
        //// alihkan halaman kembali
        //return redirect(route('users.index'));
    //}
}
