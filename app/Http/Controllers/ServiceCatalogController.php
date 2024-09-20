<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laracasts\Flash\Flash;

use Illuminate\Support\Facades\Schema;

class ServiceCatalogController extends Controller {

    public function request_service()
    {
        $title = "Request A Service";
        $breadcumb = ['Request A Service'];
        $organization = DB::table('organization')->where('type', 'Department')->get();

        return view('service-catalog.request-service-catalog')
        ->with('organizations', $organization)
        ->with('title', $title)
        ->with('breadcumb', $breadcumb);
    }

    public function service_catalog($id)
    {
        // dd(Auth::user()->contact);
        $contact = Auth::user()->contact;
        $blade_file = 'service-catalog';
        if (empty($contact->job_title) || empty($contact->organization)) {
            $blade_file = 'not-found-job-organization';
        }
		//$tables = DB::select('SHOW TABLES');
		//$str = "";
		////dd($tables);
		//foreach($tables as $table)
		//{
			////var_dump($table);
			//$table = $table->Tables_in_old_itsmdb38;
			//if (Schema::hasColumn($table, 'updated_at'))
			//{
				//if (!in_array($table,['company','oauth_clients','oauth_access_tokens','oauth_clients','oauth_personal_access_clients' ]) && !Schema::hasColumn($table, 'company') && !Schema::hasColumn($table, 'country') ) {
					//$str .= "<br/>ALTER TABLE `$table` ADD `company` INT(11) NULL AFTER `updated_at`, ADD `country` VARCHAR(20) NULL AFTER `company`;";
					////$str .= "<br/>UPDATE `$table` SET country='ID',company='1';";
				//}
				////echo $table;
			//}


		//}
		//echo $str;
		//die;
        $title = "Service Catalog";
		$breadcumb = [
			[
				'name' => 'Request A Service',
				'url' => 'request-service/service-catalog/2'
			]
		];
        $organization = DB::table('organization')->where('id', $id)->first();
		$divisions = Organization::has('childrens')->where('type', 'DIVISION')->get();

        $service_category = DB::table('service_category')
								->where('type', 'service')
							//->where('department', $organization->id)
							->get();

        return view('service-catalog.'.$blade_file)
        ->with('title', $title)
        ->with('service_category', $service_category)
        ->with('organization', $organization)
        ->with('breadcumb', $breadcumb)
        ->with('divisions', $divisions)
        ->with('type','service')
        ->with('request_type','Service Request')
        ;
    }

    public function incident_catalog($id)
    {
        // dd(Auth::user()->contact);
        $contact = Auth::user()->contact;
        $blade_file = 'service-catalog';
        if (empty($contact->job_title) || empty($contact->organization)) {
            $blade_file = 'service-catalog';
            // $blade_file = 'not-found-job-organization';
        }
        $title = "Incident Catalog";
		$breadcumb = [
			[
				'name' => 'Request An Incident',
				'url' => 'request-incident/incident-catalog/2'
			]
		];
        $organization = DB::table('organization')->where('id', $id)->first();
        $service_category = DB::table('service_category')
								->where('type', 'incident')
							//->where('department', $organization->id)
							->get();
		$divisions = Organization::has('childrens')->where('type', 'DIVISION')->get();

        return view('service-catalog.'.$blade_file)
        ->with('title', $title)
        ->with('service_category', $service_category)
        ->with('organization', $organization)
        ->with('breadcumb', $breadcumb)
        ->with('type','incident')
        ->with('divisions', $divisions)
        ->with('request_type','Incident')
        ;
    }

