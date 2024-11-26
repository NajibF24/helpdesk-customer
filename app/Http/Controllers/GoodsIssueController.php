<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Contact;
use App\Models\GoodsIssue;
use App\Models\GoodsDetail;
use App\Models\GoodsIssueLog;
use App\Models\InventoryType;
use App\Models\MaterialGroup;
use App\Models\Material;
use App\Models\MaterialCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Laracasts\Flash\Flash;
use Yajra\DataTables\Facades\DataTables;

class GoodsIssueController extends Controller
{
    protected $module;
    protected $model;
    protected $title;

    public function __construct(GoodsIssue $model)
    {
        $this->module = 'goods_issue';
        $this->model = $model;
        $this->title = 'Goods Issue';

        View::share('module', $this->module);
        View::share('title', $this->title);
    }

    public function index(Request $request) {
        accessv($this->module, 'list');

        if($request->ajax()) {
            $from = $request->from;
            $data = $this->model->with('created_by_user', 'inventory_type')
                ->where('next_approver_id', Auth::user()->person)
                ->when(empty($from), function($q) {
                    $q->orWhere('requestor', Auth::user()->person);
                });
                
            return DataTables::of($data)
                ->addColumn('action', $this->module.'.actions')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view($this->module.'.index');
    }

    public function create(Request $request) {
        accessv($this->module, 'create');
        $service_id = $request->query('request');
        $originator = DB::table('contact')->orderBy('name', 'ASC')->where('type','Employee');
        //$request_management = DB::table('request_management')->where('request_name',$service_id)->first();
        $service = DB::table('service')->where('id', $service_id)->first();
        $breadcumb = [
            [
                'name' => 'Request a Goods Issue',
                'url' => 'request-incident/incident-catalog'
            ],
            [
                'name' => 'Create Goods Issue '.$service->name,
                'url' => 'create/incident?category='.$this->module.'&request='.$request->query('request').'&target='.$request->query('target')
            ]
        ];

        $person_id = Auth::user()->person;
        //get from contact
        if($person_id) {
            $contact = DB::table('contact')->where('id',$person_id)->first();
            $originator = $originator->where('id',"!=", $person_id)->where('organization',$contact->organization);
            //cek lokasi dan company
            //yang cocok di request management
            $request_management = getRequestManagement($service_id,$contact);


        } else {
            return view('crudmodal.problem_not_found')->with('message',"Contact account not found")->with('title','Create Problem Request')->with('type',$type)->with('column',$column)->with('breadcrumb',$this->breadcrumb);
        }

        $originator = $originator->get();

        $users = User::all(['id', 'name']);
        $inventoryTypes = InventoryType::whereIn('title', ['borrow', 'deploy'])
            ->where('transaction_type', 'goods_issue')
            ->get(['id', 'title']);

        return view('goods_issue.create')
            ->with('request_management',$request_management)
            ->with('breadcumb', $breadcumb)
            ->with('title','Create Goods Issue')
            ->with('originator', $originator)
            ->with('requester', Auth::user()->name)
            ->with('category', $this->module)
            ->with('service', $service)
            ->with('approval', "")
            ->with('type', 'goods_issue')
            ->with('breadcrumb',$breadcumb)
            ->with('users',$users)
            ->with('inventoryTypes',$inventoryTypes)
            ->with('route', 'problem-catalog.store');

        // return view($this->module.'.create', compact('materialCodes', 'brands'));
    }

    public function store(Request $request) {
        accessv($this->module, 'create');

        DB::beginTransaction();
        try {
            accessv($this->module,'create');

			$input = $request->all();

            unset($input['client_token']);//kadang2 ikut masuk
			unset($input['_token']);
			unset($input['input_call']);

			// $input = $this->processUpload($request, $input);

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
            if (empty($request_management->approval_support_custom)) {
				echo json_encode(["success"=>false,"message"=>"Sorry, selected catalog doesnt have aproval support custom yet"]);
				die;
            }

			if(empty($request_management)) {
				echo json_encode(["success"=>false,"message"=>"Sorry, Request Not Avaliable for this Requester"]);
				die;
			}

			$form_builder = DB::table('form_builder')->where('id',$request_management->form_builder)->first();

			$service_id = $request_management->request_name;
			$service_category_id = DB::table('lnkservicetoservice_category')->where('service_id',$service_id)->value('service_category_id');

			$list_filename = array();
			$list_file_url = [];
			$file = $request->file('file');
			if($request->hasFile('file'))
			{
				$filename = izrand(5).'-'.$file->getClientOriginalName();
				handleUpload($file,$filename,'/upload');
				$list_filename[] = $filename;
				$list_file_url[] = 'uploads/'.$filename;
			}
			$file = $request->file('file2');
			if($request->hasFile('file2'))
			{
				$filename = izrand(5).'-'.$file->getClientOriginalName();
				handleUpload($file,$filename,'/upload');
				$list_filename[] = $filename;
				$list_file_url[] = 'uploads/'.$filename;
			}
			$file = $request->file('file3');
			if($request->hasFile('file3'))
			{
				$filename = izrand(5).'-'.$file->getClientOriginalName();
				handleUpload($file,$filename,'/upload');
				$list_filename[] = $filename;
				$list_file_url[] = 'uploads/'.$filename;
			}

			$files_url = implode(",",$list_file_url);

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
			}

            $inventory = GoodsIssue::latest('id')->first();
            // dd([
            //     'code' => set_ref_id('goods_issue', $inventory ? $inventory->id : 1),
            //     'subject' => $input['title'],
            //     // 'status' => $input['submit_type'],
            //     'status' => 'open',
            //     'inventory_type_id' => $input['type_id'],
            //     'description' => $input['description'],
            //     'files' => $files_url,
            //     'form_data_json'=>$input_main['form_data_json'] ?? '-',
            //     'form_builder_json'=> (empty($form_builder_json)?"":$form_builder_json),
            //     'form_builder_id'=> $form_builder->id ?? null,
            //     'requestor' => $input['request_for'] == "other" ? (int) $input['requester'] : Auth::user()->person,
            //     'company_id' => $contact->company,
            //     'request_management_id'=>$request_management->id,
            //     'service_id' => $service_id ?? 0,
            //     'servicesubcategory_id' => $service_category_id ?? 0,
            //     'token' => generateRandomString(40),
            //     'agent_id' => explode(',', $request_management->approval_support_custom)[0],
            // ]);

			$inventory2 = GoodsIssue::create([
                'code' => set_ref_id('goods_issue', $inventory ? $inventory->id : 1),
                'subject' => $input['title'],
                // 'status' => $input['submit_type'],
                'status' => 'open',
                'inventory_type_id' => $input['type_id'],
                'description' => $input['description'],
                'files' => $files_url,
                'form_data_json'=>$input_main['form_data_json'] ?? '-',
                'form_builder_json'=> (empty($form_builder_json)?"":$form_builder_json),
                'form_builder_id'=> $form_builder->id ?? null,
                'requestor' => $input['request_for'] == "other" ? (int) $input['requester'] : Auth::user()->person,
                'company_id' => $contact->company,
                'request_management_id'=>$request_management->id,
                'service_id' => $service_id ?? 0,
                'servicesubcategory_id' => $service_category_id ?? 0,
                'token' => generateRandomString(40),
                'agent_id' => explode(',', $request_management->approval_support_custom)[0],
            ]);

            $payloads = [];
            foreach($input['material_code_id'] as $key => $materialCodeId) {
                $payload = [
                    'goods_issue_id' => $inventory2->id,
                    'material_code_id' => $materialCodeId,
                    // 'qty' => $input['qty'][$key],
                    'qty' => 1,
                    'remarks' => $input['remarks'][$key],
                    'pic_user_id' => Auth::user()->id,
                    'start_date' => $input['dates_start'][$key] ?? null,
                    'end_date' => $input['dates_end'][$key] ?? null,
                    'status_return' => null,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                    'created_at' => Date::now(),
                    'updated_at' => Date::now(),
                ];

                // if(isset($input['dates'])) {
                //     $dates = explode(' - ', $input['dates'][$key]);
                //     $payload['start_date'] = date('Y-m-d', strtotime($dates[0]));
                //     $payload['end_date'] = date('Y-m-d', strtotime($dates[1]));
                // }

                if(isset($input['amount'])) {
                    $payload['amount'] = $input['amount'][$key];
                }

                if(isset($input['supplier'])) {
                    $payload['supplier'] = $input['supplier'][$key];
                }

                // if(isset($input['user_id'])) {
                //     $payload['pic_user_id'] = $input['user_id'][$key];
                // }

                $payloads[] = $payload;
            }

            GoodsDetail::insert($payloads);

			GoodsIssueLog::create([
                'goods_issue_id' => $inventory2->id,
                'message' => 'New Goods Issue',
            ]);

            $input['id'] = $inventory2->id;

            //flow baru old system dicomment
            $ticket = DB::table('goods_issues')
                        ->where('id', $input['id'])->first();
            $next_approval_id = 0;
            $contact_case_journey = getInventoryManagementCaseJourney($inventory2,"include self","not_include_request_management_notif");

            foreach($contact_case_journey as $contact) {
                //$next_approval_id =
                if(!empty($contact->step_approval)) {
                    //cek kontak tsb adalah approver dan bukan yang melakukan approve saat ini

                    $has_approve = DB::table('goods_issue_approvals')
                                        ->where('goods_issue_id',$ticket->id)
                                        ->where('approver_id',$contact->id)
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

            DB::table('goods_issues')
                ->where('id', $input['id'])
                ->update(['next_approver_id' => $next_approval_id]);

            $token_email_approve = generateRandomString(40);
            DB::table('goods_approval_tokens')->insertGetId(
                [
                    'goods_issue_id' => $input['id'],
                    'goods_receive_id' => null,
                    'created_at' => date("Y-m-d H:i:s"),
                    //'user_id' => Auth::user()->id,
                    'contact_id' => $next_approval_id,
                    'token'=>$token_email_approve,
                ]
            );

            $content_notif = "<p>Inventory Activity: Request with Inventory Number ".goodsIssueNumber($input['id'])." has been Created.</p>
                                <p>If you have more problem, you can contact us through available contact</p>";
            $title_notif = "Inventory Activity: Request with Inventory Number ".goodsIssueNumber($input['id'])." has been Created";
            sendNotifEmailInventory(Auth::user()->person, $title_notif,$content_notif,"goods_issue", $input['id']);

            $content = "<p>You Get Approval Request with Inventory Number ".goodsIssueNumber($input['id'])." </p>
                        <p>Please review and approve this request of inventory.
                        </p>
                        <a href='".URL('/')."/email_approve_inventory/".$token_email_approve."?type=issue' class='btn btn-primary'>Approve/Reject Ticket</a>
                        <p>You can Approve or Reject this inventory if this inventory is not relevant.</p>";
                        // dd($content);
                        // dd([$next_approval_id, "You Get Approval Request with Inventory Number ".goodsIssueNumber($input['id'])." ", $content,"goods_issue",$input['id']]);
            sendNotifEmailInventory($next_approval_id, "You Get Approval Request with Inventory Number ".goodsIssueNumber($input['id'])." ", $content,"goods_issue",$input['id']);
            // dd('a');
			DB::commit();
			Flash::success('Data has been successfully saved');

            return redirect(route($this->module.'.index'));
        } catch (\Throwable $th) {
            DB::rollBack();

            if(config('app.env') == 'local') dd($th->getMessage());

            return redirect(route($this->module.'.index'))->with('error', 'Something went wrong');
        }
    }

    public function show($id) {
        accessv($this->module, 'edit');


        $person_id = Auth::user()->person;

        $contact = DB::table('contact')->where('id',$person_id)->first();

        $detail = $this->model->with(
            'contactRequestor.jobTitle',
            'createdByUser',
            'inventoryType',
            'nextApprover',
            'details.materialCode.materialGroup',
            'details.material.storeLocation:id,name',
            'details.pic',
            'requestManagement.warehouses'
        )->find($id);
        $request_management = getRequestManagement($detail->service_id,$contact);

        $input['id'] = $detail->id;

        //flow baru old system dicomment
        $ticket = DB::table('goods_issues')
                    ->where('id', $input['id'])->first();
        $next_approval_id = 0;
        $contact_case_journey = getInventoryManagementCaseJourney($detail,"include self","not_include_request_management_notif", "", "need_list_contact_not_unique");
        // dd($contact_case_journey);
        $is_alr_first_support_custom = false;
        $next_approver = Contact::whereId($ticket->next_approver_id)->first(['id']);
        $journey = array_values(collect($contact_case_journey)->where('type_approval', 'approval support custom')->toArray());

        //jika user approval custom list nya index pertama maka dia adalah orang asset (yg dapat meng assign)
        if (@$journey[0]->id == Auth::user()->person && ($next_approver && $next_approver->id == Auth::user()->person)) {
            $is_alr_first_support_custom = true;
        }

        $warehouse_ids = $detail->requestManagement->warehouses->pluck('warehouse_id')->toArray();

        return view($this->module.'.show', compact('detail', 'request_management', 'next_approver', 'is_alr_first_support_custom', 'warehouse_ids'));
    }

    public function edit($id) {}

    public function update(Request $request, $id) {
        accessv($this->module, 'edit');

        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), $this->validationRules($id));

            if($validator->fails()) {
                return redirect(route($this->module.'.edit', $id))->withErrors($validator)->withInput();
            }

            $detail = $this->model->find($id);

            // dd(File::exists(asset($detail->image)));

            $data = $validator->safe()->toArray();

            // dd($detail);

            if($request->hasFile('image'))
			{
                // if($detail->image && Storage::exists(asset($detail->image))) Storage::delete(asset($detail->image));
                if($detail->image) Storage::delete(asset($detail->image));

                $file = $request->file('image');
				$filename = izrand(5).'-'.$file->getClientOriginalName();
				handleUpload($file,$filename);
			    $data['image'] = "uploads/$filename";
			}

            $detail->update($data);

            DB::commit();

			Flash::success('Data has been successfully saved');
            return redirect(route($this->module.'.index'));
        } catch (\Throwable $th) {
            DB::rollBack();

            if(config('app.env') == 'local') dd($th->getMessage());

            return redirect(route($this->module.'.index'))->with('error', 'Something went wrong');
        }
    }

    public function destroy($id) {
        accessv($this->module, 'delete');

        DB::beginTransaction();
        try {
            $this->model->find($id)->delete($id);

            DB::commit();

			Flash::success('Data has been successfully deleted');
            return redirect(route($this->module.'.index'));
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect(route($this->module.'.index'))->with('error', 'Something went wrong');
        }
    }

    public function validationRules($id = null) {
        $validation = [
            'name' => 'required',
            'serial_number' => 'required',
            'brand_id' => 'required',
            'material_code_id' => 'required',
            'material_tag' => 'required',
            'image' => 'nullable',
            'specification' => 'required',
            'qty' => 'required',
            'description' => 'nullable',
            'po_number' => 'required',
        ];

        if($id) {
            // $validation['code'] = 'required|unique:materials,code,'.$id.',id,deleted_at,NULL';
        }

        return $validation;
    }

    public function catalog(Request $request)
    {
        $moduleTitle = ucwords(str_replace('_',' ', $this->module));
        $title = $moduleTitle." Catalog";
        $breadcumb = ['Request a '.$title, $title.' Catalog'];
        $service_category = DB::table('service_category')
								->where('type', $this->module)
							    ->get();

        return view($this->module.'.catalog')
            ->with('title', $title)
            ->with('service_category', $service_category)
            ->with('breadcumb', $breadcumb)
            ->with('type',$this->module)
            ->with('url','goods_issue')
            ->with('request_type',$moduleTitle);
    }

    public function getMaterialCodeDetail($materialCodeId) {
        try {
            $data = MaterialCode::with('materialGroup')->find($materialCodeId);

            return response()->json([
                'success' => true,
                'message' => 'Success retrieve data',
                'data' => $data
            ]);
        } catch(\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }

    public function getMaterialCodeList(Request $request) {
        $activeInventoryType = InventoryType::firstOrCreate(['title' => 'available'], ['title' => 'available']);

        $data = MaterialCode::with(['materialGroup', 'material.storeLocation.warehouse'])->whereHas('material', function ($query) use($activeInventoryType){
            $query->where('qty', '>', 0)->where('inventory_type_id', $activeInventoryType->id)->orWhereNull('inventory_type_id');
        });
        return DataTables::of($data)->make(true);
    }

    public function getMaterialList(Request $request, $id) {
        $activeInventoryType = InventoryType::firstOrCreate(['title' => 'available'], ['title' => 'available']);

        $data = Material::where('material_code_id', $id)
            ->whereHas('storeLocation', function($q) use($request) {
                $q->whereIn('warehouse_id', json_decode($request->warehouse_ids));
            })
            ->where(function($q) use($activeInventoryType) {
                $q->where('inventory_type_id', $activeInventoryType->id)->orWhereNull('inventory_type_id');
            })
            ->where(function($q) {
                $q->where('qty', '>', 0)
                    ->orWhereDoesntHave('goodsIssueDetails.goodsIssue', function($q) {
                        $q->whereNotNull('next_approver_id')->where('status', '!=', 'rejected');
                    });
            })
            ->with(['materialCode.materialGroup', 'storeLocation.warehouse']);

        return DataTables::of($data)->make(true);
    }

    public function assignMaterial($materialId, $goodsIssueDetailId) {
        try {

            $countGoodsDetail = GoodsDetail::where('material_id', $materialId)->whereHas('goodsIssue', function($q) {
                $q->whereNotNull('next_approver_id')->where('status', '!=', 'rejected');
            })->count();

            $material = Material::where('id', $materialId)->first(['qty']);

            //validasi jika material sudah dalam transaksi lain
            if($countGoodsDetail == $material->qty) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed Assign Material, all Qty already assigned',
                    'data' => []
                ]);
            }

            $data = GoodsDetail::where('id', $goodsIssueDetailId)
                ->update([
                    'material_id' => $materialId
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Success Assign Material',
                'data' => $data
            ]);
        } catch(\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }

    public function replyComment(Request $request)
    {
        DB::beginTransaction();
		try {
            $input = $request->all();

            if(empty($input['message']) || $input['message'] == '<p><br></p>') {
                return json_encode(["success" => false, 'message' => "Please input your reply", ]);
            }

            $ticket = DB::table($request->type.'s')->where('id',$input['id'])->first();
            $user = User::whereId($ticket->created_by)->first(['id', 'person']);
            $ticket->created_by_contact = $user->person;

            if(empty($ticket)) {
                echo json_encode(["success" => false, 'message' => "Transaction not found", ]);
                die;
            }

            $data = [
                $request->type.'_id' => $request->id,
                'message' => $input['message'],
                'user_id'=> Auth::user()->id,
                'contact_id'=> Auth::user()->person,
				'mode' => 'Response Info',
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ];

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

            $data['files'] = implode(",",$list_filename);
            $data['files_url'] = implode(",",$list_file_url);

            DB::table('inventory_comments') ->insert($data);

            $id = $ticket->id;

            $recipients = $input['notif'];

            if($request->type == 'goods_issue') {
                DB::table('goods_issue_logs')->insertGetId(
                    [
                        'message' => '<a href="#">'.Auth::user()->name.'</a> is reply a new comment',
                        'goods_issue_id' => $id,
                        'created_at' => date("Y-m-d H:i:s"),
                        'created_by' => Auth::user()->id,
                    ]
                );
        
                $content = "<p>".Auth::user()->name." has reply a new comment to inventory transaction with Transaction Number ".goodsIssueNumber($id)."</p>
                <div style='border: 2px solid #333; box-shadow: 0px 10px 15px -3px rgba(0,0,0,0.1);'>
                    <div style='padding: 10px;'>
                        <p style='background-color: #fff;'>
                        ".$input['message']."
                        </p>
                    </div>
                </div>
                
                <p>If you have more problem, you can contact us through available contact</p>";
                notif_to_all_needed_contact_inventory($id,$ticket,Auth::user()->name." has reply a new comment to ticket with Ticket Number ".goodsIssueNumber($id),$content,$request->type,$recipients);
            } else {
                DB::table('goods_receive_logs')->insertGetId(
                    [
                        'message' => '<a href="#">'.Auth::user()->name.'</a> is reply a new comment',
                        'goods_receive_id' => $id,
                        'created_at' => date("Y-m-d H:i:s"),
                        'created_by' => Auth::user()->id,
                    ]
                );
        
                $content = "<p>".Auth::user()->name." has reply a new comment to inventory transaction with Transaction Number ".goodsReceiveNumber($id)."</p>
                <div style='border: 2px solid #333; box-shadow: 0px 10px 15px -3px rgba(0,0,0,0.1);'>
                    <div style='padding: 10px;'>
                        <p style='background-color: #fff;'>
                        ".$input['message']."
                        </p>
                    </div>
                </div>
                
                <p>If you have more problem, you can contact us through available contact</p>";
                notif_to_all_needed_contact_inventory($id,$ticket,Auth::user()->name." has reply a new comment to ticket with Ticket Number ".goodsReceiveNumber($id),$content,$request->type,$recipients);
            }

            DB::commit();
            $html = view('goods_issue.comments')->with('detail',$ticket)->with('type', $request->type)->render();
            return json_encode(["success" => true, 'message' => "Your message has been sent", 'content'=>$html]);
        } catch (\Throwable $th) {
            DB::rollBack();

            dd($th->getMessage());

            return json_encode(["success" => false, 'message' => "Your message was failed to sent", 'content'=>null]);
        }
    }

    public function checkMaterialCode(Request $request, $materialCodeId) {
        $availableQty = 0;

        $goodsDetails = GoodsDetail::whereHas('material', function($q) use($materialCodeId){
            $q->where('material_code_id', $materialCodeId);
        })->whereHas('goodsIssue', function($q) {
            $q->whereNotNull('next_approver_id')->where('status', '!=', 'rejected');
        })->get();

        $materials = Material::where('material_code_id', $materialCodeId)
            ->get(['id', 'qty'])
            ->pluck('qty', 'id')
            ->toArray();

        foreach($goodsDetails as $goodsDetail) {
            $materials[$goodsDetail->material_id]--;
        }

        $availableQty = array_sum($materials);

        if($availableQty == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Failed Assign Material Code. All Serial Number already booked',
                'data' => []
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success checking',
            'data' => []
        ]);
    }
}
