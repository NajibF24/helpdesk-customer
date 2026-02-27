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
use Illuminate\Support\Facades\Mail;
use Exception;

class CrudController extends AppBaseController
{

	private $breadcrumb = array('histori'=>'dashboard','topik_forum'=>'dashboard');
    public function __construct()
    {
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        $this->middleware('auth');
    }

	public function tesemail($email,$title,$message) {
		Mail::to($email)->send(new \App\Mail\EmailSend(str_replace("_"," ",$title),str_replace("_"," ",$message)));
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

		$title = t($table);//title secara default mengacu nama table tapi tetap bisa dicustom, disini pakai fungsi helper t() di helpers.php

		if (in_array($type, array("incident"))) {
			$column = ["id", "title","organization", "caller", "start_date", "status", "agent"];
		}

		if (in_array($type, array("ticket"))) {
			$column = ["id", "ref", "title", "operational_status", "status", "priority", "urgency", "service_name", "service_category_name", "agent", "next_approval"];

			if ($request->query('status_ticket')) {
				$status_ticket = $request->query('status_ticket');
			}

			if ($request->query('ticket_type')) {
				$ticket_type = $request->query('ticket_type');
			}

            if ($request->query('state')) {
				$state = $request->query('state');
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
            ;
	}

    public function listServer(Request $request,$type,$param1="")
    {
		accessv($type,'list');

		$user_id = \Auth::user()->id;

		$user = \Auth::user();

		$organization = DB::table('organization_level')->pluck('name','name')->toArray();

		if(!in_array($type,["team","person"]+$organization)) {
			$query = DB::table($type);
			$users = $query;
		}
		$join_table = [];

		if(in_array($type,array('test_master_parameter_config'))){
			//$users = $users->select(
						//DB::raw($type.'.*,"'.$type.'" as nama_table,test_master_parameter.parameter as parameter
						//'))
						//->join('test_master_parameter', 'test_master_parameter.id', '=', $type.'.fid_parameter');
		}

		else if (in_array($type, array("incident"))) {
			$users = DB::table('ticket');
			$users = $users->select(
				DB::raw('ticket.*, organization.name AS organization, contact.name as caller, ticket.operational_status as status, c.name as agent, "incident_management" as nama_table')
			)
			->leftJoin('organization', 'organization.id', '=', 'ticket.org_id')
			->leftJoin('contact', 'contact.id', '=', 'ticket.caller_id')
			->leftJoin('contact as c', 'c.id', '=', 'ticket.agent_id');
		}
		else if (in_array($type, array("ticket"))) {
			$users = $users->select(
				DB::raw('ticket.*, service.name as service_name, service_category.name as service_category_name, contact.name as agent, c.name as next_approval, "ticket" as nama_table')
			)
			->leftJoin('service', 'service.id', '=', 'ticket.service_id')
			->leftJoin('service_category', 'service_category.id', '=', 'ticket.servicesubcategory_id')
			->leftJoin('contact', 'contact.id', '=', 'ticket.agent_id')
			->leftJoin('contact as c', 'c.id', '=', 'ticket.next_approval_id')
			->where('ticket.created_by', Auth::user()->id);

			# 'approved','assigned','escalated_tto','escalated_ttr','new','pending','waiting_for_approval','Draft','Submit for Approval','Rejected','Waiting for User','Open','On Progress','Resolved','Closed'
			if ($request->query('status_ticket')) {
				if (strpos($request->query('status_ticket'), ',') > 0) {
					$users->whereIn('ticket.status',['Submit for Approval','Waiting for User']);
					// echo Auth::user()->id; die();
					if ($request->query('ticket_type') == 'overdue') {
						// $users->whereRaw('ticket.created_at < DATE_SUB(CURDATE(), INTERVAL 7 DAY)');
						$users->whereRaw('ticket.due_date < CURDATE()');
					} else {
						// $users->whereRaw('ticket.created_at BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND DATE_SUB(CURDATE(), INTERVAL 7 DAY)');
						$users->whereRaw('due_date between CURDATE() and due_date');
					}
				} else if ($request->query('status_ticket') == 'unassign_ticket') {
					$users->where('ticket.agent_id', 0);
				} else {
					$users->where('ticket.status', $request->query('status_ticket'));

					if ($request->query('ticket_type') == 'incident_management'
						|| $request->query('ticket_type') == 'service_request') {
						$users->where('ticket.finalclass', $request->query('ticket_type'));
					}
				}
			}
		}
		else {
			$users = $users->select(
						DB::raw($type.'.*,"'.$type.'" as nama_table
						'));
		}


		//$users = $users->orderBy($type.'.id', 'desc');

		$query2 = datatables()->of($users);

		$sm = \DB::getDoctrineSchemaManager();
		$list_column = $sm->listTableColumns($type);
		$column = array();
		foreach ($list_column as $c) {
			$key = $c->getName();
			if($key == "foto"){
				$query2 = $query2->editColumn('foto', '<img src="{{url("/")}}/uploads/{{$foto}}" style="height:60px"/>');
			}
			if($key == "icon"){
				$query2 = $query2->editColumn('icon', '<img src="{{url("/")}}/uploads/{{$icon}}" style="height:60px"/>');
			}
			if($key == "image"){
				$query2 = $query2->editColumn('image', '<img src="{{url("/")}}/../../uploads/{{$image}}" style="height:60px"/>');
			}
			if($key == "foto_utama"){
				$query2 = $query2->editColumn('foto_utama', '<img src="{{url("/")}}/uploads/{{$foto_utama}}" style="height:60px"/>');
			}
		}

		$query2 = $query2->editColumn('created_at', '{{empty($created_at)?"":date("d M Y",strtotime($created_at))}}');
		$query2 = $query2->editColumn('updated_at', '{{empty($updated_at)?"":date("d M Y",strtotime($updated_at))}}');
		$query2 = $query2->editColumn('created_date', '{{empty($created_date)?"":date("d M Y",strtotime($created_date))}}');
		$query2 = $query2->editColumn('modified_date', '{{empty($created_date)?"":date("d M Y",strtotime($modified_date))}}');

		$query2 = $query2->addColumn('nama_table', $type);
		//$query2->addColumn('nama_table', function($data){
						//return $type;
					//});
		$list_sample_table = array();
		$disposal = false;
		return $query2->addColumn('action', 'crudmodal.actions')
					->filter(function ($query) use ($request,$type,$join_table,$list_sample_table,$disposal) {
						$query = $this->searchDataTable($query,$request,$type,$join_table,$list_sample_table,$disposal);

					})
					->escapeColumns([])->toJson();

    }

    public function edit(Request $req,$id,$type,$param1="")
    {
		accessv($type,'edit');
		$table = $type;
		$organization = DB::table('organization_level')->pluck('name','name')->toArray();
		if(!in_array($type,["team","person","employee","incident_management","service_request"]+$organization)) {
			$berkas = DB::table($table)->where('id', $id)->first();
			if (empty($berkas)) {
				Flash::error('Berkas tidak ditemukan');
				return redirect(route('list', ['type' => $type]));
			}
		}
		$column = $this->getArrayColumNameFromTableForCreateAndEdit($table);

		$column = $this->setUploadFieldAndEditorField($column);


		$menu_relation = [];
		if ($table == "incident_management") {
			$berkas = DB::table('ticket')->where('id', $id)->first();
			$menu_relation = ["asset","contact","child_incident"];
			$column = $this->selectbox_relation($column, 'company',"select", 'company','id','name');
			$column = $this->selectbox_relation($column, 'caller',"select_and_add", 'contact','id','name');
			$column = $this->selectbox_choice($column, 'origin', ['Email', 'Monitoring', 'Phone', 'Portal']);
			$column['title'] = array('type_data' => 'String');
			$column['description'] = array('type_data' => 'Text');
			$column = $this->selectbox_relation($column, 'category', "select_and_add", 'service_category', 'id', 'name');
			$column = $this->selectbox_relation($column, 'request', "select_and_add", 'service', 'id', 'name', true, 'request_type', 'Incident');
			// $column = $this->selectbox_choice($column, 'impact', ['A Department', 'A Service', 'A Person']);
			// $column = $this->selectbox_choice($column, 'urgency', ['Critical', 'High', 'Medium', 'Low']);
			$column = $this->selectbox_relation($column, 'parent_incident', "select_and_add", 'ticket', 'id', 'ref', true, 'finalclass', 'incident_management');
			$column = $this->selectbox_relation($column, 'parent_problem_id', "select_and_add", 'ticket', 'id', 'ref', true, 'finalclass', 'problem_management');
			// $column = $this->selectbox_relation($column, 'parent_change', "select_and_add", 'ticket', 'id', 'ref', true, 'finalclass', 'change');
			$column['upload_file'] = array('type_date' => 'Text');
			$column = $this->setUploadFieldAndEditorField($column);
			$column['private_log'] = array('type_data' => 'Text');
			$column['public_log'] = array('type_data' => 'Text');
		}
		if ($table == "service_request") {
			$berkas = DB::table('ticket')->where('id', $id)->first();
			$ticket = DB::table('ticket')->where('id',$id)->first();
			$title = "Detail Request ".$ticket->title;
			$breadcumb = ['', $ticket->title];
			$ticket_statuses = DB::table('ticket_approval')->where('ticket_id', $id)->get();

			//$column['include'] = array('type_data' => 'Include File','include'=>"crudmodal.service_incident_include",'statuses'=>$ticket_statuses,'ticket'=> $ticket,'title'=> $title, 'breadcumb'=> $breadcumb);
			return view("crudmodal.service_incident_include")
				->with('berkas', $berkas)
				->with('statuses',$ticket_statuses)->with('ticket',$ticket)->with('title',$title)->with( 'breadcumb',$breadcumb);
		}

		//cancel button
		$show_cancel = true;
		if(in_array($table,["features","general","contact_us","about"])) {
			//stay on page edit
			$show_cancel = false;//prevent user back to list, because it only form no list
		}

        $title = "Edit ".t($table);

        return view('crudmodal.edit')->with('title',$title)->with('menu_relation',$menu_relation)->with('type',$type)->with('berkas', $berkas)->with('row', $berkas)->with('spmampu', $berkas)->with('column',$column)->with('breadcrumb',$this->breadcrumb)->with('show_cancel',$show_cancel);
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
			$originator = DB::table('contact')->get();
			//$request_management = DB::table('request_management')->where('request_name',$service_id)->first();
			$service = DB::table('service')->where('id', $service_id)->first();
			$breadcumb = ['Request An Incident', 'Incident Catalog', 'Create Incident '.$service->name];

			$person_id = Auth::user()->person;
			 //get from contact
			 if($person_id) {
				$contact = DB::table('contact')->where('id',$person_id)->first();
				//cek lokasi dan company
				//yang cocok di request management
				$request_management = DB::table('request_management')
										->where('location',$contact->location)
										->where('company',$contact->company)
										->where('request_name',$service_id)->first();
				//var_dump($service_id);
				//var_dump($contact);
			} else {
				return view('crudmodal.incident_not_found')->with('message',"Contact account not found")->with('title','Create Incident')->with('type',$type)->with('column',$column)->with('breadcrumb',$this->breadcrumb);
			}

			if(empty($request_management)) {
				return view('crudmodal.incident_not_found')->with('message',"Request management not found in your location company")->with('title','Create Incident')->with('type',$type)->with('column',$column)->with('breadcrumb',$this->breadcrumb);
			} else {
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
			$person_id = Auth::user()->person;
			 //get from contact
			if($person_id) {
				$contact = DB::table('contact')->where('id',$person_id)->first();
				//cek lokasi dan company
				//yang cocok di request management
				$request_management = DB::table('request_management')
										->where('location',$contact->location)
										->where('company',$contact->company)
										->where('request_name',$service_id)->first();

			} else {
				return view('crudmodal.incident_not_found')->with('message',"Contact account not found")->with('title','Create Incident')->with('type',$type)->with('column',$column)->with('breadcrumb',$this->breadcrumb);
			}

			if(empty($request_management)) {
				return view('crudmodal.incident_not_found')->with('message',"Request management not found in your location company")->with('title','Create Incident')->with('type',$type)->with('column',$column)->with('breadcrumb',$this->breadcrumb);
			}
			//else {
				//return view('crudmodal.create_incident')->with('request_management',$request_management)->with('title','Create Service')->with('type',$type)->with('column',$column)->with('breadcrumb',$this->breadcrumb);
			//}

			$service_id = $req->query('request');
			//$request_management = DB::table('request_management')->where('request_name',$service_id)->first();
			$originator = DB::table('contact')->get();
			$service = DB::table('service')->where('id', $service_id)->first();
			$breadcumb = ['Request A Service', 'Service Catalog', 'Create Service '.$service->name];
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
			$id = $req->query('id');
			$ticket_draft = DB::table('ticket_draft')->where('id',$id)->first();
			//var_dump($ticket_draft->request_management);
			//var_dump($ticket_draft);

			$request_management = DB::table('request_management')->where('id',$ticket_draft->request_management)->first();
			//var_dump($request_management);
			$service_id = $request_management->request_name;

			//$service_id = $req->query('request');

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
			$originator = DB::table('contact')->get();
			$service = DB::table('service')->where('id', $service_id)->first();
			$breadcumb = ['Request A Service', 'Service Catalog', 'Create Service '.$service->name];
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
		//echo "OKE";die;
		accessv($type,'create');
		$user_id = \Auth::user()->id;
		$user_name = \Auth::user()->name;

		$input = $request->all();

		//return response()->json(array("success" => false, 'message' => "Record has been saved".var_export($input,true), "data" => "", "id" => 1));
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
		//unset($input['lnk-slt']);
		//unset($input['lnk-customer_contract']);
		unset($input['check-all']);
		unset($input['check_item']);

		//var_dump($input);
		//die;

		$input_main = $input;

        $service_id = $input['service_id'] ?? 0;
		$requester = $input['request_for'] == "other" ? (int) $input['requester'] : Auth::user()->person;
		$contact_id = $requester;
		$contact = DB::table('contact')->where('id',$contact_id)->first();
		//cek lokasi dan company
		//yang cocok di request management
		$request_management = DB::table('request_management')
								->where('location',$contact->location)
								->where('company',$contact->company)
								->where('request_name',$service_id)->first();

		//$request_management = DB::table('request_management')->where('id',$input['request_management'])->first();
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


		//ASSIGNMENT
		$assign_list = explode(",",$request_management->assignment_tier);
		$assign_type_list = explode(",",$request_management->assignment_type);
		$team_id = $assign_list[0];
		$agent_id = null;
		if($assign_type_list[0] == 1) {
			$agent_id = loadBalance($team_id);
		}
		else if($assign_type_list[0] == 2) {
			$agent_id = roundRobin($team_id);
		}
		else if($assign_type_list[0] == 3) {
			$agent_id = random($team_id);
		}


		$list_filename = array();
		$list_file_url = [];
		$file = $request->file('file');
		if($request->hasFile('file'))
		{
			$filename = izrand(5).'-'.$file->getClientOriginalName();
			handleUpload($file,$filename,'/upload');
			$list_filename[] = $filename;
			$list_file_url[] = URL('/').'/uploads/'.$filename;
		}
		$file = $request->file('file2');
		if($request->hasFile('file2'))
		{
			$filename = izrand(5).'-'.$file->getClientOriginalName();
			handleUpload($file,$filename,'/upload');
			$list_filename[] = $filename;
			$list_file_url[] = URL('/').'/uploads/'.$filename;
		}
		$file = $request->file('file3');
		if($request->hasFile('file3'))
		{
			$filename = izrand(5).'-'.$file->getClientOriginalName();
			handleUpload($file,$filename,'/upload');
			$list_filename[] = $filename;
			$list_file_url[] = URL('/').'/uploads/'.$filename;
		}

		$files = implode(",",$list_filename);
		$files_url = implode(",",$list_file_url);

		//INSERT DI MAIN TABLE

		$table_submit = "ticket";
		if($input['submit_type'] == "draft") {
			$table_submit = "ticket_draft";
		}

		$id = DB::table($table_submit)->insertGetId(
			[
				'status' => 'Open',
				'org_id' => $input_main['company'] ?? 0,
				'caller_id' => $input_main['caller'] ?? 0,
				'title' => $input_main['title'] ?? '-',
				'files' => $files,
				'files_url' => $files_url,
				'description' => $input_main['description'] ?? '-',
				//'private_log' => $input_main['private_log'] ?? '-',
				'finalclass' => $finalclass,
				//'upload_file' => $input_main['upload_file'] ?? '',
				//'impact' => $input_main['impact'] ?? '',
				//'priority' => $input_main['urgency'] ?? '',
				//'urgency' => $input_main['urgency'] ?? '',
				//'origin' => $input_main['origin'] ?? '',
				'service_id' => $service_id ?? 0,
				'servicesubcategory_id' => $service_category_id ?? 0,
				'request_management'=>$request_management->id,
				'parent_incident_id' => $input_main['parent_incident'] ?? 0,
				'parent_problem_id' => $input_main['parent_problem_id'] ?? 0,
				'parent_change_id' => $input_main['parent_change'] ?? 0,
				'public_log' => $input_main['public_log'] ?? '-',
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
                'team_id' => $team_id,
                'agent_id' => $agent_id,
                'assign_time' => date("Y-m-d H:i:s"),

				'data_json'=>json_encode($input),
				'form_data_json'=>$input_main['form_data_json'] ?? '-',
                'form_builder_json'=> $form_builder->json,
                'form_builder'=> $form_builder->id,

                'requester' => $input['request_for'] == "other" ? (int) $input['requester'] : Auth::user()->person
			]
		);

		if($input['submit_type'] == "draft") {
			$redirect = URL('/').'/myDraft';
			echo json_encode(["success"=>true,"message"=>"success","redirect"=>$redirect]);
			die;
		}

		DB::table('ticket_assign_time')->insertGetId(
			[
				'ticket_id' => $id,
				'assign_time' => date("Y-m-d H:i:s"),
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
			]
		);
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


        DB::table('ticket')
			->where('id', $id)
			->update(['ref' => $this->set_ref_id($finalclass, $input['id'])]);


        DB::table('ticket_log')->insertGetId(
			[
				'message' => 'System created Ticket with status <b>Open</b> with type <b>Incident</b>',
                'ticket_id' => $id,
                'created_at' => date("Y-m-d H:i:s"),
                'created_by' => Auth::user()->id,
			]
		);

		$content = "<p>You have assign to a new Ticket with Ticket Number ".ticketNumber($id)." </p>
					<p>Please follow up this assignment ticket. After your assign ticket is done, you can mark this ticket as Solved.
					</p>
					<p>If you cannot Solve this ticket you can Escalate this ticket</p>";
		sendNotifEmail($agent_id, "You Have Assign to New Ticket with Ticket Number ".ticketNumber($id)."", $content,"assign_ticket",$id);

		//Flash::success('Record has been saved');
		//if($request_management->request_type == "Service Request") {
			////return redirect(route('myServices'));
			//$redirect = URL('/').'/myServices';
		//} else {
			//return redirect(route('myIncidents'));
			$redirect = URL('/').'/myIncidents';
		//}
		echo json_encode(["success"=>true,"message"=>"success","redirect"=>$redirect]);
		die;

		//$user = \Auth::user();
		////$this->kirimNotifSetelahInsertBaru($table,$user,$id);


		//if($modal) {
			////echo "OKE";
			////return response()->json(array("success" => true, 'message' => "Record has been saved", "data" => "", "id" => $id,
											////"value_select"=>$id,"display_select"=>$display_select
				////));

			//echo json_encode(["success" => true, 'message' => "Record has been saved", "data" => "", "id" => $id,
											//"value_select"=>$id,"display_select"=>$display_select]);
		//} else {
			//Flash::success('Record has been saved');

			//return redirect(route('list', ['type' => $type]));
		//}
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
			if($action == "solved") {
				if($ticket->agent_id != Auth::user()->person) {
					echo "access denied";die;
				}
				DB::table('ticket')
					->where('id', $id)
					->update(['operational_status' => 'resolved']);
				$content = "<p>Your ticket with id $id has been marked as Resolved</p>
							<p>If you have more problem, you can contact us through available contact</p>";
				sendNotifEmailByUserId($ticket->created_by, "Your ticket has been marked as Resolved", $content);
				echo json_encode(['success'=>true,'message'=>'Ticket has been marked as Resolved']);
			}
			if($action == "escalate") {
				if($ticket->agent_id != Auth::user()->person) {
					echo "access denied";die;
				}


				//checking next tier

				$request_management = DB::table('request_management')->where('id',$ticket->request_management)->first();
				$list_assignment_tier = explode(",",$request_management->assignment_tier);
				$tier_flag = false;
				$next_tier_index = 0;
				for($i=0;$i<count($list_assignment_tier);$i++) {
					if($ticket->team_id == $list_assignment_tier[$i]) {
						//ditemukan yang sama, maka eskalasi Tier di loop berikutnya
						if(!empty($list_assignment_tier[$i+1])) {
							$next_team_tier = $list_assignment_tier[$i+1];
							$next_tier_index = $i+1;
						}
					}

				}
				if(empty($next_team_tier)) {
					//tidak ada lagi maka status tidak berubah
					//notif
					echo json_encode(['success'=>false,'message'=>"There's no available team for the next tear. You cannot escalate this ticket."]);
					die;
				}
				//lakukan eskalasi

				//ASSIGNMENT
				$assign_list = explode(",",$request_management->assignment_tier);
				$assign_type_list = explode(",",$request_management->assignment_type);
				$team_id = $assign_list[$next_tier_index];
				$agent_id = null;
				if($assign_type_list[$next_tier_index] == 1) {
					$agent_id = loadBalance($team_id);
				}
				else if($assign_type_list[$next_tier_index] == 2) {
					$agent_id = roundRobin($team_id);
				}
				else if($assign_type_list[$next_tier_index] == 3) {
					$agent_id = random($team_id);
				}

				DB::table('ticket')
					->where('id', $id)
					->update(['team_id'=>$team_id,'agent_id' => $agent_id,'assign_time'=>date("Y-m-d H:i:s")]);

				DB::table('ticket_assign_time')->insertGetId(
					[
						'ticket_id' => $id,
						'assign_time' => date("Y-m-d H:i:s"),
						'team_id' => $team_id,
						'agent_id'=>$agent_id,
					]
				);

				$content = "<p>You have assign to a new Escalation Ticket with id $id </p>
							<p>Please follow up this assignment ticket. After your assign ticket is done, you can mark this ticket as Solved.
							</p>
							<p>If you cannot Solve this ticket you can Escalate this ticket</p>";
				sendNotifEmail($agent_id, "You Have Assign to New Escalation Ticket", $content);

				$content = "<p>Your ticket with id $id is escalate to next tier</p>
							<p>Please wait for your ticket to be Resolved by our team.</p>";
				sendNotifEmailByUserId($ticket->created_by, "Your ticket is escalate to next tier", $content);

				echo json_encode(["success" => true, 'message' => "Your assignment has been escalation to next tier", ]);
			}


		} else {
			echo json_encode(["success" => false, 'message' => "Ticket is not found", ]);
		}

	}


    //public function loadBalance($team_id) {
		//$list_employee = DB::table('lnkemployeetoteam')->where('team_id',$team_id)->get();
		////cek tiket yang masih open dari masing2 employee
		//$contact_smallest_id = null;
		//$contact_smallest_count = null;
		//foreach($list_employee as $e) {
			//$ticket_open_count = DB::table('ticket')->where('agent_id',$e->employee_id)->where('operational_status','ongoing')->count();
			////echo $ticket_open_count;
			////echo "<br/>";
			////echo $e->employee_id;
			////echo "<br/>";
			//if(empty($contact_smallest_id)) {
				//$contact_smallest_id = $e->employee_id;
				//$contact_smallest_count = $ticket_open_count;
			//} else {
				//if($ticket_open_count < $contact_smallest_count) {
					//$contact_smallest_id = $e->employee_id;
					//$contact_smallest_count = $ticket_open_count;
				//}
			//}
		//}
		////echo $contact_smallest_id;
		////die;
		//return $contact_smallest_id;
	//}
    //public function roundRobin($team_id) {
		//$list_employee = DB::table('lnkemployeetoteam')->where('team_id',$team_id)->get();
		//$assign_list_time = [];
		//foreach($list_employee as $e) {
			//$last_ticket_of_employee = DB::table('ticket_assign_time')->where('agent_id',$e->employee_id)->orderBy('assign_time','desc')->whereNotNull('assign_time')->first();
			//if(empty($last_ticket_of_employee)) {
				////kalau ditemukan yang masih kosong maka yang itu saja
				//return $e->employee_id;
			//} else {
				////kumpulkan semua time assign yang terakhir dari employee
				//$assign_list_time[$e->employee_id] = $last_ticket_of_employee->assign_time;
			//}
		//}

		//asort($assign_list_time);
		////var_dump($assign_list_time);
		////die;
		//foreach($assign_list_time as $key => $value) {
			//$employee_id = $key;
			////echo $employee_id;
			////die;
			//return $employee_id;//first loop employee is the target
		//}
		////die;
		//return null;
	//}
    //public function random($team_id) {
		//$list_employee = DB::table('lnkemployeetoteam')->where('team_id',$team_id)->get();
		//$element_number = rand(0,($list_employee->count()-1));

		//return $list_employee[$element_number]->employee_id;
	//}

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
		$user_id = \Auth::user()->id;
		//$pejabat = DB::table('pejabat')->where('user_id', $user_id)->first();

		$input = $request->all();
		$table = $type;
		unset($input['client_token']);//kadang2 ikut masuk
		unset($input['_token']);
        unset($input['input_call']);

		if(in_array($table,["person","team","employee"])) {
			$r = DB::table('contact')->where('id', $id)->first();
		} else if(in_array($table,DB::table('organization_level')->pluck('name','name')->toArray())){
			$r = DB::table('organization')->where('id', $id)->first();
		} else if (in_array($table,["incident_management"])) {
			$r = DB::table('ticket')->where('id', $id)->first();
		} else {
			$r = DB::table($table)->where('id', $id)->first();
		}

		if($r){
			//unset($input['type']);
			unset($input['id']);
			//$input['penduduk'] = \Auth::user()->id;
			//$input['desa_id'] = $pejabat->desa_id;
			if (in_array($table, ["position"])) {
				$input['modified_date'] = date("Y-m-d H:i:s");
			} else {
				$input['updated_at'] = date("Y-m-d H:i:s");
			}


			$input = $this->processUpload($request, $input);


			$input2 = $input;

			//clear semua input relasi karena jalur pengolahannya berbeda
			foreach($input as $key => $value) {
				if (strpos($key, 'lnk-') !== false) {
					unset($input[$key]);
				}
			}
			//unset($input['lnk-slt']);
			//unset($input['lnk-customer_contract']);
			unset($input['check-all']);
			unset($input['check_item']);

			//UPDATE TABEL UTAMA
			$organization = DB::table('organization_level')->pluck('name','name')->toArray();
			if(!in_array($table,["customer_contract","team","person","employee","form_builder", "incident_management"]+$organization+$asset)) {
				DB::table($table)
							->where('id', $id)
							->update($input);
			}

			if(in_array($table, $asset)) {
				$input_main = $input;
				unset($input['name'],$input['organization'],$input['status'],$input['business_critically'],$input['move_to_production_date'],$input['description'],$input['created_at'],$input['created_by'],$input['updated_by'],$input['updated_at']);
				DB::table('asset')
						->where('id', $id)
						->update(['name' => $input_main['name']]);
				$this->updateRelation($id,'asset', ['contact','document'],$input2);

				if($table == "application_solution") {
					//$menu_relation = ["contact", "business_process",  "ticket"];
					$this->updateRelation($id,'application_solution', ['business_process'],$input2);
				}
			}

			if (in_array($table, ["incident_management"])) {
				$input_main = $input;
				DB::table('ticket')
					->where('id', $id)
					->update(
						[
							'org_id' => $input_main['company'] ?? $r->org_id,
							'caller_id' => $input_main['caller'] ?? $r->caller_id,
							'title' => $input_main['title'] ?? $r->title,
							'description' => $input_main['description'] ?? $r->description,
							'private_log' => $input_main['private_log'] ?? $r->private_log,
							'finalclass' => 'incident_management',
							'upload_file' => $input_main['upload_file'] ?? $r->upload_file,
							'impact' => $input_main['impact'] ?? $r->impact,
							'priority' => $input_main['urgency'] ?? $r->priority,
							'urgency' => $input_main['urgency'] ?? $r->urgency,
							'origin' => $input_main['origin'] ?? $r->origin,
							'service_id' => $input_main['request'] ?? $r->service_id,
							'servicesubcategory_id' => $input_main['category'] ?? $r->servicesubcategory_id,
							'parent_incident_id' => $input_main['parent_incident'] ?? $r->parent_incident_id,
							'parent_problem_id' => $input_main['parent_problem_id'] ?? $r->parent_problem_id,
							'parent_change_id' => $input_main['parent_change'] ?? $r->parent_change_id,
							'public_log' => $input_main['public_log'] ?? $r->public_log
						]
					);

				$this->updateRelation($id, 'ticket', ['asset','contact'], $input2);

				if (isset($input2['lnk-child_incident'])) {
					DB::table('ticket')->whereIn('id', $input2['lnk-child_incident'])->update(['parent_id' => $id]);
				} else {
					DB::table('ticket')->whereIn('parent_id', $id)->update(['parent_id' => 0]);
				}
			}

			Flash::success('Record has been saved');
			if(in_array($table,["features","general","contact_us","about"])) {
				//stay on page edit
				return redirect(route('edit', [$id,$type]));
			} else {
				//back to list
				return redirect(route('list', ['type' => $type]));
			}
			//return response()->json(array("success" => true, 'message' => "Record has been saved", "data" => "", "id" => $id));

		} else {
			//return response()->json(array("success" => false, 'message' => "Berkas tidak ditemukan"));
			Flash::error('Berkas tidak ditemukan');
			return redirect(route('list', ['type' => $type]));

		}

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
    public function selectbox_relation($column, $field_name,$type_data, $join_table,$join_table_value,$join_table_display, $is_where=false, $type="", $class="") {
		$option = DB::table($join_table)->pluck($join_table_display, $join_table_value);
		if ($is_where) {
			$option = DB::table($join_table)->where($type, $class)->pluck($join_table_display, $join_table_value);
		}
		$column[$field_name] = array("type_data"=>$type_data,"option"=>$option,"data-target"=>$join_table);
		return $column;
	}
    public function pick_title_name($column, $field_name) {
		$column[$field_name] = array("type_data"=>'pick_title_name');
		return $column;
	}
    public function pick_title_plus($column, $field_name,$option = [],$data_target='') {
		$column[$field_name] = array("type_data"=>'pick_title_plus','option'=>$option,'data-target'=>$data_target);
		return $column;
	}
    public function selectbox_choice($column, $field_name, $option) {
			$opt_key_val = [];
			foreach($option as $o) {
				$opt_key_val[$o] = $o;
			}
			$column[$field_name] = array("type_data"=>"select","option"=>$opt_key_val);
		return $column;
	}


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
								$filename = izrand(5).'-'.$file->getClientOriginalName();
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
							$filename = izrand(5).'-'.$file->getClientOriginalName();
							handleUpload($file,$filename,'/upload');
							$input[$key] = $filename;

							//echo "masuk ";die;
						}
					}
				}
		return $input;
	}
    //FUNGSI INI UNTUK UPDATE TABEL RELASI OTOMATIS DENGAN MENDETEKSI TABEL YANG DIRELASIKAN
    //DAPAT DIGUNAKAN DI INSERT ATAU UPDATE
	public function updateRelation($id,$table_main, $list_join_table,$input2) {

		foreach($list_join_table as $table_join) {
			if(Schema::hasTable("lnk".$table_main."to".$table_join)) {//check table exist
				$table_relation = "lnk".$table_main."to".$table_join;
			} else if(Schema::hasTable("lnk".$table_join."to".$table_main)) {//check table exist
				$table_relation = "lnk".$table_join."to".$table_main;
			} else {
				echo "table relation not found";
				echo "lnk".$table_join."to".$table_main;
				die;
			}
			DB::table($table_relation)->where($table_main.'_id',$id)->delete();
			if(!empty($input2['lnk-'.$table_join])) {
				foreach($input2['lnk-'.$table_join] as $l) {
					DB::table($table_relation)->insertGetId(
						[$table_main.'_id'=>$id,$table_join.'_id'=>$l]
					);
				}
			}
		}
	}
	public function updateRelation2($id,$table_main, $list_join_table,$input2) {

		foreach($list_join_table as $table_join) {
			if(Schema::hasTable("lnk".$table_main."to".$table_join)) {//check table exist
				$table_relation = "lnk".$table_main."to".$table_join;
			} else if(Schema::hasTable("lnk".$table_join."to".$table_main)) {//check table exist
				$table_relation = "lnk".$table_join."to".$table_main;
			} else {
				echo "table relation not found";
				echo "lnk".$table_join."to".$table_main;
				die;
			}
			DB::table($table_relation)->where($table_main.'_id',$id)->delete();
			DB::table($table_relation)->insertGetId(
				[$table_main.'_id'=>$id,$table_join.'_id'=>$input2[$table_join]]
			);
			//if(!empty($input2['lnk-'.$table_join])) {
				//foreach($input2['lnk-'.$table_join] as $l) {
					//DB::table($table_relation)->insertGetId(
						//[$table_main.'_id'=>$id,$table_join.'_id'=>$l]
					//);
				//}
			//}
		}
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
		if(in_array($type,['job_title'])) {
			$result = DB::table('job_title')->select('id','job_name AS name')->get()->toArray();
			echo json_encode($result);
			//echo json_encode(['result'=>$result,"pagination"=> ["more"=> false]]);
			die;
		}
		if(in_array($type,['position'])) {
			$result = DB::table('position')->select('id','position_name AS name')->get()->toArray();
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

		if(in_array($type,['employee','person','team'])) {
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
}