    public function create_service($id)
    {
		dd(123);
        // $originator = DB::table('contact')->get();
        // $service = DB::table('service')->where('id', $id)->first();
        // $breadcumb = ['Request A Service', 'Service Catalog', 'Create Service '.$service->name];
        // $title = "Create Service ".$service->name;

        // return view('service-catalog.create')
        // ->with('breadcumb', $breadcumb)
        // ->with('title', $title)
        // ->with('originator', $originator)
        // ->with('requester', Auth::user()->name)
        // ->with('service', $service);
    }
	public function cekSemuaAtasan($requester) {
		$contact = DB::table('contact')->where('id',$requester)->first();
		$atasan = [];
		$job_title_id = $contact->job_title;
		$job_title = DB::table('job_title')->where('id',$job_title_id)->first();
		if(empty($job_title)) {
			return false;
		}

		$parent_job_title_id = $job_title->parent;
		while($parent_job_title_id) {
			$job_title = DB::table('job_title')->where('id',$parent_job_title_id)->first();
			if($job_title) {
				//job title harus ada juga
				$contact = DB::table('contact')->where('job_title',$job_title->id)->first();
				if($contact) {
					//dimasukan jika contakny tersedia, kalo tidak, maka posisi tidak terdeteksi
					$atasan[] = [
						'job_title_id'=>$job_title->id,
						'position_id'=>$contact->position,
						'contact_id'=>$contact->id,
						'name'=>$contact->name,
					];
				}
				$parent_job_title_id = $job_title->parent;
			} else {
				$parent_job_title_id = null;
			}
		}
		//foreach($job_title as $j) {

		//}

		//var_dump($atasan);
		//die;
		return $atasan;
	}
    public function store(Request $request)
    {
		$contact = Auth::user()->contact;

		if(empty($contact->job_title) || empty($contact->organization)){
			echo json_encode(["success"=>false,"message"=>"You dont have a Job title / Organization"]);
			die;
		}

        $input = $request->all();
		DB::beginTransaction();
        try {
			$input = $request->all();

			$service_id = $input['service_id'] ?? 0;

			$requester = $input['request_for'] == "other" ? (int) $input['requester'] : Auth::user()->person;
			$contact_id = $requester;
			$contact = DB::table('contact')->where('id',$contact_id)->first();
			//cek lokasi dan company
			//yang cocok di request management
			$request_management = getRequestManagement($service_id,$contact);
			//$request_management = DB::table('request_management')
									//->where('location',$contact->location)
									//->where('company',$contact->company)
									//->where('request_name',$service_id)->first();
			//var_dump($service_id);
			//var_dump($contact);
			if(empty($request_management)) {
				echo json_encode(["success"=>false,"message"=>"Sorry, Service Request Not Avaliable for this Requester"]);
				die;
			}
			//var_dump($request_management);
			//die;
			$form_builder = DB::table('form_builder')->where('id',$request_management->form_builder)->first();

			$list_filename = array();
			$list_file_url = [];
			$file = $request->file('file');
			if($request->hasFile('file'))
			{
				$filename = izrand(5).'-'.removeSpecialCharacters($file->getClientOriginalName());
				handleUpload($file,$filename,'/upload');
				$list_filename[] = $filename;
				$list_file_url[] = URL('/').'/uploads/'.$filename;
			}
			$file = $request->file('file2');
			if($request->hasFile('file2'))
			{
				$filename = izrand(5).'-'.removeSpecialCharacters($file->getClientOriginalName());
				handleUpload($file,$filename,'/upload');
				$list_filename[] = $filename;
				$list_file_url[] = URL('/').'/uploads/'.$filename;
			}
			$file = $request->file('file3');
			if($request->hasFile('file3'))
			{
				$filename = izrand(5).'-'.removeSpecialCharacters($file->getClientOriginalName());
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

			date_default_timezone_set('Asia/Jakarta');


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
				//var_dump($form_builder_json);die;
			}

			$sla = DB::table('sla')->where('id',$request_management->SLA_delivery ?? null)->first();
			$sla_json = empty($sla) ? null : json_encode($sla);

			$data_insert_or_update = [
					'sla_json' => $sla_json,
					'status' => ($input['submit_type'] == "draft")?'Draft':'Submit for Approval',
					'org_id' => $contact->organization ?? 0,
					'caller_id' => $input['caller'] ?? 0,
					'agent_id' => $input['agent'] ?? 0,
					'title' => $input['title'] ?? '-',
					'files' => $files,
					'files_url' => $files_url,
					'description' => $input['description'] ?? '-',
					'private_log' => $input['private_log'] ?? '-',
					'finalclass' => 'service_request',
					'upload_file' => $input['upload_file'] ?? '',
					'service_id' => $input['service_id'] ?? 0,
					'servicesubcategory_id' => $input['servicesubcategory_id'] ?? 0,
					'parent_incident_id' => $input['parent_incident'] ?? 0,
					'parent_problem_id' => $input['parent_problem_id'] ?? 0,
					'parent_change_id' => $input['parent_change'] ?? 0,
					'public_log' => $input['public_log'] ?? '-',
					'created_by' => Auth::user()->id,
					'created_by_contact' => Auth::user()->person,
					'updated_by' => Auth::user()->id,
					'created_at' => date("Y-m-d H:i:s"),
					'updated_at' => date("Y-m-d H:i:s"),
					'country' => $contact->country ?? 0,
					'request_management'=>$request_management->id,
					'company' => $contact->company ?? 0,
					'token' => generateRandomString(40),
					'data_json'=>json_encode($input),
					'form_data_json'=>$input_main['form_data_json'] ?? '-',
					'form_builder_json'=> (empty($form_builder_json)?"":$form_builder_json),
					'form_builder'=> $form_builder->id ?? null,

					//'next_approval_id' => $next_approval_id,//$input['approval_custom'],
					'requester' => $requester,
				];

			if($input['submit_type'] == "draft" && !empty($input['id'])) {
				//save as draft update
				$id = DB::table($table_submit)
							->where(['id'=>$input['id'],
									'created_by' => Auth::user()->id])
							->update(
								$data_insert_or_update
							);
			} else {
				if (!empty($input['id'])) {
					// delete ticket draft
					DB::table('ticket_draft')
					->where(['id'=>$input['id'],
							'created_by' => Auth::user()->id])
					->delete();
				}

				$id = DB::table($table_submit)->insertGetId(
					$data_insert_or_update
				);
			}
			if($input['submit_type'] == "draft") {
				$redirect = URL('/').'/myDraft';
				echo json_encode(["success"=>true,"message"=>"success","redirect"=>$redirect]);
				die;
			}

			$input['id'] = $id;



			//flow baru old system dicomment
			$ticket = DB::table('ticket')
						->where('id', $id)->first();
			$next_approval_id = 0;
			$contact_case_journey = getContactCaseJourney($ticket,"include self","not_include_request_management_notif");

			foreach($contact_case_journey as $contact) {
				//$next_approval_id =
				if(!empty($contact->step_approval)) {
					//cek kontak tsb adalah approver dan bukan yang melakukan approve saat ini

					$has_approve = DB::table('ticket_approval')
										->where('ticket_id',$ticket->id)
										->where('approval_id',$contact->id)
										->first();

					if ($has_approve) {

					} else {
						//belum approve
						//sudah dapat langsung keluar loop
						$next_approval_id = $contact->id;
						break;
					}
				}
			}
			//end: flow baru old system dicomment

			DB::table('ticket')
				->where('id', $id)
				->update(['ref' => $this->set_ref_id('service_request', $input['id'])
					,'next_approval_id' => $next_approval_id]);

			DB::table('ticket_log')->insertGetId(
				[
					'message' => 'System created Ticket with status <b>Submit for Approval</b> with type <b>Service</b>',
					'ticket_id' => $id,
					'created_at' => date("Y-m-d H:i:s"),
					'created_by' => Auth::user()->id,
				]
			);


			$token_email_approve = generateRandomString(40);
			DB::table('ticket_token')->insertGetId(
				[
					'ticket_id' => $id,
					'created_at' => date("Y-m-d H:i:s"),
					//'user_id' => Auth::user()->id,
					'contact_id' => $next_approval_id,
					'token'=>$token_email_approve,
				]
			);

			$content_notif = "<p>Ticket Activity: Request with Ticket Number ".ticketNumber($id)." has been Created.</p>
									<p>If you have more problem, you can contact us through available contact</p>";
			$title_notif = "Ticket Activity: Request with Ticket Number ".ticketNumber($id)." has been Created";
			sendNotifEmail(Auth::user()->person, $title_notif,$content_notif,"ticket_monitoring", $id);

			$content = "<p>You Get Approval Request with Ticket Number ".ticketNumber($id)." </p>
						<p>Please review this ticket, and approve this ticket to make this ticket assign to Agent.
						</p>
						<p>You can click this button to Approve this ticket</p>
						<a href='".URL('/')."/email_approve/".$token_email_approve."' class='btn btn-primary'>Approval Ticket</a>
						<p>You can reject this ticket if this ticket is not relevant.</p>";
			sendNotifEmail($next_approval_id, "You Get Approval Request with Ticket Number ".ticketNumber($id)." ", $content,"approve_request",$id);

			DB::commit();

			$redirect = URL('/').'/myServices';
			echo json_encode(["success"=>true,"message"=>"success","redirect"=>$redirect]);
			die;
			//Flash::success('Record has been saved');

			//return redirect('/ticket-monitoring');
		} catch (\Throwable $th) {
			DB::rollBack();
			Log::error($th->getMessage(). ' File: ' .$th->getFile(). ' Line: ' .$th->getLine());
			$redirect = URL('/').'/myServices';

			echo json_encode(["success"=>false,"message"=>"error","redirect"=>$redirect]);
			die;
		}
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
}
