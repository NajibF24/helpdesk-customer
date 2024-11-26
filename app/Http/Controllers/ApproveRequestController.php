<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\GoodsDetail;
use App\Models\GoodsIssue;
use App\Models\GoodsIssueApproval;
use App\Models\GoodsIssueDetail;
use App\Models\GoodsIssueLog;
use App\Models\GoodsReceive;
use App\Models\GoodsReceiveApproval;
use App\Models\GoodsReceiveLog;
use App\Models\InventoryType;
use App\Models\Material;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;

use App\Models\User;
use DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ApproveRequestController extends Controller {

    public function index(Request $request)
    {
        $title = "Approve Request";
		$breadcumb = [
			[
				'name' => 'Approve Request',
				'url' => 'approve-request'
			]
		];

		if ($request->query('type') == 'all') {
			$title = "All Approve Request";
			$breadcumb = [
				[
					'name' => 'All Approve Request',
					'url' => 'approve-request?type=all'
				]
			];

			// $tickets = DB::table('ticket')
			// ->select(DB::raw('ticket.*, ticket_approval.ticket_id, ticket_approval.approval_id'))
			// ->orderBy('id','desc')->leftJoin('ticket_approval', 'ticket_approval.ticket_id', '=', 'ticket.id')
			// ->whereRaw(' (ticket.next_approval_id = ? OR ticket_approval.approval_id= ?) ', [Auth::user()->person, Auth::user()->person])
			// ->whereNotIn('ticket.status', ['Withdrawn'])
			// ->get();
            $tickets = DB::table('ticket')
            ->select(DB::raw('ticket.*, ticket_approval.ticket_id, ticket_approval.approval_id'))
            ->orderBy('id', 'desc')
            ->leftJoin('ticket_approval', 'ticket_approval.ticket_id', '=', 'ticket.id')
            ->whereRaw('(ticket.next_approval_id = ? OR ticket_approval.approval_id = ?)', [Auth::user()->person, Auth::user()->person])
            ->whereNotIn('ticket.status', ['Withdrawn'])
            ->distinct('ticket.id') // Add this line
            ->get();
		} else {
			$tickets = DB::table('ticket')
			->orderBy('id','desc')
			->where('status', 'Submit for Approval')
			->where('next_approval_id', Auth::user()->person)
			->get();
		}

        return view('approve-request.index')
            ->with('tickets', $tickets)
            ->with('title', $title)
            ->with('breadcumb', $breadcumb);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $ticket = DB::table('ticket')->where('id', $id)->first();

        DB::table('ticket')
			->where('id', $id)
			->update(
                [
                    'next_approval_id' => $input['status'] == "rejected" ? 0 : $input['approval_custom'],
                    'status' => $input['status'],
                    'reason' => $input['reason'] ?? '-',
                    'approval_state' => $input['approval_state'] ?? '-',
                    'updated_by' => Auth::user()->id,
                    'updated_at' => date("Y-m-d H:i:s")
                ]
            );

        DB::table('ticket_approval')->insertGetId(
            [
                'ticket_id' => $id,
                'approval_id' => $input['approval_custom'] ?? $ticket->next_approval_id,
                'status' => $input['status'],
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]
        );


        Flash::success('Record has been saved');

        return redirect('/approve-request');
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
	public function email_approve($token) {
		$ticket_token = DB::table('ticket_token')->where('token',$token)->first();
		if($ticket_token) {
			$user = User::where('person', $ticket_token->contact_id)->first();
			if(empty($user)) {
				echo "User Account not found for Employee. Please create your user login for Employee, and then you can continue to approve this request";
				die;
			}
			Auth::login($user);

			$id = $ticket_token->ticket_id;

			$ticket = DB::table('ticket')->where('id',$id)->first();
			$title = "Detail Request ".$ticket->title;
			$breadcumb = ['Approve Request', $ticket->title];
			$ticket_statuses = DB::table('ticket_approval')->where('ticket_id', $id)->get();

			//echo $approval_id;
			//var_dump($next_is_assignment);
			return view('approve-request.show_email_approve')
				->with('ticket', $ticket)
				->with('title', $title)
				->with('breadcumb', $breadcumb)
				->with('statuses', $ticket_statuses)
				;
       } else {
			echo "invalid link";
	   }
	}
	public function email_approve_inventory(Request $request, $token) {
		$goods_approval_tokens = DB::table('goods_approval_tokens')->where('token',$token)->first();

		if(empty($goods_approval_tokens)) {
			echo "invalid token";exit;
		}

        if (empty($request->type)) {
			echo "empty type";exit;
        }elseif(!in_array($request->type, ['issue', 'receive'])){
			echo "invalid type";exit;
        }
        $type = $request->type;

		$expiryTime = (new DateTime($goods_approval_tokens->created_at))->modify('+14 days')->format('Y-m-d H:i:s');   // Create a DateTime object

		if(date('Y-m-d H:i:s') > $expiryTime) {
			echo "token expired, please approve using website portal";exit;
		}

		if($goods_approval_tokens && $type=='issue') {
			$user = User::where('person', $goods_approval_tokens->contact_id)->first();
			if(empty($user)) {
				echo "User Account not found for Employee. Please create your user login for Employee, and then you can continue to approve this request";
				die;
			}
			Auth::login($user);

			$id = $goods_approval_tokens->goods_issue_id;

			$inventory = DB::table('goods_issues')->where('id',$id)->first();
			$title = "Detail Request ".$inventory->subject;
			$breadcumb = ['Approve Request', $inventory->subject];
			$inventory_statuses = DB::table('goods_issue_approvals')->where('goods_issue_id', $id)->get();

			//echo $approval_id;
			//var_dump($next_is_assignment);
			return view('approve-request.show_email_approve_inventory')
				->with('inventory', $inventory)
				->with('title', $title)
				->with('type', $type)
				->with('breadcumb', $breadcumb)
				->with('statuses', $inventory_statuses);
       }elseif($goods_approval_tokens && $type=='receive'){
            $user = User::where('person', $goods_approval_tokens->contact_id)->first();
            if(empty($user)) {
                echo "User Account not found for Employee. Please create your user login for Employee, and then you can continue to approve this request";
                die;
            }
            Auth::login($user);

            $id = $goods_approval_tokens->goods_receive_id;

            $inventory = DB::table('goods_receives')->where('id',$id)->first();
            $title = "Detail Request ".$inventory->subject;
            $breadcumb = ['Approve Request', $inventory->subject];
            $inventory_statuses = DB::table('goods_receive_approvals')->where('goods_receive_id', $id)->get();

            //echo $approval_id;
            //var_dump($next_is_assignment);
            return view('approve-request.show_email_approve_inventory')
                ->with('inventory', $inventory)
                ->with('title', $title)
                ->with('type', $type)
                ->with('breadcumb', $breadcumb)
                ->with('statuses', $inventory_statuses);
       } else {
			echo "invalid link";
	   }
	}
	public function ticket_detail($token)
	{
		$ticket_token = DB::table('ticket')->where('token',$token)->first();
		if($ticket_token) {
			return redirect('/ticket-monitoring/'.$token);
       } else {
			echo "invalid link";
	   }
	}

    public function ticketActionInventory(Request $request) {
        $input = $request->all();
		// dd($input);
		$id = $request['id'];
        // dd(explode(',', $request->issue_detail_ids));
        // dd($request->all());

		$validation = Validator::make($input, [
			'message' => 'required'
		]);

		if($validation->fails()) {
			return json_encode(["success" => false, 'message' => "Please insert the reason."]);
		}

        $type = $request->type;

		if(!in_array($type, ['receive', 'issue'])) {
			return json_encode(["success" => false, 'message' => "Invalid trx type."]);
		}


		$ticket = DB::table('goods_'.$type.'s')->where('id',$id)->first();
		$invType = InventoryType::whereId($ticket->inventory_type_id)->first(['title']);

		if($ticket->next_approver_id != Auth::user()->person) {
            // dd([$ticket, Auth::user()->person]);
			echo json_encode(["success" => false, 'message' => "You don't need to approve this ticket, because you are not next approver for this ticket. Please contact Admin you have more problem."]);
			die;
		}

        //$next_approval_state = "";
		$next_is_assignment = 0;
		//$dapet = false;
		$next_approver_id = "";
		//$transisi_dari_approval_user_ke_support = false;
		$request_management = DB::table('request_management')->where('id', $ticket->request_management_id)->first();
		DB::beginTransaction();
        try {
            if ($type == 'issue') {
                $contact_case_journey = getInventoryManagementCaseJourney($ticket,"include self","not_include_request_management_notif","","need_list_contact_not_unique");
            }else{
                $contact_case_journey = getInventoryManagementCaseJourneyReturn($ticket,"include self","not_include_request_management_notif");
            }

			// dd($contact_case_journey);

			$contactApprovalSupportCustoms = array_values(collect($contact_case_journey)->where('type_approval', 'approval support custom')->toArray());

			if (@$contactApprovalSupportCustoms[0]->id == Auth::user()->person) {
                if ($type == 'issue') {
                    $goodsIssueDetails = GoodsDetail::whereNull('material_id')->where('goods_'.$type.'_id', $ticket->id)->exists();

                    if($goodsIssueDetails) {
                        echo json_encode(["success" => false, 'message' => 'Please assign all the material']);
                        die;
                    }
                }
			}
            $is_alr_first_support_custom = false;

			// dd($contact_case_journey);

			foreach($contact_case_journey as $contact) {
				if(!empty($contact->step_approval)) {
					if ($contact->id == Auth::user()->person &&
							$contact->id == @$contactApprovalSupportCustoms[0]->id && 
							$contact->type_approval == 'approval support custom'
						) {
							$is_alr_first_support_custom = true;
						}
				}		


				//$next_approval_id =
				if(!empty($contact->step_approval) && ($contact->id != Auth::user()->person)) {
					//cek kontak tsb adalah approver dan bukan yang melakukan approve saat ini

					// $has_approve = DB::table('ticket_approval')
					// 					->where('ticket_id',$ticket->id)
					// 					->where('approval_id',$contact->id)
					// 					->first();

					if (!empty($contact->has_approved) && $contact->has_approved) {

					} else {
						//belum approve
						//sudah dapat langsung keluar loop
						$next_approver_id = $contact->id;

						// if(Auth::user()->person == $contact_case_journey[count($contact_case_journey) - 1]->id) {
						// 	$next_approver_id = null;
						// }

						break;
					}
				}
			}

			if ($type == 'receive' && $is_alr_first_support_custom) {
				$items = !empty($request->issue_detail_ids) ? explode(',', $request->issue_detail_ids) : [];

				if(count($items) == 0) {
					echo json_encode(["success" => false, 'message' => 'Please select at least one material to approve.']);
                    die; 
				}
			}

			$status = 'Submit for Approval';
			if(!$next_approver_id) {
				$status = 'Open';
				$next_is_assignment = true;
			}

			//var_dump($next_approver_id);
			//var_dump($status);
			//var_dump("nextisassignment".$next_is_assignment);
			//die;

			if ($id) {

				DB::table('goods_'.$type.'_approvals')->insertGetId(
					[
						'goods_'.$type.'_id' => $id,
						'approver_id' => Auth::user()->person,//$request['approval_custom'] ?? 0,
						'status' => "approved",
						'reason' => $request['message'] ?? '-',
						'created_by' => Auth::user()->id,
						'updated_by' => Auth::user()->id,
						'created_at' => date("Y-m-d H:i:s"),
						'updated_at' => date("Y-m-d H:i:s")
					]
				);
				
				DB::table('inventory_comments')->insert([
					'message' => $request['message'],
					'goods_'.$type.'_id' => $id,
					'user_id' => Auth::user()->id,
					'contact_id'=> Auth::user()->person,
					'mode' => 'Approve Reason',
					'created_at' => date("Y-m-d H:i:s"),
					'updated_at' => date("Y-m-d H:i:s"),
					'created_by' => Auth::user()->id,
					'updated_by' => Auth::user()->id,
				]);


				// dd($is_alr_first_support_custom);

                if ($type == 'receive' && $is_alr_first_support_custom) {
                    foreach (explode(',', $request->issue_detail_ids) as $key => $value) {
						$giDetail = GoodsDetail::where('status_return', GoodsDetail::STATUS_RETURN_SELECTED)
							->where('goods_receive_id', $id)
							->find($value);

						if($request->store_location_ids) {
							Material::where('id', @$giDetail->material_id)->update([
								'store_location_id' => $request->store_location_ids[$key]
							]);
						}

						$giDetail->update(
                            [
								'condition' => $request->conditions[$key],
                                'status_return' => "hold",
                            ]
                        );
                        
                    }
                }


				if(!$next_is_assignment) {//belum assignment

					DB::table('goods_'.$type.'_logs')->insertGetId(
						[
							'message' => 'Ticket has been approved by <a href="#">'.Auth::user()->name.'</a> and is forwared to next approver',
							'goods_'.$type.'_id' => $id
						]
					);

					if($next_approver_id) {

                        DB::table('goods_'.$type.'s')->where('id', $id)->update(
                            [
                                'next_approver_id' => $next_approver_id,
                                'status' => "approved",
                            ]
                        );
					}

					$token_email_approve = generateRandomString(40);
                    if ($type == 'issue') {
                        $goods_number = goodsIssueNumber($id);
                        $gat = [
							'goods_issue_id' => $id,
							'goods_receive_id' => null,
							'created_at' => date("Y-m-d H:i:s"),
							//'user_id' => Auth::user()->id,
							'contact_id' => $next_approver_id ?? 0,
							'token'=>$token_email_approve,
                        ];
                    }else{
                        $goods_number = goodsReceiveNumber($id);
                        $gat = [
							'goods_issue_id' => $ticket->goods_issue_id,
							'goods_receive_id' => $id,
							'created_at' => date("Y-m-d H:i:s"),
							//'user_id' => Auth::user()->id,
							'contact_id' => $next_approver_id ?? 0,
							'token'=>$token_email_approve,
                        ];
                    }
					DB::table('goods_approval_tokens')->insertGetId($gat);

                    if ($is_alr_first_support_custom) {
                        // dd($next_approver_id);
                        $content = "<p>You Get Assign Request with Inventory Number ".$goods_number." </p>
                            <p>Please review this inventory, and assign this inventory to proceed.
                            </p>
                            <p>You can click this button to Assign this inventory</p>
                            <a href='".env('MAIL_AGENT_REDIRECT_URL')."/goods_$type/".$ticket->id."' class='btn btn-primary'>Assign Inventory</a>
                            <p>You can ignore this inventory if this inventory request is not relevant.</p>";
                        sendNotifEmailInventory($next_approver_id, "You Get Handle Request with Inventory Number ".$goods_number." ", $content,"goods_$type",$id);
                        // exit;

                        date_default_timezone_set('Asia/Jakarta');

                        $content = "<p>Your inventory with Inventory Number ".$goods_number." has been approved by ".Auth::user()->name."</p>
                                    <p>Your inventory is forwarded to next Approver</p>
                                    <p>If you have more problem, you can contact us through available contact</p>";
                        sendNotifEmailByUserIdInventory($ticket->created_by, "Your inventory with Inventory Number ".$goods_number." has been approved by ".Auth::user()->name, $content,"goods_$type",$id);


                        echo json_encode(["success" => true, 'message' => "This ticket has been approved and then this ticket is forwarded to the next Approver."]);
                    }else{
                        $content = "<p>You Get Approval Request with Inventory Number ".$goods_number." </p>
                            <p>Please review this inventory, and approve this inventory to make this inventory assign to Agent.
                            </p>
                           	<a href='".URL('/')."/email_approve_inventory/".$token_email_approve."?type=". $type ."' class='btn btn-primary'>Approve/Reject Ticket</a>
                            <p>You can reject this inventory if this inventory is not relevant.</p>";
                        sendNotifEmailInventory($next_approver_id, "You Get Approval Request with Inventory Number ".$goods_number." ", $content,"goods_$type",$id);

                        date_default_timezone_set('Asia/Jakarta');


                        $content = "<p>Your inventory with Inventory Number ".$goods_number." has been approved by ".Auth::user()->name."</p>
                                    <p>Your inventory is forwarded to next Approver</p>
                                    <p>If you have more problem, you can contact us through available contact</p>";
                        sendNotifEmailByUserIdInventory($ticket->created_by, "Your inventory with Inventory Number ".$goods_number." has been approved by ".Auth::user()->name, $content,"goods_$type",$id);

                        echo json_encode(["success" => true, 'message' => "This Request has been approved."]);

                    }
				}
				else {
					DB::table('goods_'.$type.'_logs')->insertGetId(
						[
							'message' => 'Ticket has been approved by <a href="#">'.Auth::user()->name.'</a>',
							'goods_'.$type.'_id' => $id,
							// 'created_at' => date("Y-m-d H:i:s"),
							// 'created_by' => Auth::user()->id,
						]
					);
                    // dd($request->all());

					$materialIds = [];
					$goods_issue_detail_ids = [];
                    if ($type == 'issue') {
                        $materialIds = GoodsDetail::where('goods_'.$type.'_id', $ticket->id)
                            ->get(['material_id'])
                            ->pluck('material_id')
                            ->toArray();
                    }else{
                        if(!empty($invType) && strtolower($invType->title) != 'new') {
							$goods_receive = GoodsReceive::with(['goods_issue.details' => function ($query) use($ticket){
								// Add a condition to filter the related details
								$query->where('goods_receive_id', $ticket->id)->where('status_return', 'hold');
							}])
							->where('id', $ticket->id)
							->first();

							foreach ($goods_receive->goods_issue->details as $key => $value) {
								$materialIds[] = $value->material_id;
								$goods_issue_detail_ids[] = $value->id;
							}
						}
                    }

					$uid = Auth::user()->id;

                    if ($type == 'issue') {
						$gi = GoodsIssue::find($ticket->id);

						$inventoryTypeActive = InventoryType::firstOrCreate(['title' => 'available'], ['title' => 'available']);

						foreach($materialIds as $materialId) {
							$mat = Material::where('id', $materialId)->first(['id', 'qty']);


							if($mat->qty == 1) {
								Material::where('id', $materialId)->decrement('qty', 1, ['inventory_type_id' => $gi->inventory_type_id]);
							} else {
								Material::where('id', $materialId)->decrement('qty', 1, ['inventory_type_id' => $inventoryTypeActive->id]);
							}
						}

                        GoodsDetail::where('goods_'.$type.'_id', $ticket->id)
                            ->update(['status_return' => GoodsDetail::STATUS_RETURN_ISSUED]);
                        $gi->update(['status' => 'full_approved', 'next_approver_id' => null]);
                    }else{
						$inventoryType = InventoryType::firstOrCreate(['title' => 'available'], ['title' => 'available']);

						foreach($materialIds as $materialId) {
							Material::where('id', $materialId)->increment('qty', 1, ['inventory_type_id' => $inventoryType->id]);
						}

						if(count($goods_issue_detail_ids) == 0) {
							$materials = GoodsDetail::where('goods_receive_id', $ticket->id)
								->where('status_return', GoodsDetail::STATUS_RETURN_HOLD)
								->get();

							foreach($materials as $row) {
								$material = Material::create([
									'name' => $row->name,
									'material_code_id' => $row->material_code_id,
									'serial_number' => $row->serial_number,
									'brand_id' => $row->brand_id,
									'store_location_id' => $row->store_location_id,
									'material_tag' => $row->material_tag,
									'description' => $row->description,
									'specification' => $row->specification,
									'image' => $row->image,
									'qty' => $row->qty,
									'po_number' => $row->po_number,
									'inventory_type_id' => $inventoryType->id,
									'document' => null
								]);

								$row->update([
									'status_return' => GoodsDetail::STATUS_RETURN_APPROVED,
									'material_id' => $material->id
								]);
							}
						} else {
							GoodsDetail::whereIn('id', $goods_issue_detail_ids)->update([
								'status_return' => GoodsDetail::STATUS_RETURN_APPROVED,
							]);
						}
                      
                        GoodsReceive::find($ticket->id)->update(['status' => 'full_approved', 'next_approver_id' => null]);
                    }

					if($type == 'issue') {
                        $goods_number = goodsIssueNumber($id);
						$type = "goods_issue";
					} else {
						$goods_number = goodsReceiveNumber($id);
						$type = "goods_receive";
					}

					$content = "<p>Your inventory with Inventory Number ".$goods_number." has been approved by ".Auth::user()->name."</p>
						<p>Your inventory is fully approved</p>
						<p>If you have more problem, you can contact us through available contact</p>";
					sendNotifEmailByUserIdInventory($ticket->created_by, "Your inventory with Inventory Number ".$goods_number." has been approved by ".Auth::user()->name, $content,$type,$id);

					echo json_encode(["success" => true, 'message' => "This Request has been approved."]);

				}
			} else {
				echo json_encode(["success" => false, 'message' => "Inventory is not found"]);
			}

			DB::commit();
        } catch (Exception $e) {
			DB::rollBack();
			Log::error($e->getMessage());
            echo json_encode(["success" => false, 'message' => $e->getMessage().$e->getLine().$e->getFile()]);
            die;
        }

	}

    public function ticketAction(Request $request) {
        $input = $request->all();
		$id = $request['id'];

		//var_dump($input);
		//die;
		$ticket = DB::table('ticket')->where('id',$id)->first();

		if($ticket->next_approval_id != Auth::user()->person) {
			echo json_encode(["success" => false, 'message' => "You don't need to approve this ticket, because you are not next approver for this ticket. Please contact Admin you have more problem."]);
			die;
		}

        //$next_approval_state = "";
		$next_is_assignment = 0;
		//$dapet = false;
		$next_approval_id = "";
		//$transisi_dari_approval_user_ke_support = false;
		$request_management = DB::table('request_management')->where('id', $ticket->request_management)->first();
        //try {
			//echo "WOO".$ticket->approval_state;

			$contact_case_journey = getContactCaseJourney($ticket,"include self","not_include_request_management_notif","","need_list_contact_not_unique");

			foreach($contact_case_journey as $contact) {
				//$next_approval_id =
				if(!empty($contact->step_approval) && ($contact->id != Auth::user()->person)) {
					//cek kontak tsb adalah approver dan bukan yang melakukan approve saat ini

					// $has_approve = DB::table('ticket_approval')
					// 					->where('ticket_id',$ticket->id)
					// 					->where('approval_id',$contact->id)
					// 					->first();

					if (!empty($contact->has_approved) && $contact->has_approved) {

					} else {
						//belum approve
						//sudah dapat langsung keluar loop
						$next_approval_id = $contact->id;

						if(Auth::user()->person == $contact_case_journey[count($contact_case_journey) - 1]->id) {
							$next_approval_id = null;
						}

						break;
					}
				}
			}

			$status = 'Submit for Approval';
			if(!$next_approval_id) {
				$status = 'Open';
				$next_is_assignment = true;
			}

			//var_dump($next_approval_id);
			//var_dump($status);
			//var_dump("nextisassignment".$next_is_assignment);
			//die;

			if ($id) {

				DB::table('ticket_approval')->insertGetId(
					[
						'ticket_id' => $id,
						'approval_id' => Auth::user()->person,//$request['approval_custom'] ?? 0,
						'status' => "approved",
						'reason' => $request['message'] ?? '-',
						'created_by' => Auth::user()->id,
						'updated_by' => Auth::user()->id,
						'created_at' => date("Y-m-d H:i:s"),
						'updated_at' => date("Y-m-d H:i:s")
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
							'mode'	=> 'Approve Reason',
						]);
				DB::table('schedule_execution')
					->where(['ref_id'=> $id,'type'=>'ticket','status'=>'Pending','action'=>'Send Email'])
					->update([
							'status' => 'No Need Send, Has Approved',]
					);

				if(!$next_is_assignment) {//belum assignment

					DB::table('ticket_log')->insertGetId(
						[
							'message' => 'Ticket has been approved by <a href="#">'.Auth::user()->name.'</a> and is forwared to next approver',
							'ticket_id' => $id,
							'created_at' => date("Y-m-d H:i:s"),
							'created_by' => Auth::user()->id,
						]
					);
					//echo "masuk sini";die;
					DB::table('ticket')
					->where('id', $id)
					->update(
						[
							'next_approval_id' => $next_approval_id ?? 0,
							'approval_state'=>$next_approval_state ?? '-',
							'status' => $status,
							'reason' => $input['reason'] ?? '-',
							'updated_by' => Auth::user()->id,
							'updated_at' => date("Y-m-d H:i:s")
						]
					);


					$token_email_approve = generateRandomString(40);
					DB::table('ticket_token')->insertGetId(
						[
							'ticket_id' => $id,
							'created_at' => date("Y-m-d H:i:s"),
							//'user_id' => Auth::user()->id,
							'contact_id' => $next_approval_id ?? 0,
							'token'=>$token_email_approve,
						]
					);

					$content = "<p>You Get Approval Request with Ticket Number ".ticketNumber($id)." </p>
						<p>Please review this ticket, and approve this ticket to make this ticket assign to Agent.
						</p>
						<p>You can click this button to Approve this ticket</p>
						<a href='".URL('/')."/email_approve/".$token_email_approve."' class='btn btn-primary'>Approve/Reject Ticket</a>
						<p>You can reject this ticket if this ticket is not relevant.</p>";
					sendNotifEmail($next_approval_id, "You Get Approval Request with Ticket Number ".ticketNumber($id)." ", $content,"approve_request",$id);

					date_default_timezone_set('Asia/Jakarta');

					DB::table('schedule_execution')->insertGetId(
						[
							'action' => 'Send Email',
							'recipient' => $next_approval_id ?? 0,
							'title' => "Reminder Approval Request with Ticket Number ".ticketNumber($id)." ",
							'data' => "<p>This is reminder Approval Request with Ticket Number ".ticketNumber($id)." </p>
									<p>Please review this ticket, and approve this ticket to make this ticket assign to Agent.
									</p>
									<p>You can reject this ticket if this ticket is not relevant.</p>",
							'ref_id' => $id,
							'type' => 'ticket',
							'status' => 'Pending',
							'execution_time'=> date("Y-m-d H:i:s", strtotime("+1 Minutes")),//Days
							'created_at' => date("Y-m-d H:i:s"),
							'created_by' => Auth::user()->id,
						]
					);

					DB::table('schedule_execution')->insertGetId(
						[
							'action' => 'Send Email',
							'recipient' => $next_approval_id ?? 0,
							'title' => "Reminder Approval Request with Ticket Number ".ticketNumber($id)." ",
							'data' => "<p>This is reminder Approval Request with Ticket Number ".ticketNumber($id)." </p>
									<p>Please review this ticket, and approve this ticket to make this ticket assign to Agent.
									</p>
									<p>You can reject this ticket if this ticket is not relevant.</p>",
							'ref_id' => $id,
							'type' => 'ticket',
							'status' => 'Pending',
							'execution_time'=> date("Y-m-d H:i:s", strtotime("+2 Minutes")),
							'created_at' => date("Y-m-d H:i:s"),
							'created_by' => Auth::user()->id,
						]
					);


					$content = "<p>Your ticket with Ticket Number ".ticketNumber($id)." has been approved by ".Auth::user()->name."</p>
								<p>Your ticket is forwarded to next Approver</p>
								<p>If you have more problem, you can contact us through available contact</p>";
					sendNotifEmailByUserId($ticket->created_by, "Your ticket with Ticket Number ".ticketNumber($id)." has been approved by ".Auth::user()->name, $content,"ticket_monitoring",$id);


					echo json_encode(["success" => true, 'message' => "This ticket has been approved and then this ticket is forwarded to the next Approver."]);

				}
				else {

					DB::table('ticket_log')->insertGetId(
						[
							'message' => 'Ticket has been approved by <a href="#">'.Auth::user()->name.'</a> ',
							'ticket_id' => $id,
							'created_at' => date("Y-m-d H:i:s"),
							'created_by' => Auth::user()->id,
						]
					);

					DB::table('ticket')
					->where('id', $id)
					->update(
						[
							'next_approval_id' => null,
							'approval_state'=>'assignment_tier',
							'status' => $status,
							'updated_by' => Auth::user()->id,
							'updated_at' => date("Y-m-d H:i:s"),
							'SLA_status' => 'Active',
						]
					);


					//HANDLE PROBLEM
					if($ticket->finalclass == 'problem_request') {
						$list_ticket = DB::table('lnktickettoproblem')
											->where('problem_ticket_id',$ticket->id)
											->join('ticket', 'ticket.id', '=', 'lnktickettoproblem.ticket_id')
											->get();
						foreach($list_ticket as $t) {
							//Parent Incident diclose
							//Parent Service tetep pause
							$parent_ticket = DB::table('ticket')->where('id', $t->id)->first();
							if($parent_ticket) {
								if($parent_ticket->finalclass == 'incident_management') {
									DB::table('ticket')
										->where('id', $parent_ticket->id)
										->update([
													'SLA_status' => 'Closed',
													//'remaining_SLA' => null,
													//'remaining_SLA_unit' => null,
													//'paused_at' => null,
													'due_date' => null
												]);

									//DB::table('ticket_log')->insertGetId(
										//[
											//'message' => 'Ticket Incident '.$parent_ticket->ref.' has been close, because ticket has been converted to Problem',
											//'ticket_id' => $id,
											//'created_at' => date("Y-m-d H:i:s"),
											//'created_by' => Auth::user()->id,
										//]
									//);

									//DB::table('ticket_log')->insertGetId(
										//[
											//'message' => 'Ticket Incident '.$parent_ticket->ref.' has been close, because ticket has been converted to Problem',
											//'ticket_id' => $parent_ticket->id,
											//'created_at' => date("Y-m-d H:i:s"),
											//'created_by' => Auth::user()->id,
										//]
									//);
								}
							}
						}
					}
					//END HANDLE PROBLEM


					//echo "masuk assignment";die;
					//LANJUT KE ASSIGNMNET TIER
					//CEK MANUAL ATAU AUTO
					if(!empty($input['agent_id'])) {
						$agent_id = $input['agent_id'];
					} else {
						$agent_id = null;
					}

					$ticket = DB::table('ticket')->where('id',$id)->first();
					$request_management = DB::table('request_management')->where('id', $ticket->request_management)->first();
					$this->assignment_tier($id,$request_management,$agent_id);
				}
			} else {
				echo json_encode(["success" => false, 'message' => "Ticket is not found"]);
			}

        //} catch (Exception $e) {
            //$next_approval_id = "";
            //echo json_encode(["success" => false, 'message' => $e->getMessage()]);
            //die;
        //}

	}

	public function rejectGoodsReceive(Request $request, $id) {
		DB::beginTransaction();
		try {
			$validation = Validator::make($request->all(), [
				'message' => 'required'
			]);
	
			if($validation->fails()) {
				return json_encode(["success" => false, 'message' => "Please insert the reason."]);
			}

			GoodsReceiveApproval::create([
				'goods_receive_id' => $id,
				'approver_id' => Auth::user()->person,//$request['approval_custom'] ?? 0,
				'status' => "rejected",
				'reason' => $request['message'] ?? '-',
				'created_by' => Auth::user()->id,
				'updated_by' => Auth::user()->id,
				'created_at' => date("Y-m-d H:i:s"),
				'updated_at' => date("Y-m-d H:i:s")
			]);

			GoodsReceiveLog::create([
				'goods_receive_id' => $id,
				'message' => $request['message'] ?? '-'
			]);

            GoodsReceive::where('id', $id)
                ->update([
                    'status' => "rejected",
					'next_approver_id' => null
                ]);

			DB::table('inventory_comments')->insert([
				'message' => $request['message'],
				'goods_receive_id' => $id,
				'user_id' => Auth::user()->id,
				'contact_id'=> Auth::user()->person,
				'mode' => 'Reject Reason',
				'created_at' => date("Y-m-d H:i:s"),
				'updated_at' => date("Y-m-d H:i:s"),
				'created_by' => Auth::user()->id,
				'updated_by' => Auth::user()->id,
			]);

			$gr = GoodsReceive::whereId($id)->first(['id', 'code', 'created_by']);

			$content = "<p>Your inventory with Inventory Number ".$gr->code." has been rejected by ".Auth::user()->name."</p>
				<p>Your inventory has been rejected</p>
				<p>If you have more problem, you can contact us through available contact</p>";
			sendNotifEmailByUserIdInventory($gr->created_by, "Your inventory with Inventory Number ".$gr->code." has been rejected by ".Auth::user()->name, $content,'goods_receive',$id);

			DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Success reject',
                'data' => []
            ]);
        } catch(\Throwable $th) {
			DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
	}

	public function rejectGoodsIssue(Request $request, $id) {
		DB::beginTransaction();
		try {

			// dd($request->all());
			$validation = Validator::make($request->all(), [
				'message' => 'required'
			]);
	
			if($validation->fails()) {
				return json_encode(["success" => false, 'message' => "Please insert the reason."]);
			}

			GoodsIssueApproval::create([
				'goods_issue_id' => $id,
				'approver_id' => Auth::user()->person,//$request['approval_custom'] ?? 0,
				'status' => "rejected",
				'reason' => $request['message'] ?? '-',
				'created_by' => Auth::user()->id,
				'updated_by' => Auth::user()->id,
				'created_at' => date("Y-m-d H:i:s"),
				'updated_at' => date("Y-m-d H:i:s")
			]);

			GoodsIssueLog::create([
				'goods_issue_id' => $id,
				'message' => $request['message'] ?? '-'
			]);

            GoodsIssue::where('id', $id)
                ->update([
                    'status' => "rejected",
					'next_approver_id' => null
                ]);

			DB::table('inventory_comments')->insert([
				'message' => $request['message'],
				'goods_issue_id' => $id,
				'user_id' => Auth::user()->id,
				'contact_id'=> Auth::user()->person,
				'mode' => 'Reject Reason',
				'created_at' => date("Y-m-d H:i:s"),
				'updated_at' => date("Y-m-d H:i:s"),
				'created_by' => Auth::user()->id,
				'updated_by' => Auth::user()->id,
			]);

			$gi = GoodsIssue::whereId($id)->first(['id', 'code', 'created_by']);

			$content = "<p>Your inventory with Inventory Number ".$gi->code." has been rejected by ".Auth::user()->name."</p>
				<p>Your inventory has been rejected</p>
				<p>If you have more problem, you can contact us through available contact</p>";
			sendNotifEmailByUserIdInventory($gi->created_by, "Your inventory with Inventory Number ".$gi->code." has been rejected by ".Auth::user()->name, $content,'goods_issue',$id);

			DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Success reject',
                'data' => []
            ]);
        } catch(\Throwable $th) {
			DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
	}

	public function assignment_tier($id,$request_management,$agent_id) {
		//SINKRONKAN FUNGSI INI  DGN FUNGSI assignment_tier lainnya baik customer maupun helpdesk portal (search fungsi ... tion assignment_tier )

		//ASSIGNMENT
		$ticket = DB::table('ticket')->where('id',$id)->first();

		$next_agent = $agent_id ?? null;

		//PROBLEM REQUEST DI GRP ASSIGNMENT AGENTNYA DISAMAKAN DENGAN
		//SERVICE/INCIDENT (SESUAI REQ MANAGEMENT) DIMANA DI VERSI SEBELUMNYA ASSIGNMENT AGENTNYA KE REQUESTER

		// if($ticket->finalclass == "problem_request") {
		// 	$team_id = null;
		// 	$agent_id = $ticket->requester;
		// 	$tier = 1;
		// }
		// else {
			//assignment_system menghandle roundrobin, loadbalace, random
			//dan handling cuti,
			//hasil returnya bisa 1. dapet agent, 2. pending on leave, atau 3. tidak dapat agent

			$ret_val = assignment_system($ticket,$request_management,$next_agent,"approval");
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
				//saat diapprove tiket tetap berjalan, dimana belum ada agent yang diassign
				//maka kemudian admin yang harus assign manual ke agent tertentu

				DB::table('ticket_log')->insertGetId(
					[
						'message' => 'There is no agent that is available for this ticket right now because the agent is On Leave. Contact Administrator to make this ticket is assigned to available Agent.',
						'ticket_id' => $id,
						'created_at' => date("Y-m-d H:i:s"),
						'created_by' => -1,
					]
				);

				echo json_encode(["success" => true, "warning" =>true, 'message' => 'There is no agent that is available for this ticket right now because the agent is On Leave. <b>Please contact Administrator to make this ticket is assigned to available Agent. </b>']);
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


				// DB::table('ticket_assignment_log')->insertGetId(
				// 	[
				// 		'ticket_id' => $id,
				// 		'team_id' => -1,
				// 		'agent_id'=>-1,
				// 		'created_at' => date("Y-m-d H:i:s"),
				// 		'updated_at' => date("Y-m-d H:i:s"),
				// 		'created_by' => -1,
				// 		'updated_by' => -1,
				// 		'status'=>'Pending On-Leave',
				// 		'tier'=>$tier,
				// 	]
				// );

				// DB::table('ticket_log')->insertGetId(
				// 	[
				// 		'message' => 'Ticket status is Pending On-Leave </a>',
				// 		'ticket_id' => $id,
				// 		'created_at' => date("Y-m-d H:i:s"),
				// 		'created_by' => -1,
				// 	]
				// );

				// $content_notif = "<p>Ticket Activity: Status with Ticket Number ".ticketNumber($id)." is Pending On-Leave</p>
				// 				<p>If you have more problem, you can contact us through available contact</p>";
				// $title_notif = "Ticket Activity: Status with Ticket Number ".ticketNumber($id)." is Pending on Leave";
				// notif_to_all_needed_contact($id,$ticket,$title_notif,$content_notif,"ticket_monitoring");

				// echo json_encode(["success" => true, 'message' => "Now the status ticket is Pending On-Leave, because the agent/s is on Leave right now. You can assign other agent manually via Helpdesk Portal or wait automatic assignment to agent when agent is back to work. ", ]);
				// die;


			} else {
				//no agent can handle
				echo json_encode(["success" => false, 'message' => 'Ticket has been approved by Approver, but there is no agent that is available for this ticket right now. <b>Please contact Administrator to make this ticket is assigned to available Agent. </b>']);
				die;
			}
		// }


		//$team_id = 0;
		//if(empty($agent_id)) {
			//if($ticket->finalclass == "problem_request") {
				//$team_id = 0;
				//$agent_id = $ticket->requester;
			//} else {
				//$assign_list = explode(",",$request_management->assignment_tier);
				//$assign_type_list = explode(",",$request_management->assignment_type);
				//$team_id = $assign_list[0];
				//$agent_id = null;
				//if($assign_type_list[0] == 1) {
					//$agent_id = loadBalance($team_id);
				//}
				//else if($assign_type_list[0] == 2) {
					//$agent_id = roundRobin($team_id);
				//}
				//else if($assign_type_list[0] == 3) {
					//$agent_id = random($team_id);
				//}
				//else if($assign_type_list[0] == 4) {
					//$agent_id = $team_id;//kalau manual maka isi team_id sebetulnya employee id yg terpilih
					//$team_id = 0;//kosongkan

					//$is_active = filterActiveEmployee($agent_id);
					//if(!$is_active) {
						//$agent_id = null;
					//}
				//}
			//}
		//}
		//if(empty($agent_id)) {
            //echo json_encode(["success" => false, 'message' => 'Ticket has been approved by Approver, but there is no agent that is available for this ticket right now. <b>Please contact Administrator to make this ticket is assigned to available Agent. </b>']);
            //die;
		//}

		$assign_time = getAssignTime($agent_id);

		DB::table('ticket')
			->where('id', $id)
			->update(
				['team_id' => $team_id,
                 'agent_id' => $agent_id,
                 'assign_time' => $assign_time,
                 'current_tier' => $tier,
                 'ticket_open_time'=>$assign_time,//perhitungan awal agent terhadap first response dan time resolved
              ]);

		//awal perhitungan due date diubah jadi saat Start Case
		// $due_date = checkDueDate($id,date("Y-m-d H:i:s"));

		// DB::table('ticket')
		// 	->where('id', $id)
		// 	->update(
		// 		[
        //          'due_date'=>$due_date,
        //       ]);

		$escalation_time_list = explode(",",$request_management->escalation_time);
		$escalation_unit_list = explode(",",$request_management->escalation_unit);


        $next_tier_index = $tier - 1;// tier mulai dari 1, next_tier_index mulai dari 0

        if(!empty($escalation_unit_list[$next_tier_index])) {
			$escalation_date = checkEscalationDate($id,$assign_time,($escalation_time_list[$next_tier_index] ?? null),($escalation_unit_list[$next_tier_index] ?? null));

			DB::table('ticket')
				->where('id', $id)
				->update(
					[
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

		$contact = DB::table('contact')->where('id',$agent_id)->first();

		DB::table('ticket_log')->insertGetId(
			[
				'message' => 'Ticket is assigned to <a href="#">'.($contact->name ?? "").'</a>',
				'ticket_id' => $id,
				'created_at' => date("Y-m-d H:i:s"),
				'created_by' => Auth::user()->id,
			]
		);

		if($ticket->finalclass == "problem_request") {
			$content = "<p>Your Problem with Ticket Number ".ticketNumber($id)." has been Approved</p>
						<p>Now you can focus to solve this problem. After your problem is done, don't forget to mark this ticket as Solved.
						</p>";
			sendNotifEmail($agent_id, "Problem with Ticket Number ".ticketNumber($id)." has been Approved. Now you can focus to solve this problem.", $content,"assign_ticket",$id,get_cc($id));
			echo json_encode(["success" => true, 'message' => "This ticket has been Approved. Now the employee/s can focus to solve this problem.  "]);
		} else {
			// $content = "<p>You have assign to a new Ticket with Ticket Number ".ticketNumber($id)." </p>
			// 			<p>Please follow up this assignment ticket. After your assign ticket is done, you can mark this ticket as Solved.
			// 			</p>
			// 			<p>If you cannot Solve this ticket you can Escalate this ticket</p>";
			// sendNotifEmail($agent_id, "You Have Assign to New Ticket with Ticket Number ".ticketNumber($id)."", $content,"assign_ticket",$id);


			$agent_name = DB::table('contact')->where('id',$agent_id)->value('name');

			// $content = "<p>Your ticket with Ticket Number ".ticketNumber($id)." has been assign to ".$agent_name."</p>
			// 			<p>Now the status ticket is <b>Open</b></p>
			// 			<p>If you have more problem, you can contact us through available contact</p>";
			// sendNotifEmailByUserId($ticket->created_by, "Your ticket with Ticket Number ".ticketNumber($id)." has been assign to ".$agent_name, $content,"ticket_monitoring",$id,get_cc($id));

			$name = DB::table('contact')->where('id',$agent_id)->value('name');
			$title_notif = "".$name." Have Assign to New Ticket with Ticket Number ".ticketNumber($id)."";
			$content_notif = "<p>".$name." have assign to a new Ticket with Ticket Number ".ticketNumber($id)." </p>";
			notif_to_all_needed_contact($id,$ticket,$title_notif,$content_notif,"assign_ticket",$agent_id);

			$content_notif = "<p>".$name." have assign to a new Ticket with Ticket Number ".ticketNumber($id)." </p>
						<p>Please follow up this assignment ticket. After your assign ticket is done, you can mark this ticket as Solved.
						</p>
						<p>If you cannot Solve this ticket you can Escalate this ticket</p>";
			sendNotifEmail($agent_id, $name." Have Assign to New Ticket with Ticket Number ".ticketNumber($id)."", $content_notif,"assign_ticket",$id);

			echo json_encode(["success" => true, 'message' => "This ticket has been assign to Assignment Tier 1"]);
		}
	}

	public function ticket_reject(Request $request)
	{
		$input = $request->all();
		$id = $request['id'];
		$ticket = DB::table('ticket')->where('id', $id)->first();

		if($ticket->next_approval_id != Auth::user()->person) {
			echo json_encode(["success" => false, 'message' => "You can not reject this ticket, because you are not next approver for this ticket. Please contact Admin you have more problem."]);
			die;
		}
		try {
			DB::table('ticket')
			->where('id', $id)
			->update(
                [
                    'next_approval_id' => 0,
                    'status' => 'Rejected',
                    'reason' => $request['message'] ?? '-',
                    'approval_state' => $input['approval_state'] ?? '-',
                    'updated_by' => Auth::user()->id,
                    'updated_at' => date("Y-m-d H:i:s")
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
						'mode'	=> 'Reject Reason',
					]);

			DB::table('ticket_approval')->insertGetId(
				[
					'ticket_id' => $id,
					'approval_id' => Auth::user()->person,//$request['approval_custom'] ?? 0,
					'status' => "rejected",
					'reason' => $request['message'] ?? '-',
					'created_by' => Auth::user()->id,
					'updated_by' => Auth::user()->id,
					'created_at' => date("Y-m-d H:i:s"),
					'updated_at' => date("Y-m-d H:i:s")
				]
			);

			if($ticket->finalclass == 'problem_request') {
				//untuk problem parent ticketnya dilanjut SLAnya
				//(SLA_status Active kemudian recalculate due_date dihitung dari remaining_SLA dan remaining_SLA_unit)
				$now = date("Y-m-d H:i:s");
				$list_ticket = DB::table('lnktickettoproblem')
									->where('problem_ticket_id',$ticket->id)
									->join('ticket', 'ticket.id', '=', 'lnktickettoproblem.ticket_id')
									->get();
				foreach($list_ticket as $t) {
					$parent_ticket = DB::table('ticket')->where('id', $t->id)->first();
					if($parent_ticket) {
						//var_dump($parent_ticket);

						//$parent_due_date = checkDueDate($parent_ticket->id,$now,"remaining SLA",$parent_ticket->remaining_SLA,$parent_ticket->remaining_SLA_unit);
						$parent_due_date = checkDueDate($parent_ticket->id,$now,"SLA continue");

						DB::table('ticket')
							->where('id', $parent_ticket->id)
							->update([	'due_date' => $parent_due_date,
										'SLA_status' => 'Active',
										'continue_at' => date("Y-m-d H:i:s"),
										//'remaining_SLA' => null,//jangan dinullkan walaupun sudah aktiflagi, kaeran berguna saat ticket resolved dan reopen lagi
										//'remaining_SLA_unit' => null,
										//'paused_at' => null,
										//'due_date' => null
									]);

						DB::table('ticket_log')->insertGetId(
							[
								'message' => 'SLA for Ticket '.$parent_ticket->ref.' is continued, after approval for convert to problem is rejected',
								'ticket_id' => $id,
								'created_at' => date("Y-m-d H:i:s"),
								'created_by' => Auth::user()->id,
							]
						);

						DB::table('ticket_log')->insertGetId(
							[
								'message' => 'SLA for Ticket '.$parent_ticket->ref.' is continued, after approval for convert to problem is rejected',
								'ticket_id' => $parent_ticket->id,
								'created_at' => date("Y-m-d H:i:s"),
								'created_by' => Auth::user()->id,
							]
						);

						//flow baru dimana saat reject problem, tiket incident/service nya kembali onProgress,
						//dimana di tahap sebelumnya saat create Problem tiket incident/servicenya dijadikan Resolved statusnya (Search Kode Keyword:FLOW341)
						setOnProgress($parent_ticket->id);
					}
				}
			}

			// $content = "<p>Your ticket with Ticket Number ".ticketNumber($id)." has been rejected by ".Auth::user()->name."</p>
            // <div style='border: 2px solid #333; box-shadow: 0px 10px 15px -3px rgba(0,0,0,0.1);'>
            //     <div style='padding: 10px;'>
            //         <p style='background-color: #fff;'>
            //         ".$input['message']."
            //         </p>
            //     </div>
            // </div>
			// <p>If you have more problem, you can contact us through available contact</p>";
			// sendNotifEmail($ticket->created_by, "Your ticket with Ticket Number ".ticketNumber($id)." has been rejected by ".Auth::user()->name, $content,"ticket_monitoring",$id);

			$content = "<p>Your ticket with Ticket Number ".ticketNumber($id)." has been rejected by ".Auth::user()->name."</p>
            <div style='border: 2px solid #333; box-shadow: 0px 10px 15px -3px rgba(0,0,0,0.1);'>
                <div style='padding: 10px;'>
                    <p style='background-color: #fff;'>
                    ".$input['message']."
                    </p>
                </div>
            </div>
						<p>If you have more problem, you can contact us through available contact</p>";
			sendNotifEmailByUserId($ticket->created_by, "Your ticket with Ticket Number ".ticketNumber($id)." has been rejected by ".Auth::user()->name, $content,"ticket_monitoring",$id);

			echo json_encode(["success" => true, 'message' => "This ticket has been rejected."]);
		} catch (Exception $e) {
			echo json_encode(["success" => false, 'message' => $e->getMessage()]);
		}
	}

	public function ticket_delete(Request $request)
	{
		$input = $request->all();
		$id = $request['id'];
		$ticket = DB::table('ticket')->where('id', $id)->first();

		if($ticket->created_by != Auth::user()->id) {
			echo json_encode(["success" => false, 'message' => "You can not delete this ticket, because you are not creating this ticket. Please contact Admin you have more problem."]);
			die;
		}

		try {
			DB::table('ticket')
			->where('id', $id)
			->update(
                [
                    'status' => 'Withdrawn',
                    'reason' => $request['message'] ?? '-',
                    'approval_state' => $input['approval_state'] ?? '-',
                    'updated_by' => Auth::user()->id,
                    'updated_at' => date("Y-m-d H:i:s")
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
						'mode'	=> 'Withdrawn Reason',
					]);

			DB::table('ticket_approval')->insertGetId(
				[
					'ticket_id' => $id,
					'approval_id' => Auth::user()->person,//$request['approval_custom'] ?? 0,
					'status' => "withdrawn",
					'reason' => $request['message'] ?? '-',
					'created_by' => Auth::user()->id,
					'updated_by' => Auth::user()->id,
					'created_at' => date("Y-m-d H:i:s"),
					'updated_at' => date("Y-m-d H:i:s")
				]
			);

			DB::table('ticket_log')->insertGetId(
				[
					'message' => 'Ticket has been withdrawn by <a href="#">'.Auth::user()->name.'</a> ',
					'ticket_id' => $id,
					'created_at' => date("Y-m-d H:i:s"),
					'created_by' => Auth::user()->id,
				]
			);

			// $content = "<p>Your Staff have withdrawn this Ticket with Ticket Number ".ticketNumber($id)."</p>";
			// sendNotifEmail($ticket->next_approval_id, "Ticket Number ".ticketNumber($id)." has been withdrawn", $content,"ticket_monitoring",$id);

			$content = "<p>The ticket with Ticket Number ".ticketNumber($id)." has been withdrawn by ".Auth::user()->name."</p>
						<p>If you have more problem, you can contact us through available contact</p>";
			sendNotifEmailByUserId($ticket->next_approval_id, "The ticket with Ticket Number ".ticketNumber($id)." has been withdrawn by ".Auth::user()->name, $content,"ticket_monitoring",$id);

			echo json_encode(["success" => true, 'message' => "This ticket has been withdrawn."]);
		} catch (Exception $e) {
			echo json_encode(["success" => false, 'message' => $e->getMessage()]);
		}
	}

    public function show($token)
    {
		//echo Auth::user()->person."<-current_login_person";
        $ticket = DB::table('ticket')->where('token',$token)->first();
		if(empty($ticket)) {
			denied();
		}

        $id = $ticket->id;
        $title = "Detail Request ".$ticket->title;
        $breadcumb = [
			[
				'name' => 'Approve Request',
				'url' => 'approve-request'
			],
			[
				'name' => ucfirst($ticket->title),
				'url' => 'approve-request/'.$token
			]
		];
        $ticket_statuses = DB::table('ticket_approval')->where('ticket_id', $id)->get();


		//echo $approval_id;
		//var_dump($next_is_assignment);
        return view('approve-request.show')
            ->with('ticket', $ticket)
            ->with('title', $title)
            ->with('breadcumb', $breadcumb)
            ->with('statuses', $ticket_statuses);
            //->with('approval_id', $approval_id)
            //->with('next_is_assignment',$next_is_assignment)
            //->with('next_approval_state',$next_approval_state)
            ;
    }

	private function backup_fungsi_alur_lama() {

			if($ticket->approval_state != "appoval_support") {
				//APPROVAL USER
				//echo "APPROVAL USER";
				if(empty($request_management->approval_user_custom)) {
					//1. MAX USER SUPERORDINATE
							//Next Approval User
							$contact_login = DB::table('contact')
												->select('contact.id','contact.position','contact.job_title','job_title.parent')
												->leftJoin('job_title', 'contact.job_title', '=', 'job_title.id')
												->where('contact.id', Auth::user()->person)
												->first();
							//var_dump($contact_login);
							$approval = DB::table('contact')->where('job_title', $contact_login->parent)->first();
							//var_dump($approval);
							if(empty($contact_login->parent)) {
								//echo "sip";
								//TANDAI UNTUK DIEKSEKUSI DI STEP KODE BERIKUTNYA
								//YAITU PENGECEKAN APPROVAL SUPPORT
								$transisi_dari_approval_user_ke_support = true;

								//CEK YANG APPROVE SAMA DGN APPROVER TERAKHIR
							} else if ($request_management->max_user_superordinate == $contact_login->position) {

								//echo "okeeee";
								// assignment tier
								//TANDAI UNTUK DIEKSEKUSI DI STEP KODE BERIKUTNYA
								//YAITU PENGECEKAN APPROVAL SUPPORT
								$transisi_dari_approval_user_ke_support = true;

							} else {
								//var_dump($request_management->max_user_superordinate);
								//var_dump($contact_login->position);
								//echo "LANJUT";
								//LANJUT APPROVER SELANJUTNYA
								$next_approval_id = $approval->id;
							}
				}
				else {
					//echo "APPROVAL USER CUSTOM";
					$list_approval_user_custom = explode(",",$request_management->approval_user_custom);
					$found_approve = false;
					for($i=0;$i<count($list_approval_user_custom);$i++) {
						$approve_user_contact_id = DB::table('contact')->where('contact.job_title',$list_approval_user_custom[$i])->value('id');
						//echo $approve_support_agent_id."<-approve_support_agent_id";

						if($approve_user_contact_id) {

						} else {
							//karena contact tidak ada, maka cek ke atasannya
							$need_check_jobtitle = $list_approval_user_custom[$i];
							$atasan = getMinimalSatuLevelAtasan($need_check_jobtitle);
							$approve_user_contact_id = $atasan['contact_id'];
						}

						if(!empty($approve_user_contact_id)) { //kalau kosong diskip ke loop berikutnya
							$ticket_approval = DB::table('ticket_approval')
												->where('ticket_id',$id)
												->where('approval_id',$approve_user_contact_id)
												->first();


							if(empty($ticket_approval)) {
								//cek kalau sama dgn yg login, maka di skip, karena yang dicari next approve
								if($approve_user_contact_id != Auth::user()->person) {
									//next belum tercatat berarti yang ini
									if(!$dapet) { //tapi cek dulu bahwa belum dapet yang berikutnya, jika sudah dapat maka ga usah
										//echo "KENAA";
										//die;
										$found_approve = true;
										$next_approval_id = $approve_user_contact_id;
										//$next_approval_state = "appoval_support";
									}
								}
							} else {
								//cek apakah statusnya sudah diapprove atau belum
								if($ticket_approval->status == 'new') {
									//ini seharusnya yang sedang login saat ini, jadi bukan (skip)
								} else if($ticket_approval->status == 'approved') {
									//berarti sudah diapprove (skip)
								} else {

								}
							}
						}
					}
					//echo "WOY";
					if(!$found_approve) {
						//echo "tidak dapet";
						//kalau tidak dapet maka selanjutnya ke approval support
						$transisi_dari_approval_user_ke_support = true;
					}


					////1. APPROVAL USER CUSTOM
					////cek atasan dari user yang merequest
					//$list_approval_user_custom = explode(",",$request_management->approval_user_custom);
					//$semua_atasan = $this->cekSemuaAtasan($ticket->requester);
					//$found_approve = false;
					//foreach($list_approval_user_custom as $auc_position_id) {
						////var_dump($auc_position_id);
						//foreach($semua_atasan as $atasan) {
							//if(!$found_approve) {
								////var_dump($atasan);
								//if($atasan['position_id'] == $auc_position_id) {
									////cek kalau sama dgn yg login, maka di skip, karena yang dicari next approve
									//if($atasan['contact_id'] != Auth::user()->person) {
										////dapat
										////echo "dapat";
										////var_dump($atasan);
										////KETEMU POSISI
										////CEK APAKAH SUDAH APPROVE
										//$ticket_approval = DB::table('ticket_approval')
															//->where('ticket_id',$id)
															//->where('approval_id',$atasan['contact_id'])
															//->first();
										//if(!$ticket_approval) {
											////belum diapprove yang bersangkutan
											////var_dump($atasan);
											//$found_approve = true;
											//$next_approval_id = $atasan['contact_id'];
										//}
									//}
								//}
							//}
									////$atasan[] = [
										////'job_title_id'=>$job_title->id,
										////'position_id'=>$position->id,
										////'contact_id'=>$contact->id,
									////];

						//}
					//}
					//if(!$found_approve) {
						////echo "tidakdpt";
						////jika tidak ditemukan maka dianggap ini yang terakhir
						////next approval support
						////TANDAI UNTUK DIEKSEKUSI DI STEP KODE BERIKUTNYA
						////YAITU PENGECEKAN APPROVAL SUPPORT
						//$transisi_dari_approval_user_ke_support = true;
					//}

				}


			}
			else {
				//3. APPROVAL SUPPORT
				//echo "APPSUPROT";
				$next_approval_state = "appoval_support";
								if(!empty($request_management->approval_support_custom)) {
									//echo "approval_support_custom";
									$list_approval_support_custom = explode(",",$request_management->approval_support_custom);

									for($i=0;$i<count($list_approval_support_custom);$i++) {
										$approve_support_agent_id = DB::table('contact')->where('contact.job_title',$list_approval_support_custom[$i])->value('id');
										//echo $approve_support_agent_id."<-approve_support_agent_id";
										if($approve_support_agent_id) {

										} else {
											//karena contact tidak ada, maka cek ke atasannya
											$need_check_jobtitle = $list_approval_support_custom[$i];
											$atasan = getMinimalSatuLevelAtasan($need_check_jobtitle);
											$approve_support_agent_id = $atasan['contact_id'];
										}
										if(!empty($approve_support_agent_id)) { //kalau kosong diskip ke loop berikutnya
											$ticket_approval = DB::table('ticket_approval')
																->where('ticket_id',$id)
																->where('approval_id',$approve_support_agent_id)
																->first();


											if(empty($ticket_approval)) {
												//cek kalau sama dgn yg login, maka di skip, karena yang dicari next approve
												if($approve_support_agent_id != Auth::user()->person) {
													//next belum tercatat berarti yang ini
													if(!$dapet) { //tapi cek dulu bahwa belum dapet yang berikutnya, jika sudah dapat maka ga usah
														//echo "KENAA";
														//die;
														$dapet = true;
														$next_approval_id = $approve_support_agent_id;
														$next_approval_state = "appoval_support";
													}
												}
											} else {
												//cek apakah statusnya sudah diapprove atau belum
												if($ticket_approval->status == 'new') {
													//ini seharusnya yang sedang login saat ini, jadi bukan (skip)
												} else if($ticket_approval->status == 'approved') {
													//berarti sudah diapprove (skip)
												} else {

												}
											}
										}
									}
									//echo "WOY";
									if(!$dapet) {
										//echo "DUAAAKE";
										//kalau tidak dapet maka dianggap sudah diapprove oleh support semua
										//atau page ini support yang terakhir
										//ketika diapprove maka assign ke team
										$next_is_assignment = 1;
									}
								}
								else {
									//echo "MAX SUPPORT APPROVAL";


									//3. MAX SUPPORT APPROVAL
									//ambil sampel salah satu agent dari assignment tier 1
									$assign_list = explode(",",$request_management->assignment_tier);

									$assign_type_list = explode(",",$request_management->assignment_type);

									$team_id = $assign_list[0] ?? 0;
									//echo "oke";
									//var_dump($assign_type_list[0]);
									if(!empty($assign_type_list[0]) && $assign_type_list[0] == "4") {
										//echo "masuk";
										//Manual

										$team_id = 0;
										$agent_id_req_manaj = $assign_list[0];
										$job_title_id = DB::table('contact')->where('id',$agent_id_req_manaj)->value('job_title');
										$employee = DB::table('contact')->where('id',$agent_id_req_manaj)->first();
										//var_dump($employee);
									} else {
										//loadbalance roundrobin random
										//$list_employee_team = DB::table('lnkemployeetoteam')->where('team_id',$team_id)->get();
										//$job_title = null;
										////var_dump($team_id);
										////var_dump($list_employee_team);
										//foreach($list_employee_team as $et) {
											//$employee = DB::table('contact')->where('id',$et->employee_id)->first();
											////var_dump($employee);
											//if(!empty($employee->job_title)) {
												//$job_title_id = $employee->job_title;
											//}
										//}

										$ret_val = getEmployeeJabatanTerbawah($team_id);
										$job_title_id = $ret_val[0];
										$employee = $ret_val[1];

									}



									$contact_yang_login = DB::table('contact')->where('id',Auth::user()->person)->first();

									//echo "stepA";
									if(empty($job_title_id)) {
										//echo "stepB";
										//tidak bisa diproses, employee tidak tercatat job titlenya
										//langsung ke assignment
										$next_is_assignment = 1;
									}
									else if(empty($request_management->max_support_approval)) {
										//echo "stepC";
										//max support approval tidak diset maka
										//langsung ke assignment
										$next_is_assignment = 1;
									}
									else if($contact_yang_login->job_title == $request_management->max_support_approval) {
										//yang login sudah maks
										//langsung ke assignment
										$next_is_assignment = 1;
									}
									else {
										//echo "stepD";
										$list_atasan = $this->cekSemuaAtasan($employee->id);
										foreach($list_atasan as $atasan) {
											//var_dump($atasan);
											//echo "SSS";
											$ticket_approval = DB::table('ticket_approval')
																->where('ticket_id',$id)
																->where('approval_id',$atasan['contact_id'])
																->first();
											//echo "YA";
											if($ticket_approval 								  //sudah approve atau
												|| ($atasan['contact_id'] == Auth::user()->person)  //yang approv yang lagi login
											) {
												//echo "tidak";
												//lanjut cek atasan selanjutnya
											} else {
												//dapat atasan yang dimaksud
												//echo "dapet";
												//var_dump($atasan['name']);
												$dapet = true;
												$next_approval_id = $atasan['contact_id'];
												$next_approval_state = "appoval_support";
												break;
											}
					//$atasan[] = [
						//'job_title_id'=>$job_title->id,
						//'position_id'=>$contact->position,
						//'contact_id'=>$contact->id,
						//'name'=>$contact->name,
					//];
										}
										if(!$dapet) {
											//echo "DUAAAKE";
											//kalau tidak dapet maka dianggap sudah diapprove oleh support semua
											//atau page ini support yang terakhir
											//ketika diapprove maka assign ke team
											$next_is_assignment = 1;
										}

										////setelah dapat job title cek apakah sudah max approval atau belum kalau sudah
										////lanjut assignment
										//$contact_yang_login = DB::table('contact')->where('id',Auth::user()->person)->first();

										//if($contact_yang_login->job_title == $request_management->max_support_approval) {
											//echo "stepE";
											////yang login sudah maks
											//$next_is_assignment = 1;
										//}
										//else {
											//echo "stepF";
											////belum sampe maks approval
											////maka cari ke atasnya
												////setelah dapat job_title dapatkan parent jobtitlenya
												//$job_title = DB::table('job_title')->where('id',$job_title_id)->first();
												////var_dump($job_title);
												//if(empty($job_title->parent) ||
													//$job_title->parent == 0) {

													//echo "stepG";
													////parent tidak ditemukan, tidak bisa dilanjut, maka langsung ke assignment saja
													//$next_is_assignment = 1;
												//} else {
													//echo "stepH";
															////parent ditemukan, lanjutkan proses
															//$job_title_parent_id = $job_title->parent;
															//$belum_ketemu = true;

															//while($belum_ketemu) {//selama belum ketemu, cari ke atasnya terus
																//echo "stepI";
																//$contact_atasan = DB::table('contact')->where('job_title',$job_title_parent_id)->first();
																//if(empty($contact_atasan)) {
																	//echo "stepJ";
																	////kontak atasan ga ketemu
																	////lanjut ke loop berikutnya
																	//$job_title_parent_id = $job_title_parent->parent;
																	//$belum_ketemu = true;
																//} else {
																	//echo "stepK";
																	////contact ditemukan
																	////cek sudah approva apa belum
																	//$ticket_approval = DB::table('ticket_approval')
																						//->where('ticket_id',$id)
																						//->where('approval_id',$atasan_employee->id)
																						//->first();
																	//if($ticket_approval //sudah approve atau
																		//|| ($contact_atasan->id == Auth::user()->person)  //yang approv yang lagi login
																	//) {
																		//echo "stepL";
																		////atasan tsb sudah approve, diskip, lanjut ke atasnya
																		//$job_title_parent = DB::table('job_title')->where('id',$job_title_parent_id)->first();
																		//if(empty($job_title_parent->parent) ||
																			//$job_title_parent->parent == 0) {
																			////parent job title udah ga ada, bisa jadi udah paling atas atau terputus hirarkinya
																			////maka dianggap tidak perlu approval dari sisi support lagi
																			//$next_is_assignment = 1;
																			//break;

																		//} else {
																			//echo "stepM";
																			////cari lagi ke atasnya sampai dapat
																			//$job_title_parent_id = $job_title_parent->parent;
																			//$belum_ketemu = true;
																		//}
																	//} else {
																		////belum approve cek apakah contact tsb yang lagi login

																			//$belum_ketemu = true;


																	//}
																//}

															//}
												//}
										//}
									}

								}


			}



			if($transisi_dari_approval_user_ke_support) {
				//echo "TRANSISI";
				//kode ini hanya dieksekusi sekali, yaitu saat transisi dari approval user ke approval support

					$list_assignment_tier = explode(",",$request_management->assignment_tier);
					$next_approval_id = 0;

					//lanjut cek ke Next Approval dari Support
					//echo "cek";

					if(!empty($request_management->approval_support_custom)) {
						//2. APPROVAL SUPPORT CUSTOM
						//echo "APPROVAL SUPPORT CUSTOM";
						$list_approval_support_custom = explode(",",$request_management->approval_support_custom);

						for($i=0;$i<count($list_approval_support_custom);$i++) {
							$approve_support_agent_id = DB::table('contact')->where('contact.job_title',$list_approval_support_custom[$i])->value('id');
							//echo $approve_support_agent_id."<-approve_support_agent_id";
							if(!empty($approve_support_agent_id)) { //kalau kosong diskip ke loop berikutnya
								$ticket_approval = DB::table('ticket_approval')
													->where('ticket_id',$id)
													->where('approval_id',$approve_support_agent_id)
													->first();


								if(empty($ticket_approval)) {
									//cek kalau sama dgn yg login, maka di skip, karena yang dicari next approve
									if($approve_support_agent_id != Auth::user()->person) {
										//next belum tercatat berarti yang ini
										if(!$dapet) { //tapi cek dulu bahwa belum dapet yang berikutnya, jika sudah dapat maka ga usah
											//echo "KENAA";
											$dapet = true;
											$next_approval_id = $approve_support_agent_id;
											$next_approval_state = "appoval_support";
										}
									}
								} else {
									//cek apakah statusnya sudah diapprove atau belum
									if($ticket_approval->status == 'new') {
										//ini seharusnya yang sedang login saat ini, jadi bukan (skip)
									} else if($ticket_approval->status == 'approved') {
										//berarti sudah diapprove (skip)
									} else {

									}
								}
							}
						}
						//echo "WOY";
						if(!$dapet) {
							//echo "DUAAAKE";
							//kalau tidak dapet maka dianggap sudah diapprove oleh support semua
							//atau page ini support yang terakhir
							//ketika diapprove maka assign ke team
							$next_is_assignment = 1;
						}
						//var_dump($list_approval_support_custom);
						//echo $list_approval_support_custom[0];

					}
					else if(!empty($request_management->max_support_approval)) {
						//echo "MAX SUPPORT APPROVAL";
						//2. MAX SUPPORT APPROVAL
						//ambil sampel salah satu agent dari assignment tier 1
						$assign_list = explode(",",$request_management->assignment_tier);
						$assign_type_list = explode(",",$request_management->assignment_type);


						$team_id = $assign_list[0] ?? 0;
						//echo "oke";
						//var_dump($assign_type_list[0]);
						if(!empty($assign_type_list[0]) && $assign_type_list[0] == "4") {
							//echo "masuk";
							//Manual

							$team_id = 0;
							$agent_id_req_manaj = $assign_list[0];
							$job_title_id = DB::table('contact')->where('id',$agent_id_req_manaj)->value('job_title');
							//var_dump($job_title_id);
						} else {
							//loadbalance roundrobin random
							//$list_employee_team = DB::table('lnkemployeetoteam')->where('team_id',$team_id)->get();
							//$job_title = null;
							////var_dump($team_id);
							////var_dump($list_employee_team);
							//$list_obj_employee = [];
							//foreach($list_employee_team as $et) {
								//$employee = DB::table('contact')->where('id',$et->employee_id)->first();
								////var_dump($employee);
								//if(!empty($employee->job_title)) {

									////kumpulkan data employee, atasan, dan jobtitlenya, utk nanti cross cek hirarki yg terbawah
									//$list_job_title_atasan = [];
									//do {
										//$atasan_found = false;
										//$cek_job_title = DB::table('job_title')->where('id',$employee->job_title)->first();
										//if(!empty($cek_job_title->parent)) {
											//$atasan_found = true;
											//$list_job_title_atasan[] = $cek_job_title->parent;
										//}
									//} while($atasan_found)

									//$list_obj_employee[$employee->id] = ['obj_employee'=>$employee,
																		//'job_title_id'=>$employee->job_title,
																		//'list_job_title_atasan'=>$list_job_title_atasan,
																		//];

									//$job_title_id = $employee->job_title;
								//}
							//}

							//$list_obj_employee2 = $list_obj_employee;
							//$list_obj_employee3 = $list_obj_employee; //copy yg ketiga nanti jadi hasilnya
							////setelah employee terkumpul cek semua atasannya
							//foreach($list_obj_employee as $id_employee => $obj_employee) {
								//foreach($list_obj_employee2 as $id_employee2 => $obj_employee2) {
									//if(in_array($obj_employee2['job_title_id'],$obj_employee['list_job_title_atasan'])) {
										////hapus employee ini karena employee atasan dari yang lain
										//unset($list_obj_employee3[$id_employee2]);
									//}
								//}
							//}
							////semua unsur atasan sudah dihapus
							////sisanya yang kemungkinan bawahan ambil salah satu saja
							//if(!empty($list_obj_employee3)) {
								//$employee = reset($obj_employee3);//ambil element pertama
								//if(!empty($employee->job_title)) {
									//$job_title_id = $employee->job_title;
								//}
							//}
							$ret_val = getEmployeeJabatanTerbawah($team_id);
							$job_title_id = $ret_val[0];
							$employee = $ret_val[1];
						}

						if(!empty($job_title_id)) {
							$job_title = DB::table('job_title')->where('id',$job_title_id)->first();
						}


						//echo "step223";
						if(empty($job_title_id)) {
							//echo "step3";
							//tidak bisa diproses, team employee tidak tercatat job titlenya
							//lanjut ke assignment
							$next_is_assignment = 1;
						} else if(empty($job_title)) {
							//echo "step5";
							//tidak bisa diproses, object job_title gak dapet
							//lanjut ke assignment
							$next_is_assignment = 1;
						} else if(empty($job_title->parent) || $job_title->parent == 0) {
							//echo "step51";
							//tidak bisa diproses, parent job_title ga ada
							//lanjut ke assignment
							$next_is_assignment = 1;
						} else {
							//echo "step52";

							$job_title_parent_id = $job_title->parent;
							$atasan_employee = DB::table('contact')->where('job_title',$job_title_parent_id)->first();

							//cek sudah diapprove
							$ticket_approval = DB::table('ticket_approval')
												->where('ticket_id',$id)
												->where('approval_id',$atasan_employee->id ?? -100)
												->first();
							if($ticket_approval ) {
								//sudah diapprove bersangkutan
								$atasan_employee = null;
							}
							//cek apakah atasan tsb yang lagi login
							if(!empty($atasan_employee->id) && Auth::user()->person == $atasan_employee->id) {
								$atasan_employee = null;
							}

							//var_dump($atasan_employee);

							while(empty($atasan_employee)) {
								//echo "kosong";
								$job_title_parent = DB::table('job_title')->where('id',$job_title_parent_id)->first();
								if($job_title_parent->parent) {
									//cari lagi ke atasnya sampai dapat
									//echo "cariAtasLagi";
									$job_title_parent_id = $job_title_parent->parent;

								} else {
									//parent job title udah ga ada, bisa jadi udah paling atas atau terputus hirarkinya
									//maka dianggap tidak perlu approval dari sisi support lagi
									$next_is_assignment = 1;
									//echo "stepNextAssign";
									break;
								}
								$atasan_employee = DB::table('contact')->where('job_title',$job_title_parent_id)->first();

								//cek sudah diapprove
								$ticket_approval = DB::table('ticket_approval')
													->where('ticket_id',$id)
													->where('approval_id',$atasan_employee->id ?? -100)
													->first();
								if($ticket_approval) {
									//sudah diapprove bersangkutan
									$atasan_employee = null;
								}
								//cek apakah atasan tsb yang lagi login
								if(!empty($atasan_employee->id) && Auth::user()->person == $atasan_employee->id) {
									$atasan_employee = null;
								}
							}

							if(!$next_is_assignment) {
								//echo "step7";
								//atasan ketemu
								$next_approval_id = $atasan_employee->id;
								$next_approval_state = "appoval_support";
							}
						}
					} else {
						//echo "gak kena atas";
						//ini jika approval_support_custom
						$next_is_assignment = 1;
					}
					//$contact_login = DB::table('contact')
										//->select('contact.id','contact.position','contact.job_title','job_title.parent')
										//->leftJoin('job_title', 'contact.job_title', '=', 'job_title.id')
										//->where('contact.id', Auth::user()->person)
										//->first();


			}
			//die;


	}

}
