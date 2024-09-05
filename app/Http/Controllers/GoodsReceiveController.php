<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\GoodsIssue;
use App\Models\GoodsDetail;
use App\Models\GoodsIssueLog;
use App\Models\GoodsReceive;
use App\Models\GoodsReceiveDetail;
use App\Models\GoodsReceiveLog;
use App\Models\InventoryType;
use App\Models\MaterialGroup;
use App\Models\Material;
use App\Models\MaterialCode;
use App\Models\User;
use App\Models\Warehouse;
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

class GoodsReceiveController extends Controller
{
    protected $module;
    protected $model;
    protected $title;

    public function __construct(GoodsReceive $model)
    {
        $this->module = 'goods_receive';
        $this->model = $model;
        $this->title = 'Goods Receive';

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
                'name' => 'Request a Goods Receive',
                'url' => 'request-incident/incident-catalog'
            ],
            [
                'name' => 'Create Goods Receive '.$service->name,
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

        $inventoryTypeDisposal = InventoryType::firstOrCreate(['title' => 'disposal'], ['title' => 'disposal']);

        $goods_issue = GoodsIssue::with('goods_receive')
            ->whereHas('details', function($q) {
                $q->where('status_return', GoodsDetail::STATUS_RETURN_ISSUED);
            })
            ->where('requestor', Auth::user()->person)
            ->where('status', 'full_approved')
            ->where('inventory_type_id', '!=', $inventoryTypeDisposal->id)
            ->get();

        $originator = $originator->get();

        $users = User::all(['id', 'name']);
        $inventoryTypes = InventoryType::where('title', '!=', 'active')
            ->where('transaction_type', 'goods_receive')
            ->get(['id', 'title']);
            
        $warehouses = Warehouse::all(['name', 'id'])->pluck('name', 'id')->toArray();
        $materialCodes = MaterialCode::all(['code', 'id'])->pluck('code', 'id')->toArray();
        $brands = Brand::all(['name', 'id'])->pluck('name', 'id')->toArray();

        return view('goods_receive.create')
            ->with('request_management',$request_management)
            ->with('breadcumb', $breadcumb)
            ->with('title','Create Goods Receive')
            ->with('originator', $originator)
            ->with('requester', Auth::user()->name)
            ->with('category', $this->module)
            ->with('service', $service)
            ->with('approval', "")
            ->with('type', 'goods_receive')
            ->with('goods_issue', $goods_issue)
            ->with('breadcrumb',$breadcumb)
            ->with('users',$users)
            ->with('warehouses',$warehouses)
            ->with('materialCodes',$materialCodes)
            ->with('brands',$brands)
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

            // dd($input);

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

            $inventory = GoodsReceive::latest('id')->first();

            $inventory2 = GoodsReceive::create([
                'code' => set_ref_id('goods_receive', $inventory ? (int)$inventory->id : 1),
                'subject' => $input['title'],
                // 'status' => $input['submit_type'],
                'goods_issue_id' => !empty($request->goods_issue_id)?$request->goods_issue_id:null,
                'status' => 'open',
                'inventory_type_id' => $input['inventory_type_id'],
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

            $newInventoryType = InventoryType::firstOrCreate(['id' => $input['inventory_type_id']], ['title' => 'new']);
            if(empty($request->issue_detail_ids) && $newInventoryType->title != 'new') {
                return back()->with('error', 'Please select minimal 1 item');
            }

            if(!empty($request->issue_detail_ids)) {
                GoodsDetail::whereIn('id', $request->issue_detail_ids)->update([
                    'status_return' => GoodsDetail::STATUS_RETURN_SELECTED,
                    'goods_receive_id' => $inventory2->id
                ]);
            }

            $userId = Auth::user()->id;
            $now = Date::now();

            if(empty($request->goods_issue_id)) {
                $datas = [];
                for($i = 0; $i < count($request->qty); $i++) {
                    $datas[] = [
                        'goods_receive_id' => $inventory2->id,
                        'material_code_id' => @$request->material_code_id[$i],
                        'store_location_id' => @$request->store_location_id[$i],
                        'status_return' => GoodsDetail::STATUS_RETURN_SELECTED,
                        'name' => @$request->name[$i],
                        'serial_number' => @$request->serial_number[$i],
                        'brand_id' => @$request->brand_id[$i],
                        'material_tag' => @$request->material_tag[$i],
                        'qty' => @$request->qty[$i],
                        'po_number' => @$request->po_number[$i],
                        'description' => @$request->material_description[$i],
                        'specification' => @$request->specification[$i],
                        'image' => null,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    if(isset($request->image[$i])) {
                        $file = $request->file("image")[$i];
                        $filename = izrand(5).'-'.$file->getClientOriginalName();
                        handleUpload($file,$filename);
                        $datas[$i]['image'] = "uploads/$filename";
                    }
                }
                
                GoodsDetail::insert($datas);
            }

			GoodsReceiveLog::create([
                'goods_receive_id' => $inventory2->id,
                'message' => 'New Goods Receive',
            ]);

            $input['id'] = $inventory2->id;

            //flow baru old system dicomment
            $ticket = DB::table('goods_receives')
                        ->where('id', $input['id'])->first();
            $next_approval_id = 0;
            $base_contact_case_journey = getInventoryManagementCaseJourneyReturn($inventory2,"include self","not_include_request_management_notif");

            foreach($base_contact_case_journey as $contact) {
                //$next_approval_id =
                if(!empty($contact->step_approval)) {
                    //cek kontak tsb adalah approver dan bukan yang melakukan approve saat ini

                    $has_approve = DB::table('goods_receive_approvals')
                                        ->where('goods_receive_id',$ticket->id)
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

            DB::table('goods_receives')
                ->where('id', $input['id'])
                ->update(['next_approver_id' => $next_approval_id]);

            $token_email_approve = generateRandomString(40);
            DB::table('goods_approval_tokens')->insertGetId(
                [
                    'goods_issue_id' => $request->goods_issue_id,
                    'goods_receive_id' => $input['id'],
                    'created_at' => date("Y-m-d H:i:s"),
                    //'user_id' => Auth::user()->id,
                    'contact_id' => $next_approval_id,
                    'token'=>$token_email_approve,
                ]
            );

            $content_notif = "<p>Inventory Activity: Request with Inventory Number ".goodsReceiveNumber($input['id'])." has been Created.</p>
                                <p>If you have more problem, you can contact us through available contact</p>";
            // dd($content_notif);
            $title_notif = "Inventory Activity: Request with Inventory Number ".goodsReceiveNumber($input['id'])." has been Created";
            sendNotifEmailInventory(Auth::user()->person, $title_notif,$content_notif,"goods_receives", $input['id']);

            $content = "<p>You Get Approval Request with Inventory Number ".goodsReceiveNumber($input['id'])." </p>
                        <p>Please review this inventory, and approve this inventory to make this inventory assign to Agent.
                        </p>
                        <p>You can Approve or Reject this inventory if this inventory is not relevant.</p>";
            sendNotifEmailInventory($next_approval_id, "You Get Approval Request with Inventory Number ".goodsReceiveNumber($input['id'])." ", $content,"approve_request",$input['id']);

			DB::commit();
			Flash::success('Data has been successfully saved');

            return redirect(route($this->module.'.index'));
        } catch (\Throwable $th) {
            DB::rollBack();

            dd($th->getMessage().$th->getLine().$th->getFile());

            // if(config('app.env') == 'local') dd($th->getMessage().$th->getLine().$th->getFile);

            return redirect(route($this->module.'.index'))->with('error', 'Something went wrong');
        }
    }

    public function show($id) {
        accessv($this->module, 'edit');


        $person_id = Auth::user()->person;

        $contact = DB::table('contact')->where('id',$person_id)->first();
        $detail = $this->model->with([
            'contactRequestor.jobTitle',
            'createdByUser',
            'nextApprover',
            'details.storeLocation',
            'goods_issue',
            'requestManagement.service'
        ])->find($id);

        if($detail->next_approver_id || strtolower($detail->status) == 'rejected') {
            $detail->load([
                'goods_issue.inventoryType',
                'goods_issue.details' => function($q) use($id) {
                    $q->where('goods_receive_id', $id)->whereIn('status_return', [GoodsDetail::STATUS_RETURN_SELECTED, GoodsDetail::STATUS_RETURN_HOLD]);
                },
                'goods_issue.details.materialCode.materialGroup',
                'goods_issue.details.material.storeLocation:id,name,warehouse_id',
                'goods_issue.details.pic',
            ]);
        } else {
            $detail->load([
                'goods_issue.inventoryType',
                'goods_issue.details' => function($q) use($id){
                    $q->where('goods_receive_id', $id)->where('status_return', '!=', GoodsDetail::STATUS_RETURN_SELECTED);
                },
                'goods_issue.details.materialCode.materialGroup',
                'goods_issue.details.material.storeLocation:id,name,warehouse_id',
                'goods_issue.details.pic',
            ]);
        }

        // dd($detail);
        $request_management = getRequestManagement($detail->service_id,$contact);

        $input['id'] = $detail->id;

        //flow baru old system dicomment
        $ticket = DB::table('goods_receives')
                    ->where('id', $input['id'])->first();
        $next_approval_id = 0;
        $contact_case_journey = getInventoryManagementCaseJourneyReturn($detail,"include self","not_include_request_management_notif");

        $is_alr_first_support_custom = false;

        $contactApprovalSupportCustoms = array_values(collect($contact_case_journey)->filter(function($row) {
            return $row->type_approval == 'approval support custom';
        })->toArray());

        $next_approver = null;
        foreach($contact_case_journey as $contact) {
            if(!empty($contact->step_approval)) {
                //cek kontak tsb adalah approver dan bukan yang melakukan approve saat ini

                $has_approve = DB::table('goods_receive_approvals')
                                    ->where('goods_receive_id',$ticket->id)
                                    ->where('approver_id',$contact->id)
                                    ->first();
                if ($has_approve) {

                } else {
                    // dd($contact);
                    //belum approve
                    //sudah dapat langsung keluar loop
                    $next_approver_id = $contact->id;
                    $next_approver = $contact;

                    if ($contact->id == Auth::user()->person &&
                        $contact->id == @$contactApprovalSupportCustoms[0]->id && 
                        $contact->type_approval == 'approval support custom'
                    ) {
                        $is_alr_first_support_custom = true;
                    }

                    break;
                }

                if ( $contact->id == Auth::user()->person && 
                    $contact->id == @$contactApprovalSupportCustoms[0]->id && 
                    $contact->type_approval == 'approval support custom'
                ) {
                    $is_alr_first_support_custom = true;
                }
            }
        }

        // dd($has_approve);

        return view($this->module.'.show', compact('detail', 'request_management', 'next_approver', 'is_alr_first_support_custom'));
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
            ->with('url','goods_receive')
            ->with('request_type',$moduleTitle);
    }

    public function getMaterialCodeDetail($materialCodeId) {
        try {
            $data = MaterialCode::with('materialGroup')->find($materialCodeId);

            return response()->json([
                'success' => false,
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
        $data = MaterialCode::with(['materialGroup', 'material.storeLocation.warehouse']);
        return DataTables::of($data)->make(true);
    }

    public function getIssueData(Request $request, $issue_id) {
        $data = GoodsIssue::with([
            'inventoryType',
            'inventoryManagementDetails' => function($q) {
                $q->where('status_return', GoodsDetail::STATUS_RETURN_ISSUED);
            },
            'inventoryManagementDetails.material.storeLocation.warehouse',
            'inventoryManagementDetails.materialCode.materialGroup',
            'inventoryManagementDetails.pic.contact'
        ])
            ->whereHas('details', function($q) {
                $q->where('status_return', GoodsDetail::STATUS_RETURN_ISSUED);
            })
            ->where('requestor', Auth::user()->person)
            ->where('status', 'full_approved')
            ->where('id', $issue_id)
            ->first();
        return $data;
    }

    public function getMaterialList(Request $request, $id) {
        $data = Material::where('material_code_id', $id)->with(['materialCode.materialGroup', 'storeLocation.warehouse']);
        return DataTables::of($data)->make(true);
    }
}
