<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;
use Redirect,Config;
use Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use App\Models\ScanWorkflow;
use App\Models\Workflow;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use App\Http\Helpers\GeneralHelpers;
use App\Mail\EmailSend;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\isEmpty;

class CrudController extends AppBaseController
{

	private $breadcrumb = array('histori'=>'dashboard','topik_forum'=>'dashboard');
    public function __construct()
    {
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        $this->middleware('auth');
    }

	public function tesemail($email,$title,$message) {
		//Mail::to($email)->send(new \App\Mail\EmailSend(str_replace("_"," ",$title),str_replace("_"," ",$message)));

        $data['ticket'] = DB::table('ticket')->first();
        $data['ticket']->message = str_replace("_"," ",$message);
        $data['statuses'] = DB::table('ticket_approval')->where('ticket_id', $data['ticket']->id)->get();
		$data['message'] = str_replace("_"," ",$message);
		$data['title'] = str_replace("_"," ",$title);

		Mail::to($email)->send(new \App\Mail\EmailSend(str_replace("_"," ",$title),$data));

		echo "oke";
		die;
	}

	public function listItem(Request $request, $type,$param1="") {
		accessv($type,'list');

		$table = $type;
		//$list_item = DB::table($table)->get();

		$column = $this->getArrayColumnNameFromTable($table);
		$status_ticket = "";
		$ticket_type = "";
        $state = "";
		$requester = "";
		$start_date = "";
		$end_date = "";

		$title = t($table);//title secara default mengacu nama table tapi tetap bisa dicustom, disini pakai fungsi helper t() di helpers.php

		if (in_array($type, array("ticket","approve_request","ticket_monitoring","draft"))) {
			$column = ["id", "Ticket Number"=>"ref","service_name" , "title", "Service Category"=>"service_category_name",  "status", "Request Date"=>"created_at","related_ticket","agent"];

			if ($request->query('status_ticket')) {
				$status_ticket = $request->query('status_ticket');
			}

			if ($request->query('ticket_type')) {
				$ticket_type = $request->query('ticket_type');
			}

            if ($request->query('state')) {
				$state = $request->query('state');
			}

			if ($request->query('requester')) {
				$requester = $request->query('requester');
			}

			if ($type == 'draft') {
				$title = "Draft Request";
			}

            if ($request->query('start_date')) {
				$start_date = $request->query('start_date');
			}

            if ($request->query('end_date')) {
				$end_date = $request->query('end_date');
			}
		}



		unset($column['id']);
		$tab = "";
		$disposal = false;
        return view('crudmodal.index')->with('column',$column)->with('table',$table)->with('tab',$tab)
            ->with('breadcrumb',$this->breadcrumb)
            ->with('title',$title)
            ->with('disposal',$disposal)
			->with('status_ticket',$status_ticket)
			->with('ticket_type',$ticket_type)
            ->with('state',$state)
            ->with('start_date',$start_date)
            ->with('end_date',$end_date)
			->with('requester',$requester);
	}

