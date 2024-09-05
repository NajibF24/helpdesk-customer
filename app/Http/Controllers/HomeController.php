<?php

namespace App\Http\Controllers;

use App\Models\GoodsDetail;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

	public function index()
    {
		////INI MENGUPDATE DATA SEBELUMNYA SUPAYA CONTACT ID SEMUA TERCATAT DI TICKET
		//$tickets = DB::table('ticket')->get();
		//foreach($tickets as $t) {
			//$contact_id = DB::table('users')->where('id',$t->created_by)->value('person');
			//if($contact_id) {
				//$contact_id = DB::table('ticket')->where('id',$t->id)->update(['created_by_contact'=>$contact_id]);
			//}
		//}
		////INI MENGUPDATE TOKEN notification_message yang masih kosong
		//$notifs = DB::table('notification_message')->whereNull('token')->get();
		//foreach($notifs as $nm) {
				//DB::table('notification_message')->where('id',$nm->id)->update(['token' => generateRandomString(40)]);
		//}

        //return "oke";die;
        $title = "Home";

        //$id=127;
        //$ref_id = env('PREFIX_INCIDENT_MANAGEMENT')."-".str_pad($id,env('TICKET_PADDING'),"0",STR_PAD_LEFT);

        //echo $ref_id;
        //die();

        return view('home')
        ->with('title', $title);
    }

    public function openApps($apps = "") {
		if($apps == "ngs_system") {
			$url = env('NABATI_PORTAL_URL');
		}
		if($apps == "ngs_portal") {
			$url = env('GRP_HELPDESK_URL');
		}
		if(!empty($url)) {
			//create token
			$token = $this->generateRandomString(60);
			date_default_timezone_set('Asia/Jakarta');
			DB::table('users')->where('id',Auth::user()->id)->update(['autologin_token'=>$token,'autologin_token_expire'=>date("Y-m-d H:i:s", strtotime("+1 hours"))]);
			echo $url."/onetime_login_token/".$token;
			return redirect($url."/onetime_login_token/".$token);
		} else {
			echo "Application not found";
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

    public function dashboard_created_resolved_reload(Request $request)
    {
		$data_created = DB::table('ticket')
					->where('ticket.created_by',Auth::user()->id)
					//->whereIn('ticket.status',['Closed'])
					->whereBetween('ticket.created_at', [$_GET['start_date'], $_GET['end_date']])
					->groupBy('date')
					->orderBy('date', 'DESC')
					->get(array(
						DB::raw('Date(created_at) as date'),
						DB::raw('COUNT(*) as "value"')
					))
					;
		$data_resolved = DB::table('ticket')
					->where('ticket.created_by',Auth::user()->id)
					->join('ticket_assignment_log', 'ticket_assignment_log.ticket_id', '=', 'ticket.id')
					->whereIn('ticket.status',['Resolved'])
					->where('ticket_assignment_log.status','Resolved')
					->whereBetween('ticket.created_at', [$_GET['start_date'], $_GET['end_date']])
					->groupBy('date')
					->orderBy('date', 'DESC')
					->get(array(
						DB::raw('Date(ticket_assignment_log.created_at) as date'),
						DB::raw('COUNT(*) as "value"')
					))
					;
		//$new_data = [];
		//foreach($data_created as $d) {
			//$new_data_created[] = ['date'=>$d->date,'value'=>$d->value];
		//}
        $json = [
            'data_created' => $data_created,
            'data_resolved' => $data_resolved
        ];

        return response()->json($json);
    }
    public function activity_stream()
    {
        return view('activity_stream');
    }

    public function dashboard()
    {
        $title = "Dashboard";

        return view('dashboard')
        ->with('title', $title);
    }

	public static function getlog() {
		$res = file_get_contents("../storage/logs/laravel.log");
		echo $res;
	}

    public function dashboard_reload(Request $request)
    {
        return view('dashboard_reload')->with('request',$request);
    }

    public function dashboard_chart_reload(Request $request)
    {
		$map_color = [
			'Open' => '#76CDE1',
			'On Progress' => '#FEAC3A',
			'Resolved' => '#FD5852',
			'Closed' => '#00774A'
		];
        $list_status = ['Open','On Progress','Resolved','Closed'];
        $list_status_with_reopen = ['Open','Re-Open','On Progress','Resolved','Closed'];
        $arr_priority =[];
        $query = DB::table('ticket')
                ->whereIn('ticket.status',$list_status_with_reopen)
                ->where('finalclass',$request->query('type'));

		if ($request->query('requester')) {
			//TODO : DISINI HARUS DICEK KEMBALI REQUESTER BERISI USER YG LOGIN DAN BAWAHANNYA,
			//DI LUAR ITU TIDAK BOLEH MASUK
			//yang difilter adalah user sebagai requester atau sebagai creator
			$requester_arr = explode("|", $request->query('requester'));
			$requester_arr2 = array_merge($requester_arr,$requester_arr);
			$arr_param = [];
			for($i=0;$i<count($requester_arr);$i++) {
				$arr_param[] = "?";
			}
			$str_param = implode(",",$arr_param);

			$query->whereRaw(' (created_by_contact IN ('.$str_param.') OR requester IN ('.$str_param.')) ',$requester_arr2);
		} else {
			//tidak ada parameter requester, maka default ke dirinya sendiri
			$query->whereRaw(' (created_by_contact = ? OR requester = ?) ',[Auth::user()->person,Auth::user()->person]);
		}


		if (validateDate($request->query('start_date')) && validateDate($request->query('end_date'))) {
			$query->whereRaw('DATE(ticket.created_at) BETWEEN ? AND ?', [$request->query('start_date'), $request->query('end_date')]);
		} else if (!validateDate($request->query('start_date')) && validateDate($request->query('end_date'))) {
			$query->whereRaw('DATE(ticket.created_at) <= ?',  [$request->query('end_date')]);
		}

		$total = $query->count();

        foreach($list_status as $p) {
            $query = DB::table('ticket')
                                        //->where('status',$p)
                                        ->where('status', 'ilike', '%'.$p.'%') //dgn pakai like Re-Open masuk ke Open jadi lebih praktis tidak usah pakai if
                                        ->where('finalclass',$request->query('type'));

			if ($request->query('requester')) {
				//TODO : DISINI HARUS DICEK KEMBALI REQUESTER BERISI USER YG LOGIN DAN BAWAHANNYA,
				//DI LUAR ITU TIDAK BOLEH MASUK
				//yang difilter adalah user sebagai requester atau sebagai creator
				$requester_arr = explode("|", $request->query('requester'));
				$requester_arr2 = array_merge($requester_arr,$requester_arr);
				$arr_param = [];
				for($i=0;$i<count($requester_arr);$i++) {
					$arr_param[] = "?";
				}
				$str_param = implode(",",$arr_param);

				$query->whereRaw(' (created_by_contact IN ('.$str_param.') OR requester IN ('.$str_param.')) ',$requester_arr2);
			} else {
				//tidak ada parameter requester, maka default ke dirinya sendiri
				$query->whereRaw(' (created_by_contact = ? OR requester = ?) ',[Auth::user()->person,Auth::user()->person]);
			}

			if (validateDate($request->query('start_date')) && validateDate($request->query('end_date'))) {
				$query->whereRaw('DATE(ticket.created_at) BETWEEN ? AND ?', [$request->query('start_date'), $request->query('end_date')]);
			} else if (!validateDate($request->query('start_date')) && validateDate($request->query('end_date'))) {
				$query->whereRaw('DATE(ticket.created_at) <= ?',  [$request->query('end_date')]);
			}

			$count = $query->count();
            if($count) {
                $arr_priority[] = ['name'=>$p,'y'=>$count,'color'=>$map_color[$p]];
            }
        }

        $json = [
            'total' => $total,
            'data' => $arr_priority
        ];
		return response()->json($json);
// $list_status = ['new','Submit for Approval','Rejected','Waiting for User','Open','On Progress'
//                     //,'Resolve','Close','Draft'
//                 ];

//['Submit for Approval','Rejected','Waiting for User','Open',])
//->whereBetween('created_at', [$request->query('start_date'), $request->query('end_date')])
//->where('created_by',Auth::user()->id)


			//->whereIn('ticket.status',['Submit for Approval','Rejected','Waiting for User','Open',])
			//->where('priority',$p->priority)

			//->whereBetween('created_at', [$request->query('start_date'), $request->query('end_date')])

    }

    public function notification()
    {
        return view('notification');
    }

	public function messages()
    {
        return view('messages');
    }

	public function set_read_notification(Request $request){
		DB::beginTransaction();
        try {
			$data['read_at'] = date("Y-m-d H:i:s");
			DB::table('notification_message')->whereIn('id',$request['read'])->update($data);
			DB::commit();
			$response['status'] = true;
            $response['message'] = "Successfully updated notification data";
		} catch (\Exception $e) {
            DB::rollBack();
            $response['status'] = false;
            $response['message'] = $e->getMessage();
        }
		return json_encode($response);
	}

	public function getMaterialListReport(Request $request) {

		$lines = GoodsDetail::has('material')
			->whereNotNull('start_date')
			->whereNotNull('end_date')
			->when($request->filter_status == "all", function($query) {
				$query->where(function($q) {
					$q->whereDate('end_date', date('Y-m-d'))
					->orWhereDate('end_date', '<', date('Y-m-d'));
				});
			})
			->where('goods_details.created_by', Auth::user()->id)
			->with('material.material_code');

		if($request->filter_status == 'due_today') {
			$lines->whereDate('end_date', date('Y-m-d'));
		} else if($request->filter_status == 'overdue') {
			$lines->whereDate('end_date', '<', date('Y-m-d'));
		}

		return DataTables::of($lines)
			->addColumn('date_start', function($row) {
				return date('d M Y', strtotime($row->start_date));
			})
			->addColumn('date_end', function($row) {
				return date('d M Y', strtotime($row->end_date));
			})
			->addColumn('status_badge', function($row) {
				if($row->end_date == date('Y-m-d')) {
					return "<div class='badge badge-warning'>Due Today</div>";
				}

				if($row->end_date < date('Y-m-d')) {
					return "<div class='badge badge-danger'>Overdue</div>";
				}

				return '';
			})
			->rawColumns(['status_badge'])
			->make();
	}
}