    public function listServer(Request $request,$type,$param1="")
    {
		accessv($type,'list');

		$user_id = \Auth::user()->id;

		$user = \Auth::user();

		$query = DB::table($type);
		$users = $query;
		$join_table = [];

		if(in_array($type,array('test_master_parameter_config'))){
			//$query = $query->select(
						//DB::raw($type.'.*,"'.$type.'" as nama_table,test_master_parameter.parameter as parameter
						//'))
						//->join('test_master_parameter', 'test_master_parameter.id', '=', $type.'.fid_parameter');
		}

		else if (in_array($type, array("ticket","approve_request","ticket_monitoring"))) {
			$type = "ticket";
			$query = DB::table('ticket');
			$query = $query->select(
				DB::raw('ticket.*, service.name as service_name, service_category.name as service_category_name, c1.name as agent, c.name as next_approval, contact.name as ticket_requester, "ticket" as nama_table,
							c5.name AS company, co5.name AS country')
			)
			->leftJoin('service', 'service.id', '=', 'ticket.service_id')
			->leftJoin('service_category', 'service_category.id', '=', 'ticket.servicesubcategory_id')
			->leftJoin('contact as c1', 'c1.id', '=', 'ticket.agent_id')
			->leftJoin('contact as c', 'c.id', '=', 'ticket.next_approval_id')
			->leftJoin('contact', 'contact.id', '=', 'ticket.requester')
			->leftJoin('company as c5', 'c5.id', '=', 'ticket.company')
			->leftJoin('country as co5', 'co5.code', '=', 'ticket.country');

			if (validateDate($request->query('start_date')) && validateDate($request->query('end_date'))) {
				$query->whereRaw('DATE(ticket.created_at) BETWEEN ? AND ?', [$request->query('start_date'), $request->query('end_date')]);
			} else if (!validateDate($request->query('start_date')) && validateDate($request->query('end_date'))) {
				$query->whereRaw('DATE(ticket.created_at) <= ?',  [$request->query('end_date')]);
			}

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

			if ($request->query('ticket_type') == 'incident_management'
				|| $request->query('ticket_type') == 'service_request'
				|| $request->query('ticket_type') == 'problem_request'
				) {
				$query->where('ticket.finalclass', $request->query('ticket_type'));
			}

			if ($request->query('status_ticket')) {
				$query->where('ticket.status', $request->query('status_ticket'));
			} else if ($request->query('state')) {
				if ($request->query('state') == 'due_today') {
					$query->whereIn('ticket.status',['Open','Re-Open','On Progress'])
							->whereRaw(' ((due_date >= NOW()) and  (CURRENT_DATE = DATE(due_date))) ');
				} else if ($request->query('state') == 'overdue') {
					$query->whereIn('ticket.status',['Open','Re-Open','On Progress'])
							->whereRaw('due_date < NOW()');
				} else {
					$query->where('id',-99999);//imposible value, no data return
				}
			}

			//$join_table = ['contact'];



				//if ($request->query('status_ticket') == 'unassign_ticket') {
					//$query->where('ticket.agent_id', 0);
				//} else {

				//if (strpos($request->query('status_ticket'), ',') > 0) {
					//$query->whereIn('ticket.status',['Submit for Approval','Waiting for User']);

					//if ($request->query('requester')) {
						//$query->whereIn('ticket.requester', explode("|", $request->query('requester')));
					//}

					//if ($request->query('ticket_type') == 'overdue') {
						//$query->whereRaw('ticket.created_at < DATE_SUB(CURDATE(), INTERVAL 7 DAY)');
					//} else {
						//$query->whereRaw('ticket.created_at BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND DATE_SUB(CURDATE(), INTERVAL 7 DAY)');
					//}
				//} else



					//if ($request->query('requester')) {
						//$query->whereIn('ticket.requester', explode("|", $request->query('requester')));
					//}

					//if ($request->query('ticket_type') == 'incident_management'
						//|| $request->query('ticket_type') == 'service_request') {
						//$query->where('ticket.finalclass', $request->query('ticket_type'));
					//}

		}
		else if (in_array($type, array("draft"))) {
			$type = "ticket_draft";
			$query = DB::table('ticket_draft');
			$query = $query->select(
				DB::raw('ticket_draft.*, service.name as service_name, service_category.name as service_category_name, c1.name as agent, c.name as next_approval, contact.name as ticket_requester, "ticket_draft" as nama_table,
							c5.name AS company, co5.name AS country')
			)
			->leftJoin('service', 'service.id', '=', 'ticket_draft.service_id')
			->leftJoin('service_category', 'service_category.id', '=', 'ticket_draft.servicesubcategory_id')
			->leftJoin('contact as c1', 'c1.id', '=', 'ticket_draft.agent_id')
			->leftJoin('contact as c', 'c.id', '=', 'ticket_draft.next_approval_id')
			->leftJoin('contact', 'contact.id', '=', 'ticket_draft.requester')
			->leftJoin('company as c5', 'c5.id', '=', 'ticket_draft.company')
			->leftJoin('country as co5', 'co5.code', '=', 'ticket_draft.country')
			->where('ticket_draft.created_by', Auth::user()->id);
		}
		else {
			$query = $query->select(
						DB::raw($type.'.*,"'.$type.'" as nama_table
						'));
		}


		//$query = $query->orderBy($type.'.id', 'desc');

		$query2 = datatables()->of($query);

		$sm = \DB::getDoctrineSchemaManager();
		$list_column = $sm->listTableColumns($type);
		$column = array();
		foreach ($list_column as $c) {
			$key = $c->getName();
			if($key == "foto"){
				$query2 = $query2->editColumn('foto', '<img src="{{ url("/") }}/uploads/{{ $foto }}" style="height:60px"/>');
			}
			if($key == "icon"){
				$query2 = $query2->editColumn('icon', '<img src="{{ url("/") }}/uploads/{{ $icon }}" style="height:60px"/>');
			}
			if($key == "image"){
				$query2 = $query2->editColumn('image', '<img src="{{ url("/") }}/../../uploads/{{ $image }}" style="height:60px"/>');
			}
			if($key == "foto_utama"){
				$query2 = $query2->editColumn('foto_utama', '<img src="{{ url("/") }}/uploads/{{ $foto_utama }}" style="height:60px"/>');
			}
			if($type == "holiday" && $key == "date") {
				$query2->editColumn('date', '{{ empty($date)?"":date("d M Y",strtotime($date)) }}');
			}
		}
		$query2 = $query2->editColumn('related_ticket', '{{ empty($related_ticket)?"-":$related_ticket }}');
		$query2 = $query2->editColumn('created_at', '{{ empty($created_at)?"":date("d M Y",strtotime($created_at)) }}');
		$query2 = $query2->editColumn('updated_at', '{{ empty($updated_at)?"":date("d M Y",strtotime($updated_at)) }}');
		$query2 = $query2->editColumn('created_date', '{{ empty($created_date)?"":date("d M Y",strtotime($created_date)) }}');
		$query2 = $query2->editColumn('modified_date', '{{ empty($created_date)?"":date("d M Y",strtotime($modified_date)) }}');

		//$query2 = $query2->editColumn('status', 'statusHtml($status)');

		$query2 = $query2->addColumn('nama_table', $type);
		$query2->editColumn('status', function($data){
						return statusHtml(@$data->status);
					});
		$list_sample_table = array();
		$disposal = false;
        //DB::enableQueryLog();
		$ret = $query2->addColumn('action', 'crudmodal.actions')
					//->filter(function ($query) use ($request,$type,$join_table,$list_sample_table,$disposal) {
						//$query = $this->searchDataTable($query,$request,$type,$join_table,$list_sample_table,$disposal);

					//})
					->escapeColumns([])->toJson();
        //dd(DB::getQueryLog());

       return $ret;
    }

    public function edit(Request $req,$id,$type,$param1="")
    {
		accessv($type,'edit');
		$table = $type;

		if(in_array($type,["incident_management","service_request","problem"])) {

			return redirect(URL('/').'/approve-request/'.$id);
		}
		//$organization = DB::table('organization_level')->pluck('name','name')->toArray();
		//if(!in_array($type,["team","person","employee","incident_management"]+$organization)) {
			//$berkas = DB::table($table)->where('id', $id)->first();
			//if (empty($berkas)) {
				//Flash::error('Berkas tidak ditemukan');
				//return redirect(route('list', ['type' => $type]));
			//}
		//}
		//$column = $this->getArrayColumNameFromTableForCreateAndEdit($table);

		//$column = $this->setUploadFieldAndEditorField($column);


		//$menu_relation = [];

		//if ($table == "incident_management") {
			//$berkas = DB::table('ticket')->where('id', $id)->first();
			//$menu_relation = ["asset","contact","child_incident"];
			//$column = $this->selectbox_relation($column, 'company',"select", 'company','id','name');
			//$column = $this->selectbox_relation($column, 'caller',"select_and_add", 'contact','id','name');
			//$column = $this->selectbox_choice($column, 'origin', ['Email', 'Monitoring', 'Phone', 'Portal']);
			//$column['title'] = array('type_data' => 'String');
			//$column['description'] = array('type_data' => 'Text');
			//$column = $this->selectbox_relation($column, 'category', "select_and_add", 'service_category', 'id', 'name');
			//$column = $this->selectbox_relation($column, 'request', "select_and_add", 'service', 'id', 'name', true, 'request_type', 'Incident');
			//// $column = $this->selectbox_choice($column, 'impact', ['A Department', 'A Service', 'A Person']);
			//// $column = $this->selectbox_choice($column, 'urgency', ['Critical', 'High', 'Medium', 'Low']);
			//$column = $this->selectbox_relation($column, 'parent_incident', "select_and_add", 'ticket', 'id', 'ref', true, 'finalclass', 'incident_management');
			//$column = $this->selectbox_relation($column, 'parent_problem_id', "select_and_add", 'ticket', 'id', 'ref', true, 'finalclass', 'problem_management');
			//// $column = $this->selectbox_relation($column, 'parent_change', "select_and_add", 'ticket', 'id', 'ref', true, 'finalclass', 'change');
			//$column['upload_file'] = array('type_date' => 'Text');
			//$column = $this->setUploadFieldAndEditorField($column);
			//$column['private_log'] = array('type_data' => 'Text');
			//$column['public_log'] = array('type_data' => 'Text');
		//}

		////cancel button
		//$show_cancel = true;
		//if(in_array($table,["features","general","contact_us","about"])) {
			////stay on page edit
			//$show_cancel = false;//prevent user back to list, because it only form no list
		//}

        //$title = "Edit ".t($table);

        //return view('crudmodal.edit')->with('title',$title)->with('menu_relation',$menu_relation)->with('type',$type)->with('berkas', $berkas)->with('row', $berkas)->with('spmampu', $berkas)->with('column',$column)->with('breadcrumb',$this->breadcrumb)->with('show_cancel',$show_cancel);
    }

    public function choose_incident(Request $req)
    {
		return view('crudmodal.choose_incident')->with('title','Incident Request');
	}
    public function create(Request $req,$type,$modal="",$select_target="",$param1="")
    {
		accessv($type,'create');
		$table = $type;

		$column = $this->getArrayColumNameFromTableForCreateAndEdit($table);

		$column = $this->setUploadFieldAndEditorField($column);

		$menu_relation = [];
		if ($table == "incident") {
			$service_id = $req->query('request');
			$originator = DB::table('contact')->orderBy('name', 'ASC')->where('type','Employee');
			//$request_management = DB::table('request_management')->where('request_name',$service_id)->first();
			$service = DB::table('service')->where('id', $service_id)->first();
			$breadcumb = [
				[
					'name' => 'Request An Incident',
					'url' => 'request-incident/incident-catalog/2'
				],
				[
					'name' => 'Create Incident '.$service->name,
					'url' => 'create/incident?category='.$req->query('category').'&request='.$req->query('request').'&target='.$req->query('target')
				]
			];

			$person_id = Auth::user()->person;
			 //get from contact
			 if($person_id) {
				$contact = DB::table('contact')->where('id',$person_id)->first();
				$originator = $originator->where('id',"!=", $person_id);
				//cek lokasi dan company
				//yang cocok di request management
				$request_management = getRequestManagement($service_id,$contact);
				//$request_management = DB::table('request_management')
										//->where('location',$contact->location)
										//->where('company',$contact->company)
										//->where('request_name',$service_id)->first();
				//var_dump($service_id);
				//var_dump($contact);
			} else {
				return view('crudmodal.incident_not_found')->with('message',"Contact account not found")->with('title','Create Incident')->with('type',$type)->with('column',$column)->with('breadcrumb',$this->breadcrumb);
			}

			$originator = $originator->get();

			if(empty($request_management)) {
				return view('crudmodal.incident_not_found')->with('message',"Request management not found in your location company")->with('title','Create Incident')->with('type',$type)->with('column',$column)->with('breadcrumb',$this->breadcrumb);
			} else {

				$is_ticket_resolved_not_closed_exist = DB::table('ticket')
														->where(['created_by'=>Auth::user()->id,
																'status' => 'Resolved',])->first();
				if($is_ticket_resolved_not_closed_exist) {
					Session::flash('warning', 'Resolved ticket need closed');
					return redirect()->back();
					//return view('crudmodal.status_resolved_need_closed')->with('title','Create Incident')->with('type',$type)->with('column',$column)->with('breadcrumb',$this->breadcrumb);
				}
				return view('crudmodal.create_incident')
							->with('request_management',$request_management)
							->with('breadcumb', $breadcumb)
							->with('title','Create Incident')
							->with('originator', $originator)
							->with('requester', Auth::user()->name)
							->with('category', $req->query('category'))
							->with('service', $service)
							->with('approval', "")
							->with('type',$type)
							->with('column',$column)
							->with('breadcrumb',$this->breadcrumb);
			}
			//return view('crudmodal.create_incident')->with('request_management',$request_management)->with('title','Create Incident')->with('type',$type)->with('column',$column)->with('breadcrumb',$this->breadcrumb)->with('originator', $originator);
		}
		if ($table == "service") {

			$service_id = $req->query('request');

			//service id ini harus diconvert ke request_management_id
			//sekarang get saja dulu yang pertama dari request_management
			$request_management = DB::table('request_management')->where('request_name',$service_id)->first();
			$originator = DB::table('contact')->orderBy('name', 'ASC')->where('type','Employee');
			$person_id = Auth::user()->person;
			 //get from contact
			if($person_id) {
				$contact = DB::table('contact')->where('id',$person_id)->first();
				$originator = $originator->where('id',"!=", $person_id);
				//cek lokasi dan company
				//yang cocok di request management
				$request_management = getRequestManagement($service_id,$contact);
				//$request_management = DB::table('request_management')
										//->where('location',$contact->location)
										//->where('company',$contact->company)
										//->where('request_name',$service_id)->first();

			} else {
				return view('crudmodal.incident_not_found')->with('message',"Contact account not found")->with('title','Create Incident')->with('type',$type)->with('column',$column)->with('breadcrumb',$this->breadcrumb);
			}

			if(empty($request_management)) {
				return view('crudmodal.incident_not_found')->with('message',"Request management not found in your location company")->with('title','Create Incident')->with('type',$type)->with('column',$column)->with('breadcrumb',$this->breadcrumb);
			}


			$is_ticket_resolved_not_closed_exist = DB::table('ticket')
													->where(['created_by'=>Auth::user()->id,
															'status' => 'Resolved',])->first();
			if($is_ticket_resolved_not_closed_exist) {
				Session::flash('warning', 'Resolved ticket need closed');
				return redirect()->back();
				//return view('crudmodal.status_resolved_need_closed')->with('title','Create Service')->with('type',$type)->with('column',$column)->with('breadcrumb',$this->breadcrumb);
			}
			//else {
				//return view('crudmodal.create_incident')->with('request_management',$request_management)->with('title','Create Service')->with('type',$type)->with('column',$column)->with('breadcrumb',$this->breadcrumb);
			//}

			$service_id = $req->query('request');
			//$request_management = DB::table('request_management')->where('request_name',$service_id)->first();
			$originator = $originator->get();
			$service = DB::table('service')->where('id', $service_id)->first();
			$breadcumb = [
				[
					'name' => 'Request A Service',
					'url' => 'request-service/service-catalog/2'
				],
				[
					'name' => 'Create Service '.$service->name,
					'url' => 'create/service?category='.$req->query('category').'&request='.$req->query('request').'&target='.$req->query('target')
				]
			];
			$title = "Create Service ".$service->name;

			try {
				$contact_login = DB::table('contact')->select('contact.id','contact.job_title','job_title.parent')->leftJoin('job_title', 'contact.job_title', '=', 'job_title.id')->where('contact.id', Auth::user()->person)->first();
				$approval = DB::table('contact')->where('job_title', $contact_login->parent)->first();
				$approval_id = $approval->id;
			} catch (Exception $e) {
				$approval_id = "";
			}

			return view('service-catalog.create')
				->with('request_management',$request_management)
				->with('breadcumb', $breadcumb)
				->with('title', $title)
				->with('originator', $originator)
				->with('requester', Auth::user()->name)
				->with('category', $req->query('category'))
				->with('approval', $approval_id)
				->with('service', $service)
				->with('type',$type)
				->with('column',$column)
				->with('breadcrumb',$this->breadcrumb);

		}
		if ($table == "draft") {
			$token = $req->query('id');
			$ticket_draft = DB::table('ticket_draft')->where('token',$token)->first();
			$id = $ticket_draft->id;
			//var_dump($ticket_draft->request_management);
			//var_dump($ticket_draft);


			//$request_management = DB::table('request_management')->where('id',$ticket_draft->request_management)->first();
			//var_dump($request_management);
			//$service_id = $request_management->request_name;

			//$service_id = $req->query('request');
			$service_id = $ticket_draft->service_id;
			//service id ini harus diconvert ke request_management_id
			//sekarang get saja dulu yang pertama dari request_management
			//$request_management = DB::table('request_management')->where('request_name',$service_id)->first();
			$person_id = Auth::user()->person;
			 //get from contact
			if($person_id) {

				$contact = DB::table('contact')->where('id',$person_id)->first();

				//var_dump($contact);
				//cek lokasi dan company
				//yang cocok di request management
				$request_management = getRequestManagement($ticket_draft->service_id,$contact);
				//$request_management = DB::table('request_management')
										//->where('location',$contact->location)
										//->where('company',$contact->company)
										//->where('request_name',$service_id)->first();
				//var_dump($request_management);

			} else {
				return view('crudmodal.incident_not_found')->with('message',"Contact account not found")->with('title','Create Incident')->with('type',$type)->with('column',$column)->with('breadcrumb',$this->breadcrumb);
			}

			if(empty($request_management)) {
				return view('crudmodal.incident_not_found')->with('message',"Request management not found in your location company")->with('title','Create Incident')->with('type',$type)->with('column',$column)->with('breadcrumb',$this->breadcrumb);
			}
			//else {
				//return view('crudmodal.create_incident')->with('request_management',$request_management)->with('title','Create Service')->with('type',$type)->with('column',$column)->with('breadcrumb',$this->breadcrumb);
			//}

			//$service_id = $req->query('request');
			//$request_management = DB::table('request_management')->where('request_name',$service_id)->first();
			$originator = DB::table('contact')->where('type','Employee')->get();
			$service = DB::table('service')->where('id', $service_id)->first();

			if ($ticket_draft->finalclass == 'service_request') {
				$breadcumb = [
					[
						'name' => 'Draft',
						'url' => 'myDraft'
					],
					[
						'name' => 'Create Service '.$service->name,
						'url' => 'create/draft?id='.$ticket_draft->token
					]
				];
				$title = "Create Service ".$service->name;
			}

			if ($ticket_draft->finalclass == 'incident_management') {
				$breadcumb = [
					[
						'name' => 'Draft',
						'url' => 'myDraft'
					],
					[
						'name' => 'Create Incident '.$service->name,
						'url' => 'create/draft?id='.$ticket_draft->token
					]
				];
				$title = "Create Incident ".$service->name;
			}

			try {
				$contact_login = DB::table('contact')->select('contact.id','contact.job_title','job_title.parent')->leftJoin('job_title', 'contact.job_title', '=', 'job_title.id')->where('contact.id', Auth::user()->person)->first();
				$approval = DB::table('contact')->where('job_title', $contact_login->parent)->first();
				$approval_id = $approval->id;
			} catch (Exception $e) {
				$approval_id = "";
			}

			return view('service-catalog.create')
				->with('request_management',$request_management)
				->with('breadcumb', $breadcumb)
				->with('title', $title)
				->with('originator', $originator)
				->with('requester', Auth::user()->name)
				->with('category', $req->query('category'))
				->with('approval', $approval_id)
				->with('service', $service)
				->with('type',$type)
				->with('column',$column)
				->with('breadcrumb',$this->breadcrumb)
				->with('ticket_draft',$ticket_draft);

		}
        //var_dump($column);
        $title = "Create ".t($table);
        if($modal) {
			return view('crudmodal.create_modal')->with('select_target',$select_target)->with('menu_relation',$menu_relation)->with('type',$type)->with('column',$column)->with('breadcrumb',$this->breadcrumb);
		} else {
			return view('crudmodal.create')->with('title',$title)->with('menu_relation',$menu_relation)->with('type',$type)->with('column',$column)->with('breadcrumb',$this->breadcrumb);
		}
    }

    public function store($type,Request $request,$modal="")
    {

		$contact = Auth::user()->contact;

		accessv($type,'create');
		DB::beginTransaction();
		try {
			$user_id = \Auth::user()->id;
			$user_name = \Auth::user()->name;

			$input = $request->all();

			$request->validate([
				'file' => 'file|mimes:jpg,jpeg,png,gif,doc,docx,pdf,xls,xlsx,txt,pptx', // Max size in kilobytes (2 MB)
			]);

			$table = $type;
			unset($input['client_token']);//kadang2 ikut masuk
			unset($input['_token']);
			unset($input['input_call']);

			if (in_array($table, ["position"])) {
				$input['created_date'] = date("Y-m-d H:i:s");
				$input['created_by'] = $user_name;
				$input['modified_date'] = date("Y-m-d H:i:s");
				$input['modified_by'] = $user_name;
			} else {
				$input['created_at'] = date("Y-m-d H:i:s");
				$input['created_by'] = $user_name;
				$input['updated_at'] = date("Y-m-d H:i:s");
				$input['updated_by'] = $user_name;
			}

			$input = $this->processUpload($request, $input);

			$input2 = $input;

			//clear semua input relasi karena jalur pengolahannya berbeda
			foreach($input as $key => $value) {
				if (strpos($key, 'lnk-') !== false) {
					unset($input[$key]);
				}
			}
			unset($input['check-all']);
			unset($input['check_item']);

			$input_main = $input;

			$service_id = $input['service_id'] ?? 0;
			$requester = $input['request_for'] == "other" ? (int) $input['requester'] : Auth::user()->person;
			$contact_id = $requester;
			$contact = DB::table('contact')->where('id',$contact_id)->first();
			//cek lokasi dan company
			//yang cocok di request management
			$request_management = getRequestManagement($service_id,$contact);

			if(empty($request_management)) {
				echo json_encode(["success"=>false,"message"=>"Sorry, Incident Request Not Avaliable for this Requester"]);
				die;
			}

			$form_builder = DB::table('form_builder')->where('id',$request_management->form_builder)->first();

			if($request_management->request_type == "Service Request") {
				$finalclass="service_request";
			} else {
				$finalclass="incident_management";
			}

			$service_id = $request_management->request_name;
			//$service = DB::table('service')->where('id',$request_management->service)->first();
			$service_category_id = DB::table('lnkservicetoservice_category')->where('service_id',$service_id)->value('service_category_id');

			$list_filename = array();
			$list_file_url = [];
			$file = $request->file('file');

			if($request->hasFile('file'))
			{
				$filename = izrand(5).'-'.removeSpecialCharacters($file->getClientOriginalName());
				handleUpload($file,$filename,'/upload',$file->getPathName());
				$list_filename[] = $filename;
				$list_file_url[] = URL('/').'/uploads/'.$filename;
			}
			$file = $request->file('file2');
			if($request->hasFile('file2'))
			{
				$filename = izrand(5).'-'.removeSpecialCharacters($file->getClientOriginalName());
				handleUpload($file,$filename,'/upload',$file->getPathName());
				$list_filename[] = $filename;
				$list_file_url[] = URL('/').'/uploads/'.$filename;
			}
			$file = $request->file('file3');
			if($request->hasFile('file3'))
			{
				$filename = izrand(5).'-'.removeSpecialCharacters($file->getClientOriginalName());
				handleUpload($file,$filename,'/upload',$file->getPathName());
				$list_filename[] = $filename;
				$list_file_url[] = URL('/').'/uploads/'.$filename;
			}

			$files = implode(",",$list_filename);
			$files_url = implode(",",$list_file_url);


			date_default_timezone_set('Asia/Jakarta');

			//INSERT DI MAIN TABLE

			$table_submit = "ticket";
			if($input['submit_type'] == "draft") {
				$table_submit = "ticket_draft";
			}

			$form_builder_json = "";
			if(!empty($form_builder->json)) {
				$form_builder_list_field = json_decode((empty($form_builder->json)?"":$form_builder->json));
				//foreach($form_builder2 as $f) {
				for($i =0;$i<count($form_builder_list_field);$i++) {
					if(str_contains($form_builder_list_field[$i]->type, 'data_grid')) {
						//extract example 30 from data_grid30
						preg_match_all('!\d+!', $form_builder_list_field[$i]->type, $form_object_id);

						$form_builder_object = DB::table('form_builder_object')->where('id',$form_object_id)->first();
						$form_builder_list_field[$i]->rows = $form_builder_object->rows ?? 0;
						$form_builder_list_field[$i]->columns = $form_builder_object->columns ?? 0;
						$form_builder_list_field[$i]->header = $form_builder_object->header ?? "";

					}
				}
				//rewrite
				$form_builder_json = json_encode($form_builder_list_field);
			}
			//var_dump($form_builder_json);die;

			$sla = DB::table('sla')->where('id',$request_management->SLA_delivery ?? null)->first();
			$sla_json = empty($sla) ? null : json_encode($sla);

			$id = DB::table($table_submit)->insertGetId(
				[
					'sla_json' => $sla_json,
					'status' => ($input['submit_type'] == "draft")?'Draft':'Open',
					'org_id' => $contact->organization ?? 0,
					'caller_id' => $input_main['caller'] ?? 0,
					'title' => $input_main['title'] ?? '-',
					'files' => $files,
					'files_url' => $files_url,
					'description' => $input_main['description'] ?? '-',
					'finalclass' => $finalclass,
					'service_id' => $service_id ?? 0,
					'servicesubcategory_id' => $service_category_id ?? 0,
					'request_management'=>$request_management->id,
					'parent_incident_id' => $input_main['parent_incident'] ?? 0,
					'parent_problem_id' => $input_main['parent_problem_id'] ?? 0,
					'parent_change_id' => $input_main['parent_change'] ?? 0,
					'public_log' => $input_main['public_log'] ?? '-',
					'created_by' => Auth::user()->id,
					'created_by_contact' => Auth::user()->person,
					'updated_by' => Auth::user()->id,
					'created_at' => date("Y-m-d H:i:s"),
					'updated_at' => date("Y-m-d H:i:s"),
					'country' => $contact->country ?? 0,
					'company' => $contact->company ?? 0,
					'token' => generateRandomString(40),
					'data_json'=>json_encode($input),
					'form_data_json'=>$input_main['form_data_json'] ?? '-',
					'form_builder_json'=> (empty($form_builder_json)?"":$form_builder_json),
					'form_builder'=> $form_builder->id ?? null,
					'requester' => $input['request_for'] == "other" ? (int) $input['requester'] : Auth::user()->person,
					'SLA_status' => 'Active',
				]
			);

			if($input['submit_type'] == "draft") {
				$redirect = URL('/').'/myDraft';
				echo json_encode(["success"=>true,"message"=>"success","redirect"=>$redirect]);
				die;
			}
			if($input['submit_type'] != "draft") {

				DB::table('ticket')
					->where('id', $id)
					->update([	'ref' => $this->set_ref_id($finalclass, $id),
								]);

				//ASSIGNMENT
				$ticket = DB::table('ticket')->where('id',$id)->first();

				$next_agent = null;

				//assignment_system menghandle roundrobin, loadbalace, random
				//dan handling cuti,
				//hasil returnya bisa 1. dapet agent, 2. pending on leave, atau 3. tidak dapat agent
				$ret_val = assignment_system($ticket,$request_management,$next_agent,"approval");//submit incident sama flowny dgn approval
				if($ret_val['status'] == 'ok') {
					$agent_id = $ret_val['agent_id'];
					$team_id = $ret_val['team_id'];
					$tier = $ret_val['tier'];
				} else if($ret_val['status'] == 'pending leave') {
					$agent_id = $ret_val['agent_id'];
					$team_id = $ret_val['team_id'];
					$tier = $ret_val['tier'];


					//flow baru grp tidak ada pending leave
					//saat form incident disubmit tiket tetap tersubmit dan tetap berjalan,
					//dimana belum ada agent yang diassign
					//maka kemudian admin yang harus assign manual ke agent tertentu

					DB::table('ticket_log')->insertGetId(
						[
							'message' => 'There is no agent that is available for this ticket right now because the agent is On Leave. Contact Administrator to make this ticket is assigned to available Agent.',
							'ticket_id' => $id,
							'created_at' => date("Y-m-d H:i:s"),
							'created_by' => -1,
						]
					);

					$redirect = URL('/').'/myIncidents';

					echo json_encode(["success" => true, "warning" =>true, "redirect"=>$redirect, 'message' => 'There is no agent that is available for this ticket right now because the agent is On Leave. <b>Please contact Administrator to make this ticket is assigned to available Agent. </b>']);
					die;

				} else {
					//no agent can handle
					//DELETE TICKET NOT USED
					$ticket = DB::table('ticket')->where('id',$id)->delete();

					echo json_encode(["success" => false, 'message' => 'There is no agent that is available for this ticket right now. <b>Please contact Administrator to make this ticket is assigned to available Agent. </b>']);
					die;
				}

				//END ASSGINMENT

				$assign_time = getAssignTime($agent_id);

				DB::table('ticket_assign_time')->insertGetId(
					[
						'ticket_id' => $id,
						'assign_time' => $assign_time,
						'team_id' => $team_id,
						'agent_id'=>$agent_id,
					]
				);

				DB::table('ticket_assignment_log')->insertGetId(
					[
						'ticket_id' => $id,
						'team_id' => $team_id,
						'agent_id'=>$agent_id,
						'created_at' => date("Y-m-d H:i:s"),
						'updated_at' => date("Y-m-d H:i:s"),
						'created_by' => Auth::user()->id,
						'updated_by' => Auth::user()->id,
						'status'=>'Assignment',
						'tier'=>$tier,
					]
				);
			}
			foreach($input as $key => $value) {
				if(!is_array($value)) {
					DB::table('form_builder_data')->insertGetId(
						[
							'ticket_id' => $id,
							//'object_id' => ,
							'value' => $value,
							'name'=>$key,
						]
					);
				}
			}

			$input['id'] = $id;

			$ticket = DB::table('ticket')
							->where('id', $id)->first();

			$escalation_time_list = explode(",",$request_management->escalation_time);
			$escalation_unit_list = explode(",",$request_management->escalation_unit);

			DB::table('ticket')
				->where('id', $id)
				->update([
							'team_id' => $team_id,
							'agent_id' => $agent_id,
							'assign_time' => $assign_time,
							'current_tier' => $tier,
							'ticket_open_time'=>$assign_time,//perhitungan awal agent terhadap first response dan time resolved
							]);

			$next_tier_index = $tier - 1;// tier mulai dari 1, next_tier_index mulai dari 0

			if(!empty($escalation_unit_list[$next_tier_index])) {

				$escalation_date = checkEscalationDate($id,$assign_time,($escalation_time_list[$next_tier_index] ?? null),($escalation_unit_list[$next_tier_index] ?? null));

				DB::table('ticket')
					->where('id', $id)
					->update([
								'escalation_date' =>$escalation_date,
								]);

				DB::table('schedule_execution')->insertGetId(
					[
						'action' => 'Auto Escalation',
						'recipient' => 0,
						'title' => "",
						'data' => "",
						'ref_id' => $id,
						'type' => 'ticket',
						'status' => 'Pending',
						'execution_time'=> $escalation_date,//Days
						'current_tier' => $tier,
						'created_at' => date("Y-m-d H:i:s"),
						'created_by' => Auth::user()->id,
					]
				);
			} else {
				//jika tidak  ada setinganya maka dinullkan saja
				DB::table('ticket')
					->where('id', $id)
					->update([
								'escalation_date' =>null,
							]);
			}

			DB::table('ticket_log')->insertGetId(
				[
					'message' => 'System created Ticket with status <b>Open</b> with type <b>Incident</b>',
					'ticket_id' => $id,
					'created_at' => date("Y-m-d H:i:s"),
					'created_by' => Auth::user()->id,
				]
			);

			$name = DB::table('contact')->where('id',$agent_id)->value('name');
			$title_notif = "".$name." Have Assign to New Ticket with Ticket Number ".ticketNumber($id)."";
			$content_notif = "<p>".$name." have assign to a new Ticket with Ticket Number ".ticketNumber($id)." </p>";
			notif_to_all_needed_contact($id,$ticket,$title_notif,$content_notif,"assign_ticket",$agent_id);

			$content_notif = "<p>".$name." have assign to a new Ticket with Ticket Number ".ticketNumber($id)." </p>
						<p>Please follow up this assignment ticket. After your assign ticket is done, you can mark this ticket as Solved.
						</p>
						<p>If you cannot Solve this ticket you can Escalate this ticket</p>";
			sendNotifEmail($agent_id, $name." Have Assign to New Ticket with Ticket Number ".ticketNumber($id)."", $content_notif,"assign_ticket",$id);

			$redirect = URL('/').'/myIncidents';

			DB::commit();

			echo json_encode(["success"=>true,"message"=>"success","redirect"=>$redirect]);
			die;

		} catch (\Throwable $th) {
			$redirect = URL('/').'/myIncidents';

			Log::error($th->getMessage());

			DB::rollBack();

			echo json_encode(["success"=>false,"message"=> $th->getMessage(),"redirect"=>$redirect]);
			die;
		}
    }
    public function ticketAction(Request $request) {
		$input = $request->all();
		$action = $input['action'];
		$id = $input['id'];
		if($id) {
			$ticket = DB::table('ticket')
					->where('id', $id)->first();
		}
		if(!empty($ticket)) {
			if($action == "closed") {
				if(($ticket->requester == Auth::user()->person)
					|| ($ticket->created_by == Auth::user()->id)
				) {

				} else {
					echo "access denied";die;
				}

				if(empty($input['message'])) {
					return json_encode(['success'=>false,'message'=>'Please input the description']);
				}

				if($input['rating'] < 3 && strlen($input['message']) < 30) {
					return json_encode(['success'=>false,'message'=>'Please input minimal 30 character']);
				}

				if($input['rating'] >= 3 && strlen($input['message']) < 20) {
					return json_encode(['success'=>false,'message'=>'Please input minimal 20 character']);
				}

				$time1	= DB::table('ticket_assignment_log')->where(
							[
								'ticket_id' => $ticket->id,
								'status'=>'Resolved',
							]
						)->value('created_at');
				//$t1 = strtotime( $time1 );
				//$t2 = strtotime( date("Y-m-d H:i:s") );
				//$diff = $t2 - $t1;

				//$close_after_resolution_duration = $diff / ( 60 );

				$time2 = date("Y-m-d H:i:s");
				$close_after_resolution_duration = checkDurationActive($ticket->id,$ticket->created_by_contact,$time1,$time2);

				DB::table('ticket')
					->where('id', $id)
					->update(
						[
							'status' => 'Closed',
							'rating' => $input['rating'],
							'comment' => $input['message'],
							'close_after_resolution_duration'=>$close_after_resolution_duration ?? null,
							'close_after_resolution_time'=>date("Y-m-d H:i:s"),
						]
					);

				$content = "<p>Your ticket with id $id has been marked as Closed</p>
							<p>If you have more problem, you can contact us through available contact</p>";

				DB::table('ticket_assignment_log')->insertGetId(
					[
						'ticket_id' => $id,
						'team_id' => 0,
						'agent_id'=>Auth::user()->person,
						'created_at' => date("Y-m-d H:i:s"),
						'updated_at' => date("Y-m-d H:i:s"),
						'created_by' => Auth::user()->id,
						'updated_by' => Auth::user()->id,
						'status'=>'Closed',
					]
				);

				DB::table('ticket_log')->insertGetId(
					[
						'message' => 'Ticket is marked as Closed by <a href="#">'.Auth::user()->name.'</a>',
						'ticket_id' => $id,
						'created_at' => date("Y-m-d H:i:s"),
						'created_by' => Auth::user()->id,
					]
				);
				// sendNotifEmailByUserId($ticket->created_by, "Your ticket has been marked as Closed", $content,"ticket_monitoring",$id);


				//fungsi ini untuk mempermudah notif selain requester, siapa saja yang perlu dinotif
				$content_notif = "<p>Ticket Activity: Request with Ticket Number ".ticketNumber($id)." has been marked as Closed</p>
								<p>If you have more problem, you can contact us through available contact</p>";
				$title_notif = "Ticket Activity: Request with Ticket Number ".ticketNumber($id)." has been marked as Closed";
				notif_to_all_needed_contact($id,$ticket,$title_notif,$content_notif,"ticket_monitoring");

				echo json_encode(['success'=>true,'message'=>'Ticket has been marked as Closed']);
			}

			if($action == "reopen") {
				if(($ticket->requester == Auth::user()->person)
					|| ($ticket->created_by == Auth::user()->id)
				) {

				} else {
					echo "access denied";die;
				}

				//SLA ACTIVE AFTER REOPEN
				$now = date("Y-m-d H:i:s");
				if(!empty($ticket->due_date) && !empty($ticket->time_resolved)) {
					if($ticket->time_resolved >= $ticket->due_date) {
						//udah lewat
						$use_new_due_date = false;
					} else {
						//belum lewat
						$use_new_due_date = true;
					}
				} else {
					//opsi kedua pakai remaining SLA
					if($ticket->remaining_SLA == null) {
						//remaining kosong jadi lebih aman kalkulasi lagi
						$use_new_due_date = true;
					} else if ($ticket->remaining_SLA <= 0) {
						//remaining sudah habis pakai yang sebelumnya
						$use_new_due_date = false;
					} else {
						//remaining masih ada
						$use_new_due_date = true;
					}
				}
				if($use_new_due_date) {
					$ticket_due_date = checkDueDate($ticket->id,$now,"SLA continue");
				} else {
					$ticket_due_date = $ticket->due_date;
				}
				//$ticket_due_date = checkDueDate($ticket->id,$now,"remaining SLA",$ticket->remaining_SLA,$ticket->remaining_SLA_unit);


				DB::table('ticket')
					->where('id', $ticket->id)
					->update([	'due_date' => $ticket_due_date,
								'SLA_status' => 'Active',
								'continue_at' => date("Y-m-d H:i:s"),
								//'remaining_SLA' => null,
								//'remaining_SLA_unit' => null,
								//'paused_at' => null,
								//'due_date' => null
							]);


				DB::table('ticket')
					->where('id', $id)
					->update(
						[
							'status' => 'Re-Open',
							'comment' => $request['message'] ?? '-',
							'updated_by' => Auth::user()->id,
							'updated_at' => date("Y-m-d H:i:s"),
						]
					);

				DB::table('comment')
						->insert([	'message' => $request['message'] ?? '-',
							'ticket_id'=> $id,
							'user_id'=> Auth::user()->id,
							'contact_id'=> Auth::user()->person,
							'created_by' => Auth::user()->id,
							'updated_by' => Auth::user()->id,
							'created_at' => date("Y-m-d H:i:s"),
							'updated_at' => date("Y-m-d H:i:s"),
							'mode'	=> 'Reopen Ticket Reason',
						]);

				$content = "<p>Your ticket with id $id has Reopen</p>
							";

				DB::table('ticket_assignment_log')->insertGetId(
					[
						'ticket_id' => $id,
						'team_id' => 0,
						'agent_id'=>Auth::user()->person,
						'created_at' => date("Y-m-d H:i:s"),
						'updated_at' => date("Y-m-d H:i:s"),
						'created_by' => Auth::user()->id,
						'updated_by' => Auth::user()->id,
						'status'=>'Re-Open',
					]
				);

				DB::table('ticket_log')->insertGetId(
					[
						'message' => 'Ticket is Reopen by <a href="#">'.Auth::user()->name.'</a>',
						'ticket_id' => $id,
						'created_at' => date("Y-m-d H:i:s"),
						'created_by' => Auth::user()->id,
					]
				);
				// sendNotifEmailByUserId($ticket->created_by, "Your ticket has Reopen", $content,"ticket_monitoring",$id);


				//fungsi ini untuk mempermudah notif selain requester, siapa saja yang perlu dinotif
				$content_notif = "<p>Ticket Activity: Request with Ticket Number ".ticketNumber($id)." has been Reopen</p>
								<p>If you have more problem, you can contact us through available contact</p>";
				$title_notif = "Ticket Activity: Request with Ticket Number ".ticketNumber($id)." has been Reopen";
				notif_to_all_needed_contact($id,$ticket,$title_notif,$content_notif,"ticket_monitoring");

				//ticket diassign ke agent terakhir
				$agent_id = $ticket->agent_id;
				$content = "<p>You have assign to a Ticket that is Reopen with Ticket Number ".ticketNumber($id)." </p>
							<p>Please follow up this assignment ticket and make sure what make this ticket reopen again. After your assign ticket is done, you can mark this ticket as Solved.
							</p>
							<p>If you cannot Solve this ticket you can Escalate this ticket</p>";
				sendNotifEmail($agent_id, "You Have Assign to a Ticket that is Reopen with Ticket Number ".ticketNumber($id)."", $content,"assign_ticket",$id);

				echo json_encode(['success'=>true,'message'=>'Ticket has been Reopen']);
			}


			if($action == "on_progress") {

				//SINKRONKAN DGN FUNGSI setOnProgress()
				if($ticket->agent_id != Auth::user()->person) {
					echo "access denied";die;
				}

				$sla = DB::table('request_management')
						->join('sla', 'sla.id', '=', 'request_management.SLA_delivery')
						->where('request_management.id', $ticket->request_management)
						->select('sla.*')
						->first();

				if($sla) {
					$t1 = strtotime( $ticket->ticket_open_time );
					$t2 = strtotime( date("Y-m-d H:i:s") );
					$diff = $t2 - $t1;
					$first_response_duration = $diff / ( 60 );
					if($sla->target_response_unit == "days") {
						$target_duration_minute = $sla->target_response * 24 * 60;
					}
					if($sla->target_response_unit == "hours") {
						$target_duration_minute = $sla->target_response * 60;
					}
					if($sla->target_response_unit == "minutes") {
						$target_duration_minute = $sla->target_response;
					}

					if($first_response_duration
						<= $target_duration_minute) {
						//tercapati
						$first_response_status = "Target Achieved";
					} else {
						$first_response_status = "Target Not Achieved";
					}
				}

				DB::table('ticket')
					->where('id', $id)
					->update([	'status' => 'On Progress',
								'first_response_time' => date("Y-m-d H:i:s"),
								'first_response_status' => $first_response_status ?? null,
								'first_response_duration_unit' => 'minutes',
								'first_response_duration' => $first_response_duration ?? null,
							]);

				DB::table('ticket_assignment_log')->insertGetId(
					[
						'ticket_id' => $id,
						'team_id' => $ticket->team_id,
						'agent_id'=>$ticket->agent_id,
						'created_at' => date("Y-m-d H:i:s"),
						'updated_at' => date("Y-m-d H:i:s"),
						'created_by' => Auth::user()->id,
						'updated_by' => Auth::user()->id,
						'status'=>'On Progress',
					]
				);

				DB::table('ticket_log')->insertGetId(
					[
						'message' => 'Ticket is marked as On Progress by <a href="#">'.Auth::user()->name.'</a>',
						'ticket_id' => $id,
						'created_at' => date("Y-m-d H:i:s"),
						'created_by' => Auth::user()->id,
					]
				);

				$content = "<p>Your ticket with Ticket Number ".ticketNumber($id)." has been marked as On Progress</p>
							<p>If you have more problem, you can contact us through available contact</p>";
				sendNotifEmailByUserId($ticket->created_by, "Your ticket with Ticket Number ".ticketNumber($id)." has been marked as On Progress", $content,"ticket_monitoring",$id);

				//fungsi ini untuk mempermudah notif selain requester, siapa saja yang perlu dinotif
				$content_notif = "<p>Ticket Activity: Request with Ticket Number ".ticketNumber($id)." has been marked as On Progress</p>
								<p>If you have more problem, you can contact us through available contact</p>";
				$title_notif = "Ticket Activity: Request with Ticket Number ".ticketNumber($id)." has been marked as On Progress";
				notif_to_all_needed_contact($id,$ticket,$title_notif,$content_notif,"ticket_monitoring");

				echo json_encode(['success'=>true,'message'=>'Ticket has been marked as On Progress']);
			}
			if($action == "solved") {
				if($ticket->agent_id != Auth::user()->person) {
					echo "access denied";die;
				}

				date_default_timezone_set('Asia/Jakarta');

				if($ticket->due_date
					>= date("Y-m-d H:i:s")) {
					//tercapai
					$time_resolved_status = "Target Achieved";
				} else {
					$time_resolved_status = "Target Not Achieved";
				}




				DB::table('ticket')
					->where('id', $id)
					->update(['status' => 'Resolved', 'comment' => $request['message'] ?? '-',
							'time_resolved' => date("Y-m-d H:i:s"),
							'time_resolved_status' => $time_resolved_status ?? null,
					]);


				//SET RESOLVED DURATION
				$time1	= DB::table('ticket_assignment_log')->where(
							[
								'ticket_id' => $ticket->id,
								'status'=>'On Progress',//'Assignment',
								//diubah patokan awalnya, dari sebelumnya saat assignment, diganti jadi saat start case
							]
						)
						->orderBy('id','asc') //ambil yg paling awal
						->value('created_at');

				$time2	= date("Y-m-d H:i:s");

				if($time2 && $time1) {
					$t1 = strtotime( $time1 );
					$t2 = strtotime( $time2 );
					$time_resolved_duration = checkDurationActive($ticket->id,$ticket->agent_id,$time1,$time2);
					//$diff = $t2 - $t1;

					//$close_after_resolution_duration = $diff / ( 60 );

					DB::table('ticket')
						->where('id', $ticket->id)
						->update(
							[
								'time_resolved_duration'=>$time_resolved_duration ?? null,
							]
						);
				}

				//SET SLA STOPPED, AND REMAINING SLA
				$stopped_date_time = date("Y-m-d H:i:s");
				$remainingResult = checkRemainingSLA_SLA_is_paused_or_stopped($ticket->id,$stopped_date_time);
				//var_dump($remainingResult);
				//die;
				if($remainingResult) {
					DB::table('ticket')
						->where('id', $ticket->id)
						->update([	'SLA_status' => 'Stopped',
									'remaining_SLA' => $remainingResult['remaining_SLA'],
									'total_SLA' => $remainingResult['total_SLA'],
									'have_been_used_SLA' => $remainingResult['have_been_used_SLA'],
									'remaining_SLA_unit' => $remainingResult['remaining_SLA_unit'] ?? 'minutes',
									'paused_at' => $stopped_date_time,
									//'due_date' => null
								]);
				}


				DB::table('ticket_assignment_log')->insertGetId(
					[
						'ticket_id' => $id,
						'team_id' => $ticket->team_id,
						'agent_id'=>$ticket->agent_id,
						'created_at' => date("Y-m-d H:i:s"),
						'updated_at' => date("Y-m-d H:i:s"),
						'created_by' => Auth::user()->id,
						'updated_by' => Auth::user()->id,
						'status'=>'Resolved',
					]
				);

				DB::table('ticket_log')->insertGetId(
					[
						'message' => 'Ticket is marked as Resolved by <a href="#">'.Auth::user()->name.'</a>',
						'ticket_id' => $id,
						'created_at' => date("Y-m-d H:i:s"),
						'created_by' => Auth::user()->id,
					]
				);

				DB::table('comment')
						->insert([	'message' => $request['message'] ?? '-',
							'ticket_id'=> $id,
							'user_id'=> Auth::user()->id,
							'contact_id'=> Auth::user()->person,
							'created_by' => Auth::user()->id,
							'updated_by' => Auth::user()->id,
							'created_at' => date("Y-m-d H:i:s"),
							'updated_at' => date("Y-m-d H:i:s"),
							'mode'	=> 'Resolved Info',
						]);

				//set achievement
				$val = achievement($ticket);
				DB::table('ticket')->where('id',$ticket->id)->update(['achievement' => $val,]);

				//HANDLE PROBLEM
				if($ticket->finalclass == 'problem_request') {
					$list_ticket = DB::table('lnktickettoproblem')
										->where('problem_ticket_id',$ticket->id)
										->join('ticket', 'ticket.id', '=', 'lnktickettoproblem.ticket_id')
										->get();
					foreach($list_ticket as $t) {
						//parent service : Problem Selesai : SLA Reset
						//parent incident : Problem selesai : SLA CLosed
						$parent_ticket = DB::table('ticket')->where('id', $t->ticket_id)->first();
						if($parent_ticket) {
							if($parent_ticket->finalclass == 'service_request') {
								//SLA Reset
								$start_due_date = date("Y-m-d H:i:s");
								$due_date = checkDueDate($parent_ticket->id,$start_due_date);

								DB::table('ticket')
									->where('id', $parent_ticket->id)
									->update([	'due_date'=>$due_date,
												'SLA_status' => 'Active',

												]);
								DB::table('ticket_log')->insertGetId(
									[
										'message' => 'Service Ticket '.$parent_ticket->ref.' SLA has been Reset, because related ticket problem is marked as Resolved',
										'ticket_id' => $id,
										'created_at' => date("Y-m-d H:i:s"),
										'created_by' => Auth::user()->id,
									]
								);
							}

							if($parent_ticket->finalclass == 'incident_management') {
								//SLA CLosed
								//Teresolved
								if (!in_array($parent_ticket->status,["Closed","Resolved"])) {//kalau closed/resolved ga usah diresolved lagi

									DB::table('ticket')
										->where('id', $parent_ticket->id)
										->update(['status' => 'Resolved',]);

									//sinkronkan dgn kondisi action solved di atas
									if(empty($parent_ticket->due_date)) {
										$parent_ticket->due_date = checkDueDate($parent_ticket->id,$parent_ticket->ticket_open_time);
										DB::table('ticket')
											->where('id', $parent_ticket->id)
											->update([
													'due_date' => $parent_ticket->due_date
											]);
									}
									if($parent_ticket->due_date
										>= date("Y-m-d H:i:s")) {
										//tercapai
										$time_resolved_status = "Target Achieved";
									} else {
										$time_resolved_status = "Target Not Achieved";
									}




									DB::table('ticket')
										->where('id', $parent_ticket->id)
										->update(['status' => 'Resolved',
												'SLA_status' => 'Closed',
												'time_resolved' => date("Y-m-d H:i:s"),
												'time_resolved_status' => $time_resolved_status ?? null,
										]);


									//SET RESOLVED DURATION
									$time1	= DB::table('ticket_assignment_log')->where(
												[
													'ticket_id' => $parent_ticket->id,
													'status'=>'Assignment',
												]
											)->value('created_at');

									$time2	= date("Y-m-d H:i:s");

									if($time2 && $time1) {
										$t1 = strtotime( $time1 );
										$t2 = strtotime( $time2 );
										$time_resolved_duration = checkDurationActive($parent_ticket->id,$parent_ticket->agent_id,$time1,$time2);
										//$diff = $t2 - $t1;

										//$close_after_resolution_duration = $diff / ( 60 );

										DB::table('ticket')
											->where('id', $parent_ticket->id)
											->update(
												[
													'time_resolved_duration'=>$time_resolved_duration ?? null,
												]
											);
									}


									//SET SLA STOPPED, AND REMAINING SLA
									$stopped_date_time = date("Y-m-d H:i:s");
									$remainingResult = checkRemainingSLA_SLA_is_paused_or_stopped($parent_ticket->id,$stopped_date_time);
									//var_dump($remainingResult);
									//die;
									if($remainingResult) {
										DB::table('ticket')
											->where('id', $parent_ticket->id)
											->update([	'SLA_status' => 'Stopped',
														'remaining_SLA' => $remainingResult['remaining_SLA'],
														'total_SLA' => $remainingResult['total_SLA'],
														'have_been_used_SLA' => $remainingResult['have_been_used_SLA'],
														'remaining_SLA_unit' => $remainingResult['remaining_SLA_unit'] ?? 'minutes',
														'paused_at' => $stopped_date_time,
														//'due_date' => null
													]);
									}

									//set achievement
									$val = achievement($parent_ticket);
									DB::table('ticket')->where('id',$parent_ticket->id)->update(['achievement' => $val,]);

									$content = "<p>Your ticket with Ticket Number ".ticketNumber($parent_ticket->id)." has been marked as Resolved</p>
												<p>If you have more problem, you can contact us through available contact</p>";
									sendNotifEmailByUserId($parent_ticket->created_by, "Your ticket with Ticket Number ".ticketNumber($parent_ticket->id)." has been marked as Resolved", $content,"ticket_monitoring",$parent_ticket->id);

									$content_notif = "<p>Ticket Activity: Request with Ticket Number ".ticketNumber($parent_ticket->id)." has been marked as Resolved</p>
													<p>If you have more problem, you can contact us through available contact</p>";
									$title_notif = "Ticket Activity: Request with Ticket Number ".ticketNumber($parent_ticket->id)." has been marked as Resolved";
									notif_to_all_needed_contact($parent_ticket->id,$parent_ticket,$title_notif,$content_notif,"ticket_monitoring");

									//DB::table('ticket_log')->insertGetId(
										//[
											//'message' => 'Incident Ticket '.$parent_ticket->ref.' SLA has been Closed, because related ticket problem is marked as Resolved',
											//'ticket_id' => $id,
											//'created_at' => date("Y-m-d H:i:s"),
											//'created_by' => Auth::user()->id,
										//]
									//);
								}
							}
						}

					}
				}


				$content = "<p>Your ticket with Ticket Number ".ticketNumber($id)." has been marked as Resolved</p>
							<p>If you have more problem, you can contact us through available contact</p>";
				sendNotifEmailByUserId($ticket->created_by, "Your ticket with Ticket Number ".ticketNumber($id)." has been marked as Resolved", $content,"ticket_monitoring",$id);

				//fungsi ini untuk mempermudah notif selain requester, siapa saja yang perlu dinotif
				$content_notif = "<p>Ticket Activity: Request with Ticket Number ".ticketNumber($id)." has been marked as Resolved</p>
								<p>If you have more problem, you can contact us through available contact</p>";
				$title_notif = "Ticket Activity: Request with Ticket Number ".ticketNumber($id)." has been marked as Resolved";
				notif_to_all_needed_contact($id,$ticket,$title_notif,$content_notif,"ticket_monitoring");

				echo json_encode(['success'=>true,'message'=>'Ticket has been marked as Resolved']);
			}
			if($action == "escalate") {
				$last_approver = DB::table('ticket_approval')->where('ticket_id',$ticket->id)->latest('created_at')->first();
				$last_approver_id = $last_approver->approval_id ?? -9234;//imposible value
				if($ticket->agent_id == Auth::user()->person || ($last_approver_id == Auth::user()->person)  ) {
					//pass
				} else {
					echo "access denied";die;
				}


				$request_management = DB::table('request_management')->where('id',$ticket->request_management)->first();
				//var_dump($ticket);
				//var_dump($ticket->request_management);
				//var_dump($request_management);
				//die;
				//$list_assignment_tier = explode(",",$request_management->assignment_tier);
				//$assign_type_list = explode(",",$request_management->assignment_type);
				//$tier_flag = false;
				//$next_tier_index = 0;
				//$ticket->current_tier = $ticket->current_tier ?? 1;

				$next_agent = $input['agent_id4'] ?? null;
				//var_dump($input);
				//assignment_system menghandle roundrobin, loadbalace, random
				//dan handling cuti,
				//hasil returnya bisa 1. dapet agent, 2. pending on leave, atau 3. tidak dapat agent

				$ret_val = assignment_system($ticket,$request_management,$next_agent,"escalation");
				//var_dump($ret_val);
				//die;
				if($ret_val['status'] == 'ok') {
					$agent_id = $ret_val['agent_id'];
					$team_id = $ret_val['team_id'];
					$tier = $ret_val['tier'];
				} else if($ret_val['status'] == 'pending leave') {
					$agent_id = $ret_val['agent_id'];
					$team_id = $ret_val['team_id'];
					$tier = $ret_val['tier'];

					//flow baru grp tidak ada pending leave
					//saat dieskalasi gagal, karena next tier sedang on Leave

					DB::table('ticket_log')->insertGetId(
						[
							'message' => 'Failed manual escalation, there is no agent that is available for this ticket right now because the next tier agent is On Leave ',
							'ticket_id' => $id,
							'created_at' => date("Y-m-d H:i:s"),
							'created_by' => -1,
						]
					);

					echo json_encode(["success" => false, 'message' => 'There is no agent that is available for this ticket right now because the next tier agent is On Leave ']);
					die;

					//berikut flow lama pending leave versi nabati

					// DB::table('ticket')
					// 	->where('id', $id)
					// 	->update([
					// 				'status' =>'Pending On-Leave',
					// 				'pending_leave_team_id'=>$team_id,
					// 				'pending_leave_agent_id' => $agent_id,
					// 				'pending_leave_tier' => $tier,
					// 			]);

					// $content_notif = "<p>Ticket Activity: Status with Ticket Number ".ticketNumber($id)." is Pending On-Leave</p>
					// 				<p>If you have more problem, you can contact us through available contact</p>";
					// $title_notif = "Ticket Activity: Status with Ticket Number ".ticketNumber($id)." is Pending on Leave";
					// notif_to_all_needed_contact($id,$ticket,$title_notif,$content_notif,"ticket_monitoring");

					// echo json_encode(["success" => true, 'message' => "Now the status ticket is Pending On-Leave, because the agent/s is on Leave right now. You can assign other agent manually via Helpdesk Portal or wait automatic assignment to agent when agent is back to work. ", ]);
					// die;
				} else {
					//no agent can handle
					echo json_encode(["success" => false, 'message' => 'There is no agent that is available in next tier right now. <b>Please contact Administrator to set Active Agent for this Request. </b>']);
					die;
				}

				////checking next tier
				//if(!empty($input['agent_id4'])) {
					//$team_id = 0;
					//$agent_id = $input['agent_id4'];

					////ingat current tier mulai dari 1, sedangkan array mulai dari 0,
					////jadi current tier malah jadi next posisi array yang dimaksud
					//if(!empty($list_assignment_tier[$ticket->current_tier])) {
						//$next_team_tier = 0;
						//$next_tier_index = $ticket->current_tier;
					//} else {
						//$next_team_tier = 0;
						//$next_tier_index = $ticket->current_tier;
						////echo json_encode(['success'=>false,'message'=>"There's no escalation time setting for the next tier. You cannot escalate this ticket."]);
						////die;
					//}
				//}
				//else {

					////ingat current tier mulai dari 1, sedangkan array mulai dari 0,
					////jadi current tier malah jadi next posisi array yang dimaksud
					//if(!empty($list_assignment_tier[$ticket->current_tier])) {
						//$next_team_tier = $list_assignment_tier[$ticket->current_tier];
						//$next_tier_index = $ticket->current_tier;
					//}
					//if(empty($next_team_tier)) {
						////tidak ada lagi maka status tidak berubah
						////notif
						//echo json_encode(['success'=>false,'message'=>"There's no available team for next tier. You have to select Employee in select box."]);
						//die;
					//}
					////lakukan eskalasi

					////ASSIGNMENT
					//$assign_list = explode(",",$request_management->assignment_tier);
					//$assign_type_list = explode(",",$request_management->assignment_type);
					//$team_id = $assign_list[$next_tier_index];
					//$agent_id = null;
					//if($assign_type_list[$next_tier_index] == 1) {
						//$agent_id = loadBalance($team_id);
					//}
					//else if($assign_type_list[$next_tier_index] == 2) {
						//$agent_id = roundRobin($team_id);
					//}
					//else if($assign_type_list[$next_tier_index] == 3) {
						//$agent_id = random($team_id);
					//}
					//else if($assign_type_list[$next_tier_index] == 4) {
						//$agent_id = $team_id;//kalau manual maka isi team_id sebetulnya employee id yg terpilih
						//$team_id = 0;//kosongkan

						//$is_active = filterActiveEmployee($agent_id);
						//if(!$is_active) {
							//$agent_id = null;
						//}
					//}
				//}

				//set expire previous schedule execution
				DB::table('schedule_execution')->where(
					[
						'ref_id' => $id,
						'type' => 'ticket',
						'status' => 'Pending',
					])
					->update(['status'=>'Expired']);

				//if(empty($agent_id)) {
					//echo json_encode(["success" => false, 'message' => 'There is no agent that is available in next tier right now. <b>Please contact Administrator to set Active Agent for this Request. </b>']);
					//die;
				//}
				//DIEKSKALASI
				DB::table('ticket_assignment_log')->insertGetId(
					[
						'ticket_id' => $id,
						'team_id' => null,
						'agent_id'=>Auth::user()->person,
						'created_at' => date("Y-m-d H:i:s"),
						'updated_at' => date("Y-m-d H:i:s"),
						'created_by' => Auth::user()->id,
						'updated_by' => Auth::user()->id,
						'status'=>'Escalated',
					]
				);

				//DIASSIGN KE
				DB::table('ticket_assignment_log')->insertGetId(
					[
						'ticket_id' => $id,
						'team_id' => $team_id,
						'agent_id'=>$agent_id,
						'created_at' => date("Y-m-d H:i:s"),
						'updated_at' => date("Y-m-d H:i:s"),
						'created_by' => Auth::user()->id,
						'updated_by' => Auth::user()->id,
						'status'=>'Assignment',
						'tier'=>$tier,
					]
				);

				$assign_time = getAssignTime($agent_id);

				DB::table('ticket_assign_time')->insertGetId(
					[
						'ticket_id' => $id,
						'assign_time' => $assign_time,
						'team_id' => $team_id,
						'agent_id'=>$agent_id,
					]
				);

				DB::table('ticket')
					->where('id', $id)
					->update([	'team_id'=>$team_id,
								'agent_id' => $agent_id,
								'assign_time'=>$assign_time,
								'current_tier' => $tier,
							]);


				//$due_date = checkDueDate($id,date("Y-m-d H:i:s"));

				$escalation_time_list = explode(",",$request_management->escalation_time);
				$escalation_unit_list = explode(",",$request_management->escalation_unit);

				$next_tier_index = $tier - 1;// tier mulai dari 1, next_tier_index mulai dari 0

				if(!empty($escalation_unit_list[$next_tier_index])) {
					$escalation_date = checkEscalationDate($id,$assign_time,($escalation_time_list[$next_tier_index] ?? null),($escalation_unit_list[$next_tier_index] ?? null));

					DB::table('ticket')
						->where('id', $id)
						->update([
									//'due_date'=>$due_date,
									'escalation_date' =>$escalation_date,
								]);



					DB::table('schedule_execution')->insertGetId(
						[
							'action' => 'Auto Escalation',
							'recipient' => 0,
							'title' => "",
							'data' => "",
							'ref_id' => $id,
							'type' => 'ticket',
							'status' => 'Pending',
							'execution_time'=> $escalation_date,
							'current_tier' => $next_tier_index+1,
							'created_at' => date("Y-m-d H:i:s"),
							'created_by' => Auth::user()->id,
						]
					);
				} else {
					//jika tidak  ada setinganya maka dinullkan saja
					DB::table('ticket')
						->where('id', $id)
						->update([
									'escalation_date' =>null,
								]);
				}


				DB::table('ticket_assign_time')->insertGetId(
					[
						'ticket_id' => $id,
						'assign_time' => $assign_time,
						'team_id' => $team_id,
						'agent_id'=>$agent_id,
					]
				);

				DB::table('ticket_log')->insertGetId(
					[
						'message' => 'Ticket escalated by <a href="#">'.Auth::user()->name.'</a>',
						'ticket_id' => $id,
						'created_at' => date("Y-m-d H:i:s"),
						'created_by' => Auth::user()->id,
					]
				);

				$content = "<p>You have assign to a new Escalation Ticket with Ticket Number ".ticketNumber($id)."</p>
							<p>Please follow up this assignment ticket. After your assign ticket is done, you can mark this ticket as Solved.
							</p>
							<p>If you cannot Solve this ticket you can Escalate this ticket</p>";
				sendNotifEmail($agent_id, "You Have Assign to New Escalation Ticket", $content, "assign_ticket", $id);

				$content = "<p>Your ticket with Ticket Number ".ticketNumber($id)." is escalate to next tier</p>
							<p>Please wait for your ticket to be Resolved by our team.</p>";
				sendNotifEmailByUserId($ticket->created_by, "Your ticket with Ticket Number ".ticketNumber($id)." is escalate to next tier", $content, "ticket_monitoring",$id);

				//fungsi ini untuk mempermudah notif selain requester, siapa saja yang perlu dinotif
				$content_notif = "<p>Ticket Activity: Request with Ticket Number ".ticketNumber($id)." is escalate to next tier</p>
								<p>If you have more problem, you can contact us through available contact</p>";
				$title_notif = "Ticket Activity: Request with Ticket Number ".ticketNumber($id)." is escalate to next tier";
				notif_to_all_needed_contact($id,$ticket,$title_notif,$content_notif,"ticket_monitoring");

				echo json_encode(["success" => true, 'message' => "Your assignment has been escalation to next tier", ]);
			}




		} else {
			echo json_encode(["success" => false, 'message' => "Ticket is not found", ]);
		}

	}

	public function send_rating(Request $request)
	{
		$input = $request->all();

		DB::table('ticket')
			->where('id', $input['id'])
			->update(
				[
					'rating' => $input['rating'],
					'comment' => $input['comment'],
					'updated_at' => date("Y-m-d H:i:s")
				]
			);

		return redirect(URL('/').'/ticket-monitoring/'.$input['id']);
	}

    public function set_ref_id($type, $id)
	{
		$ref_id = "";

		switch($type) {
			case 'incident_management':
                // $ref_id = env('PREFIX_INCIDENT_MANAGEMENT')."-".str_pad($id,env('TICKET_PADDING'),"0",STR_PAD_LEFT);
				$ref_id = "IM-".str_pad($id,8,"0",STR_PAD_LEFT);
                break;
            case 'incident_request':
                //$ref_id = "I-00000".$id;
                // $ref_id = env('PREFIX_INCIDENT_REQUEST')."-".str_pad($id,env('TICKET_PADDING'),"0",STR_PAD_LEFT);
				$ref_id = "IR-".str_pad($id,8,"0",STR_PAD_LEFT);
                break;
            case 'service_request':
                // $ref_id = env('PREFIX_SERVICE_REQUEST')."-".str_pad($id,env('TICKET_PADDING'),"0",STR_PAD_LEFT);
				$ref_id = "SR-".str_pad($id,8,"0",STR_PAD_LEFT);
                break;
            case 'problem_request':
                // $ref_id = env('PREFIX_PROBLEM_REQUEST')."-".str_pad($id,env('TICKET_PADDING'),"0",STR_PAD_LEFT);
				$ref_id = "PR-".str_pad($id,8,"0",STR_PAD_LEFT);
                break;
            default:
                $ref_id = "DFT-".str_pad($id,8,"0",STR_PAD_LEFT);
                break;
		}

		return $ref_id;
	}
    public function update(Request $request, $id, $type)
    {

        accessv($type,'edit');

    }


    public function show($id,$type)
    {
		//$table = $type;
		//$berkas = DB::table($table)->where('id', $id)->first();

		//$sm = \DB::getDoctrineSchemaManager();
		//$list_column = $sm->listTableColumns($table);
		//$column = array();
		//foreach ($list_column as $c) {
			////echo $column->getName() . ': ' . $column->getType() . "\n";
			//$key = $c->getName();
			//if(!in_array($key,array('kabupaten_kota','kecamatan','provinsi','id','desa_id','penduduk','approve_1','approve_2','approve_3','approve_4','approve_5','approve_6','tanggal_approve_1','tanggal_approve_2','tanggal_approve_3','tanggal_approve_4','tanggal_approve_5','tanggal_approve_6','status','created_at','updated_at','deleted_at','penandatanganan','tandatangan_camat','tanggal_approve'))) {
				//$column[$key] = array("field_name"=>$key,"type_data"=>$c->getType());
			//}
		//}


		//foreach($column as $key => $value) {//multiple file upload
			//if ((strpos($key, 'daftar_file') !== false)
				//|| (strpos($key, 'galeri') !== false)
			//) {
				//$column[$key] = array("field_name"=>$key,"type_data"=>"upload multiple file");
			//}
		//}

		//foreach($column as $key => $value) {//single file
			//if ((strpos($key, 'upload_file') !== false)
				//|| (strpos($key, 'foto') !== false)
				//|| (strpos($key, 'file_download') !== false)
				//) {
				//$column[$key] = array("field_name"=>$key,"type_data"=>"upload file");
			//}
		//}
		//foreach($column as $key => $value) {//Editor
			//if ((strpos($key, 'detail') !== false)
				//) {
				//$column[$key] = array("field_name"=>$key,"type_data"=>"Editor");
			//}
		//}
		//if($table == "jabatan"){
			//$organisasi = DB::table('organisasi')->pluck('nama', 'id');
			//$column['organisasi_id'] = array("field_name"=>'organisasi_id',"type_data"=>"select","option"=>$organisasi);
		//}
		//if($table == "topik_forum"){
			//unset($column['daftar_user']);
			//$column['privasi'] = array("field_name"=>'privasi',"type_data"=>"select","option"=>array('All' => 'All', 'Organisasi' => 'Organisasi', 'User Pilihan' => 'User Pilihan'));
			//$organisasi = DB::table('organisasi')->where('desa_id',\Auth::user()->desa_id)->pluck('nama', 'id');
			//$column['organisasi'] = array("field_name"=>'organisasi',"type_data"=>"select","option"=>$organisasi);



			//$daftar_user_array = DB::table('user_pilihan')
									//->select(DB::raw('user_pilihan.*,users.name,users.nik'))
									//->join('users', 'users.id', '=', 'user_pilihan.penduduk')
									//->where('user_pilihan.type','topik_forum')
									//->where('ref_id',$berkas->id)->get();
			////var_dump($daftar_user_array);
			////die;
			//$susun = array();
			//foreach($daftar_user_array as $k){
				//$susun[] = $k->penduduk;
			//}
			//$daftar_user = implode(",",$susun);
			//$column['user_pilihan'] = array("field_name"=>'user_pilihan',
											//"type_data"=>'Pilih Daftar Anggota',
											//"daftar_user"=>$daftar_user,
											//"daftar_user_array"=>$daftar_user_array,
											//);
			//$berkas->organisasi = DB::table("organisasi")->where('id', $berkas->organisasi)->first()->nama;
		//}
		////var_dump($column);
		////die;
        //if (empty($berkas)) {
            //Flash::error('Berkas tidak ditemukan');
            //return redirect(route('list', ['type' => $type]));
        //}

        //return view('crudmodal.show')->with('type',$type)->with('berkas', $berkas)->with('row', $berkas)->with('spmampu', $berkas)->with('column',$column)->with('breadcrumb',$this->breadcrumb);
    }

    public function delete($id, $type)
    {
		accessv($type,'delete');
		$table = $type;

		if(in_array($table,array("users","oauth_access_tokens","oauth_auth_codes","oauth_clients",
					"oauth_personal_access_clients","oauth_refresh_tokens","password_resets"))){ // pembatasan tabel tertentu saja yang dapat dihapus
            Flash::error('Permission denied');
            return redirect(route('list', ['type' => $type]));
		}
		if(in_array($table,["team","person","employee"])) {
			$table = "contact";
		}
		if (in_array($table, ["incident_management"])) {
			$table = "ticket";
		}
		$berkas = DB::table($table)->where('id', $id)->first();

		//die;
        if (empty($berkas)) {

            Flash::error('Berkas tidak ditemukan');
            return redirect(route('list', ['type' => $type]));
        }

		DB::table($table)->where('id', $id)->delete();



		Flash::success('Data berhasil dihapus.');
		return redirect(route('list', ['type' => $type]));
    }

    //--- DI BAWAH FUNGSI2 YANG MENSUPPORT PROSES CRUD ---

    public function setUploadFieldAndEditorField($column) {

		foreach($column as $key => $value) {//multiple file upload
			if ((strpos($key, 'daftar_file') !== false)
				|| (strpos($key, 'galeri') !== false)
			) {
				$column[$key] = array("field_name"=>$key,"type_data"=>"upload multiple file");
			}
		}
		foreach($column as $key => $value) {//single file
			if ((strpos($key, 'upload_file') !== false)
				|| (strpos($key, 'foto') !== false)
				|| (strpos($key, 'icon') !== false)
				|| (strpos($key, 'image') !== false)
				|| (strpos($key, 'file_download') !== false)
				|| (strpos($key, 'picture') !== false)
				) {
				$column[$key] = array("field_name"=>$key,"type_data"=>"upload file");
			}
		}

		foreach($column as $key => $value) {//Editor
			if ((strpos($key, 'detail') !== false)
				) {
				$column[$key] = array("field_name"=>$key,"type_data"=>"Editor");
			}
		}
		return $column;
	}

    //PROSES UPLOAD SAAT FORM SUBMIT, HANDLE MULTIPLE ATAU SINGLE UPLOAD
    public function processUpload($request, $input) {

				foreach($input as $key => $value){
					//MULTIPLE FILE UPLOAD HANDLE
					if ((strpos($key, 'daftar_file') !== false)
						|| (strpos($key, 'galeri') !== false)

					) {
						$list_filename = array();
						$files = $request->file($key);
						if($request->hasFile($key))
						{
							foreach ($files as $file) {
								$filename = izrand(5).'-'.removeSpecialCharacters($file->getClientOriginalName());
								handleUpload($file,$filename,'/upload');
								$list_filename[] = $filename;
								//$file->store('upload/' . $filename . '/messages');
							}
							$input[$key] = implode(", ",$list_filename);
						}
					}
					//SINGLE UPLOAD
					if ((strpos($key, 'upload_file') !== false)
						|| (strpos($key, 'foto') !== false)
						|| (strpos($key, 'icon') !== false)
						|| (strpos($key, 'file_download') !== false)
						|| (strpos($key, 'image') !== false)
						|| (strpos($key, 'attachment') !== false)
						) {
						$file = $request->file($key);
						if($request->hasFile($key))
						{
							$filename = izrand(5).'-'.removeSpecialCharacters($file->getClientOriginalName());
							handleUpload($file,$filename,'/upload');
							$input[$key] = $filename;

							//echo "masuk ";die;
						}
					}
				}
		return $input;
	}

	//HANDLE PROSES PENCARIAN UMUM MAUPUN PER KOLOM DI YAJRA DATATABLE
	public function searchDataTable($query,$request,$type,$join_table,$list_sample_table,$disposal) {
		$input = $request->all();

		//echo "<pre>";
		//var_dump($input);
		//echo "</pre>";
		//die;
		if(!isset($input['search']['value'])) {
			$input['search']['value'] = "";
		}
		//$input['columns'][0]['search']['value'];
		$search_value = $input['search']['value'];
		$use_filter = false;
		$str = "? OR ";
		$disposed_str = "1";
		$arr_search = "1";
		$start_query = "1";
		$end_query = "1";

		//SEARCHING PER KOLOM
		$flag_search_column = false;



		//SPECIAL CASE SEARCHING INCLUDE HERE
		$key = 'sample_status';
		if(($type == "label_iso_flexi_vessel") &&  !empty($input[$key])) {
			//sample_status pakai alias di query jadi harus masuk special case
			$sample_table = "sample_iso_flexi_vessel";
			$str = ($str == "? OR ")?"":$str;//seting ulang nilai
			$arr_search = ($arr_search == "1")?[]:$arr_search;//seting ulang nilai
			$str .= ' (IF('.$sample_table.'.status = 1, "Progress", IF('.$sample_table.'.status = 2, "Rejected", IF('.$sample_table.'.status = 3, "Completed", "Progress"))) LIKE ?) AND';
			//kalau labelnya belum ada sampel maka dianggap Progress (Else nya Progress)
			$arr_search[] =  "%".$input[$key]."%";
			$flag_search_column = true;
			$use_filter = true;
			unset($input[$key]);//unset agar tidak digunakan di kolom lain
		}

		//GET KOlOM2 UTAMA TABEL
		$sm = \DB::getDoctrineSchemaManager();
		$list_column = $sm->listTableColumns($type);
		$arr_column = array();
		foreach ($list_column as $c) {
			//echo $column->getName() . ': ' . $column->getType() . "\n";
			$key = $c->getName();
			if(!in_array($key,array('javascript','ordering',//'created_at','updated_at','expire_date'
			))) {
				//echo $key;
				$skip = false;


				if(($type == "mode" && $key == "sampling_point" )
					|| ($type == "sample_schedule" && $key == "sampling_point" )
					|| ($type == "sample_schedule" && $key == "mode")
					|| ($type == "master_spec_tankfarm" && $key == "product" )) {
					//ini jika ingin mengecualikan pencarian kolom di tabel utama.
					//contoh kasus tabel utama mode dijoin dgn sampling_point. kolom sampling_point di mode diskip, pakainya kolom sampling_point di tabel sampling point
					$skip = true;
				}

				if((!$skip) &&  !empty($input[$key])) {
						$str = ($str == "? OR ")?"":$str;//seting ulang nilai
						$arr_search = ($arr_search == "1")?[]:$arr_search;//seting ulang nilai
						//echo "KENA";
						$column_be_search = $type.".".$key;

						//pencarian untuk tanggal harus convert dulu, di database 2021-01-04 maka diconvert via mysql jadi 04 Jan 2021
						if(in_array($key,['created_at','updated_at','expire_date'])) {
							if(($key == "expire_date") && (strtolower($input[$key]) == "soon")) {
								//mencari yang Expire Soon
								$str .= ' ('.$column_be_search.' BETWEEN CURDATE() AND date_add(CURDATE(), interval 1 month)) AND';
							} else if(($key == "expire_date") && (strtolower($input[$key]) == "expired")) {
								//mencari yang Expired
								$str .= ' ('.$column_be_search.' < CURDATE()) AND';
							} else {
								$str .= ' (DATE_FORMAT('.$column_be_search.', "%Y %M %d")   LIKE ?) AND ';
							}
						} else {
							if (($key == "status") && in_array($type,$list_sample_table)) {
								//search status harus dikondisikan dulu karena database isinya dalam angka
								$table_status = $type;
								$str .= ' (IF('.$table_status.'.status = 1, "Progress", IF('.$table_status.'.status = 2, "Rejected", IF('.$table_status.'.status = 3, "Completed", "Progress"))) LIKE ?) AND';
							} else {
								$str .= ' ('.$column_be_search.' LIKE ?) AND ';
							}
						}

						$arr_search[] =  "%".$input[$key]."%";
						$flag_search_column = true;
						$use_filter = true;
						unset($input[$key]);//jangan gunakan lagi, mengurangi resiko digunakan di join tabel

				}
			}
		}

		//kolom2 join table juga dimasukan dalam search
		foreach($join_table as $jo_table) {
			//GET KOlOM2 UTAMA TABEL
			$sm = \DB::getDoctrineSchemaManager();
			$list_column = $sm->listTableColumns($jo_table);

			foreach ($list_column as $c) {
				$key = $c->getName();
				if(!in_array($key,array('javascript','ordering',//'created_at','updated_at','expire_date'
				))) {
					if(!empty($input[$key])) {
						$str = ($str == "? OR ")?"":$str;//seting ulang nilai
						$arr_search = ($arr_search == "1")?[]:$arr_search;//seting ulang nilai
						//echo "KENA2";
						$column_be_search = $jo_table.".".$key;

						if(in_array($key,['created_at','updated_at','expire_date'])) {
							if(($key == "expire_date") && (strtolower($input[$key]) == "soon")) {
								//mencari yang Expire Soon
								$str .= ' ('.$column_be_search.' BETWEEN CURDATE() AND date_add(CURDATE(), interval 1 month)) AND';
							} else if(($key == "expire_date") && (strtolower($input[$key]) == "expired")) {
								//mencari yang Expired
								$str .= ' ('.$column_be_search.' < CURDATE()) AND';
							} else {
								$str .= ' (DATE_FORMAT('.$column_be_search.', "%Y %M %d")   LIKE ?) AND ';
							}
						} else {
							if (($key == "status") && in_array($type,['label_drumbag'])) {
								//search status harus dikondisikan dulu karena database isinya dalam angka
								$table_status = 'sample_drumbag';
								$str .= ' (IF('.$table_status.'.status = 1, "Progress", IF('.$table_status.'.status = 2, "Rejected", IF('.$table_status.'.status = 3, "Completed", "Progress"))) LIKE ?) AND';
							} else {
								$str .= ' ('.$column_be_search.' LIKE ?) AND ';
							}
						}

						$arr_search[] =  "%".$input[$key]."%";
						$flag_search_column = true;
						$use_filter = true;
						unset($input[$key]);//jangan gunakan lagi
					}
				}
			}
		}
		//END : SEARCHING PER KOLOM

		//SEARCH ALL COLUMN
		if(!empty($search_value)) {

			//GET KOlOM2 UTAMA TABEL
			$sm = \DB::getDoctrineSchemaManager();
			$list_column = $sm->listTableColumns($type);
			$arr_column = array();
			foreach ($list_column as $c) {
				//echo $column->getName() . ': ' . $column->getType() . "\n";
				$key = $c->getName();
				if(!in_array($key,array('javascript','ordering','created_at','updated_at','expire_date'
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
					//search semua kolom
					$str .= ' ('.$a.' LIKE ?) OR';
					$arr_search[] =  "%".$search_value."%";
				}

				//var_dump($arr_search);
				//var_dump($str);

				//search status harus dikondisikan dulu karena database isinya dalam angka
				if (in_array($type,$list_sample_table)) {
					$table_status = $type;
					if($type == "label_drumbag") {
						$table_status = "sample_drumbag";
					}
					if($type == "label_iso_flexi_vessel") {
						$table_status = "sample_iso_flexi_vessel";
					}

					$str .= ' (IF('.$table_status.'.status = 1, "Progress", IF('.$table_status.'.status = 2, "Rejected", IF('.$table_status.'.status = 3, "Completed", "Progress"))) LIKE ?) OR';
					$arr_search[] =  "%".$search_value."%";
				}

				$disposed_str = "1";
				if((substr($type,0,6) == "label_") || ((substr($type,0,7) == "sample_") && ($type != "sample_schedule"))) {
					if($disposal == "disposal") {
						$disposed_str = ' (is_disposed = "Disposed") ';
					} else {
						$disposed_str = ' (is_disposed IS NULL) ';
					}
				}

				if((substr($type,0,6) == "label_") || ((substr($type,0,7) == "sample_") && ($type != "sample_schedule"))) {
					//$str .= ' (DATE_FORMAT('.$type.'.expire_date, "%Y-%M-%d")   LIKE ?) OR ';
					//$arr_search[] =  "%".$search_value."%";
				}

				$str .= ' (DATE_FORMAT('.$type.'.created_at, "%Y-%M-%d")   LIKE ?) OR ';
				$arr_search[] =  "%".$search_value."%";
				$str .= ' (DATE_FORMAT('.$type.'.updated_at, "%Y-%M-%d")   LIKE ?) OR ';
				$arr_search[] =  "%".$search_value."%";



				//if ($request->has('start_date')) {
					//$start_date = $request->get('start_date');
					//$d = explode("/",$start_date);
					//$Nstart_date = $d[2].'-'.$d[1].'-'.$d[0]." 00:00:00";

					////$query->where('updated_at','>',$Nstart_date );
					////$query->where('expire_date','>',$Nstart_date );
					//$start_query = ' expire_date > '.$Nstart_date;
				//}

				//if ($request->has('end_date')) {
					//$end_date = $request->get('end_date');
					//$d = explode("/",$end_date);
					//$Nend_date = $d[2].'-'.$d[1].'-'.$d[0]." 23:59:59";
					////$query->where('updated_at','<',$Nend_date );
					////$query->where('expire_date','<',$Nend_date );
					//$end_query = ' expire_date < '.$Nend_date;
				//}

				//echo $disposed_str. ' AND ('.$str.' 0) AND '.$start_query.'  AND '.$end_query.'';


				$use_filter = true;
				//$query->whereRaw('(CONCAT(product_name,id) LIKE ?) OR ('.$type.'.id LIKE ?)', ["%".$search_value."%","%".$search_value."%"]);
				//$query->whereRaw("IF(active = 1, 'Yes', 'No') like ?", ["%{$keyword}%"]);
		}
		//END : SEARCH ALL COLUMN

		if (!empty($input['from'])) {
			$start_date = $input['from'];
			$d = explode("/",$start_date);
			$Nstart_date = '"'.$d[2].'-'.$d[1].'-'.$d[0].' 00:00:00'.'"';

			//$query->where('updated_at','>',$Nstart_date );
			//$query->where('expire_date','>',$Nstart_date );
			$start_query = $type.'.updated_at > '.$Nstart_date;
		}
		$end_query = "1";
		if (!empty($input['to'])) {
			$end_date = $input['to'];
			$d = explode("/",$end_date);
			$Nend_date = '"'.$d[2].'-'.$d[1].'-'.$d[0].' 23:59:59'.'"';
			//$query->where('updated_at','<',$Nend_date );
			//$query->where('expire_date','<',$Nend_date );
			$end_query = $type.'.updated_at < '.$Nend_date;
			$use_filter = true;
		}
		//echo "ya".var_export($input,true);


        if (!empty($input['state'])) {
            if($input['state'] = 'overdue')
            {
                $query_state = 'overdue_at < CURDATE()';
            }
            if($input['state'] = 'due_today')
            {
                $query_state = 'created_at between curdate() and overdue_at';
            }
            $query = $query->whereRaw($query_state);
        }


		if($use_filter) {
			//echo $disposed_str. ' AND ('.$str.' 0) AND '.$start_query.'  AND '.$end_query.'';
			if($flag_search_column) {
				//kalau search per column maka pakai AND maka dibelakangnya AND 1
				$query->whereRaw($disposed_str. ' AND ('.$str.' 1) AND '.$start_query.'  AND '.$end_query.'',$arr_search );
			} else {
				//kalau search semua column maka pakai OR maka dibelakangnya OR 0
				$query->whereRaw($disposed_str. ' AND ('.$str.' 0) AND '.$start_query.'  AND '.$end_query.'',$arr_search );
			}
			//var_dump($disposed_str. ' AND ('.$str.' 0) AND '.$start_query.'  AND '.$end_query.'');
			//var_dump($arr_search);
			//die;
			//$query->whereRaw($start_query.'  AND '.$end_query.'');
			//echo $start_query.'  AND '.$end_query.'';
		}

		return $query;
	}
	//fungsi ini mendapatkan daftar nama kolom dari tabel dalam bentuk array
	public function getArrayColumnNameFromTable($table) {
		$sm = \DB::getDoctrineSchemaManager();
		$list_column = $sm->listTableColumns($table);
		$column = array();
		foreach ($list_column as $c) {
			//echo $column->getName() . ': ' . $column->getType() . "\n";
			$key = $c->getName();
			if(!in_array($key,array('obsolescence_date','javascript','ordering','deleted_at','created_by','updated_by'
			))) {
				//CEK BUKAN GALERI
				if ((strpos($key, 'daftar_file') !== false)
					|| (strpos($key, 'galeri') !== false)
					|| (strpos($key, 'detail') !== false)
				) {

				} else {
					$column[] = $key;
				}
			}
		}
		return $column;
	}

	public static function getArrayColumNameFromTableForCreateAndEdit($table) {
		$sm = \DB::getDoctrineSchemaManager();
		$list_column = $sm->listTableColumns($table);
		$column = array();

		//$column_list = Schema::getColumnListing($table);
		//var_dump($column_list);
		foreach ($list_column as $c) {
			//echo $c->getName() . ': ' . $c->getType()->getName() . "\n";
			//echo "<pre>";
			//var_dump($c);
			//echo "</pre>";
			$key = $c->getName();
			if(!in_array($key,array('created_by','updated_by','obsolescence_date','json_limit','kabupaten_kota','kecamatan','provinsi','telah_diapprove','id','desa_id','penduduk','approve_1','approve_2','approve_3','approve_4','approve_5','approve_6','tanggal_approve_1','tanggal_approve_2','tanggal_approve_3','tanggal_approve_4','tanggal_approve_5','tanggal_approve_6','created_at','updated_at','deleted_at','penandatanganan','tandatangan_camat','tanggal_approve'))) {

				$column[$key] = array("field_name"=>$key,"type_data"=>ucfirst($c->getType()->getName()));

			}
		}
		return $column;
	}

	public static function date_helper($input,$date_fields) {
		foreach($date_fields as $field) {
			if (strpos($input[$field],'/') !== false) {
				$d = explode("/",$input[$field]);
				$input[$field] = $d[2].'-'.$d[1].'-'.$d[0];
			}
		}
		return $input;
	}

	public function select2list(Request $request,$type) {

		$result = [];
		if(in_array($type,['asset'])) {
			$result = DB::table('asset')
    ->selectRaw('CONCAT(asset.name, \' ( Asset Number : \', COALESCE(asset.asset_number, \'-\'), \')\') AS id,
                 CONCAT(asset.name, \' ( Asset Number : \', COALESCE(asset.asset_number, \'-\'), \')\') AS text')
    ->whereRaw('lnkcontacttoasset.contact_id=? AND (name LIKE ? OR asset_number LIKE ?)',
        [($request->query('contact_id') ?? Auth::user()->person), '%' . $request->query('term') . '%', '%' . $request->query('term') . '%']
    )
    ->join('lnkcontacttoasset', 'lnkcontacttoasset.asset_id', '=', 'asset.id')
    ->get()
    ->toArray();

			echo json_encode($result);
			//echo json_encode(array_merge(["-1"=>["id"=>"","text"=>"-Select Asset-"]],$result));
			die;
		}
		if(in_array($type,['job_title'])) {
			$result = DB::table('job_title')->select('id','job_name AS name','job_name AS text')->get()->toArray();
			echo json_encode($result);
			//echo json_encode(['result'=>$result,"pagination"=> ["more"=> false]]);
			die;
		}
		if(in_array($type,['position'])) {
			$result = DB::table('position')->select('id','position_name AS name')->get()->toArray();
			echo json_encode($result);
			die;
		}
		if(in_array($type,['country'])) {
			$result = DB::table('country')->select('code as id','name as text')->get()->toArray();
			echo json_encode($result);
			die;
		}
		if(in_array($type,['service'])) {
			$request_type = $request->query('request_type');
			if(!empty($request_type)) {
				//var_dump($request_type);
				$result = DB::table('service')->select('id','name AS text')->where('request_type',$request_type)->get()->toArray();
				echo json_encode(array_merge(["-1"=>["id"=>"","text"=>"-Select Service-"]],$result));
				die;
			}

			$service_category = $request->query('service_category');
			if(!empty($service_category)) {
				$result = DB::table('service')->select('service.id','service.name AS text')
								->where('request_type','Incident')
								->join('lnkservicetoservice_category', 'lnkservicetoservice_category.service_id', '=', 'service.id')
								->where('lnkservicetoservice_category.service_category_id',$service_category)->get()->toArray();
				echo json_encode(array_merge(["-1"=>["id"=>"","text"=>"-Select Service-"]],$result));
				die;
			}

		}
		if(in_array($type,['employee'])) {
			$result = DB::table('contact')->selectRaw("id,CONCAT(name,', ',email) AS text")
										->where('contact.status', '=', 'Active')
										->whereNull('contact.deleted_at')
										->where('type','Employee')
										->where('name', 'ilike', '%'.$request->query('term').'%')
										->get()->toArray();
			echo json_encode(array_merge(["-1"=>["id"=>"","text"=>"-Select Employee-"]],$result));
			die;
		}
		if(in_array($type,['employee2'])) {
			$result = DB::table('contact')->selectRaw('id,CONCAT(name,", ",email,"") AS text')
										->where('contact.status', '=', 'Active')
										->whereNull('contact.deleted_at')
										->where('type','Employee')
										->where('name', 'ilike', '%'.$request->query('term').'%')
										->get()->toArray();
			echo json_encode(array_merge(["-1"=>["id"=>"","text"=>"-Select Employee-"]],$result));
			die;
		}
		if(in_array($type,['person','team'])) {
			$result = DB::table('contact')->select('id','name AS text')->where('type',ucfirst($type))->get()->toArray();
			echo json_encode(array_merge(["-1"=>["id"=>"","text"=>"-Select Employee-"]],$result));
			die;
		}

		if(in_array($type,['location','sla','organization','company','service','service category','faq','faq category',])) {
			$type = str_replace(" ","_",strtolower($type));
			$result = DB::table($type)->select('id','name AS text')->get()->toArray();
			echo json_encode(array_merge(["-1"=>["id"=>"","text"=>"-Select ".ucwords(str_replace("_"," ",$type))."-"]],$result));
			die;
		}
		echo json_encode($result);
		//echo json_encode(['result'=>[],"pagination"=> ["more"=> false]]);
		die;
	}

	public function updateNextApprover($ticketId) {
		$ticket = DB::table('ticket')->where('id', $ticketId)->first();
		$contact_case_journey = getContactCaseJourney($ticket,"include self","not_include_request_management_notif","","");

		$next_approval_id = null;
		foreach($contact_case_journey as $contact) {
			if(!empty($contact->step_approval)) {
				$has_approve = DB::table('ticket_approval')
					->where('ticket_id',$ticket->id)
					->where('approval_id',$contact->id)
					->exists();

				if ($has_approve) {
				} else {
					$next_approval_id = $contact->id;
					break;
				}
			}
		}

		if($next_approval_id) {
			DB::table('ticket')->where('id', $ticketId)->update([
				'next_approval_id' => $next_approval_id
			]);

			return response()->json([
				'message' => 'Success update next approver',
			]);
		}

		return response()->json([
			'message' => 'Failed update next approver, journey not found',
		], 400);
	}
}
