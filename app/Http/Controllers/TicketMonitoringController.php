<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TicketMonitoringController extends Controller {

    public function index(Request $request)
    {
        $title = "Ticket Monitoring";
        $breadcumb = [
			[
				'name' => 'Ticket Monitoring',
				'url' => 'ticket-monitoring'
			]
		];
        $tickets = DB::table('ticket')->orderBy('id','desc')->whereRaw(' (created_by_contact = ? OR requester = ?) ',[Auth::user()->person,Auth::user()->person]);

		if ($request->query('status_ticket')) {
				if($request->query('status_ticket') == 'all') {
					$tickets = $tickets->whereIn('status', ['Open', 'On Progress', 'Resolved', 'Closed']);
				} else {
					$tickets = $tickets->where('status', $request->query('status_ticket'));
				}

		} else if ($request->query('state')) {
			if ($request->query('state') == 'overdue') {
				$tickets = $tickets
					->whereIn('ticket.status',['Open','On Progress'])
					->whereRaw('due_date < NOW()');
			} else if ($request->query('state') == 'due_today') {
				$tickets = $tickets
					->whereIn('ticket.status',['Open','On Progress'])
					->whereRaw(' ((due_date >= NOW()) and  (CURRENT_DATE = DATE(due_date))) ');
			}
		}
		if ($request->query('ticket_type') == 'incident_management'
			|| $request->query('ticket_type') == 'service_request'
			|| $request->query('ticket_type') == 'problem_request') {
			$tickets = $tickets->where('ticket.finalclass', $request->query('ticket_type'));
		}

		$tickets = $tickets->get();

        return view('ticket-monitoring.index')
            ->with('tickets', $tickets)
            ->with('title', $title)
            ->with('breadcumb', $breadcumb);
    }

    public function myDraft()
    {
        $title = "Draft Request";
        $breadcumb = ['Draft'];
        $tickets = DB::table('ticket_draft')->where('status','Draft')->orderBy('id','desc')->where('created_by', Auth::user()->id)->get();

        return view('ticket-monitoring.draft')
            ->with('tickets', $tickets)
            ->with('title', $title)
            ->with('breadcumb', $breadcumb);
    }

    public function myServices()
    {
        $title = "Service Ticket";
        $breadcumb = ['Service Ticket'];
        $tickets = DB::table('ticket')->orderBy('id','desc')->where('finalclass','service_request')->where('created_by', Auth::user()->id)->get();

        return view('ticket-monitoring.index')
            ->with('tickets', $tickets)
            ->with('title', $title)
            ->with('breadcumb', $breadcumb);
    }

    public function myIncidents()
    {
        $title = "Incident Ticket";
        $breadcumb = ['Incident Ticket'];
        $tickets = DB::table('ticket')->orderBy('id','desc')->where('finalclass','incident_management')->where('created_by', Auth::user()->id)->get();


        return view('ticket-monitoring.index')
            ->with('tickets', $tickets)
            ->with('title', $title)
            ->with('breadcumb', $breadcumb);
    }

    public function myAssignments()
    {
        $title = "My Assignments";
        $breadcumb = ['My Assignments'];
        $tickets = DB::table('ticket')->orderBy('id','desc')->where('agent_id', Auth::user()->person)->get();

        return view('ticket-monitoring.index')
            ->with('tickets', $tickets)
            ->with('title', $title)
            ->with('breadcumb', $breadcumb);
    }

    public function show($token)
    {
		//dd(get_cc($id));
        $ticket = DB::table('ticket')->where('token',$token)->first();

		if(empty($ticket)) {
			denied();
		}

        $id = $ticket->id;
        $title = "Detail Request ".$ticket->title;
        $breadcumb = [
			[
				'name' => 'Ticket Monitoring',
				'url' => 'ticket-monitoring'
			],
			[
				'name' => ucfirst($ticket->title),
				'url' => 'ticket-monitoring/'.$token
			]
		];
        $ticket_statuses = DB::table('ticket_approval')->where('ticket_id', $id)->get();

        return view('ticket-monitoring.show')
            ->with('ticket', $ticket)
            ->with('title', $title)
            ->with('statuses', $ticket_statuses)
            ->with('breadcumb', $breadcumb);
    }

    public function replyComment(Request $request)
    {
		
		DB::beginTransaction();
		try {
			$request->validate([
				'file' => 'file|mimes:jpg,jpeg,png,gif,doc,docx,pdf,xls,xlsx,txt,pptx', // Max size in kilobytes (2 MB)
			]);
			
			$input = $request->all();
				
			$ticket = DB::table('ticket')->where('id',$input['id'])->first();
			if(empty($ticket)) {
				echo json_encode(["success" => false, 'message' => "Ticket not found", ]);
				die;
			}

			$data = [	
				'message' => $input['message'],
				'ticket_id'=> $input['id'],
				'user_id'=> Auth::user()->id,
				'contact_id'=> Auth::user()->person,
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

			if(!empty($input['askMoreInfo'])) {
				$data['mode'] = "Ask More Information";
				DB::table('ticket')->where('id',$input['id'])->update(['status'=>'Waiting for User','asker_more_info'=>Auth::user()->person]);
				DB::table('comment')
					->insert($data);

				DB::table('ticket_log')->insertGetId(
					[
						'message' => '<a href="#">'.Auth::user()->name.'</a> is ask more information from the user. Status ticket change to <b>Waiting for User</b>',
						'ticket_id' => $ticket->id,
						'created_at' => date("Y-m-d H:i:s"),
						'created_by' => Auth::user()->id,
					]
				);

				$id = $ticket->id;
				$content = "<p>Approver is ask to you more Information for ticket with Ticket Number ".ticketNumber($id)." </p>
				<div style='border: 2px solid #333; box-shadow: 0px 10px 15px -3px rgba(0,0,0,0.1);'>
					<div style='padding: 10px;'>
						<p style='background-color: #fff;'>
						".$input['message']."
						</p>
					</div>
				</div>
							<p>Please respond with information that is needed</p>
							<p>Now the status ticket is <b>Waiting for User</b></p>
							<p>If you have more problem, you can contact us through available contact</p>";
				sendNotifEmailByUserId($ticket->created_by, "Approver is ask to you more Information for ticket with Ticket Number ".ticketNumber($id), $content,"ticket_monitoring",$id);

			} else {
				DB::table('comment')
					->insert($data);
				DB::table('ticket_log')->insertGetId(
					[
						'message' => '<a href="#">'.Auth::user()->name.'</a> is reply a new comment',
						'ticket_id' => $ticket->id,
						'created_at' => date("Y-m-d H:i:s"),
						'created_by' => Auth::user()->id,
					]
				);

				$id = $ticket->id;
				// if(!empty($ticket->agent_id) && $ticket->agent_id > 0) {
				// 	//kalau sudah ada agent maka kirim notif ke agent
				// 	$content = "<p>".Auth::user()->name." has reply a new comment to your ticket with Ticket Number ".ticketNumber($id)."</p>
				//     <div style='border: 2px solid #333; box-shadow: 0px 10px 15px -3px rgba(0,0,0,0.1);'>
				//         <div style='padding: 10px;'>
				//             <p style='background-color: #fff;'>
				//             ".$input['message']."
				//             </p>
				//         </div>
				//     </div>
				// 				<p>If you have more problem, you can contact us through available contact</p>";
				// 	sendNotifEmail($ticket->agent_id, Auth::user()->name." has reply a new comment to your ticket with Ticket Number ".ticketNumber($id), $content,"ticket_comment",$id);


				// 	//$content = "<p>".Auth::user()->name." has reply a new comment to ticket with Ticket Number ".ticketNumber($id)."</p>
				// 				//<p>If you have more problem, you can contact us through available contact</p>";
				// 	//sendNotifEmail($ticket->agent_id, Auth::user()->name." has reply a new comment to ticket with Ticket Number ".ticketNumber($id), $content,"ticket_comment",$id);
				// }

				// $recipients = $input['notif'];
				// $content = "<p>".Auth::user()->name." has reply a new comment to ticket with Ticket Number ".ticketNumber($id)."</p>
				// <div style='border: 2px solid #333; box-shadow: 0px 10px 15px -3px rgba(0,0,0,0.1);'>
				//     <div style='padding: 10px;'>
				//         <p style='background-color: #fff;'>
				//         ".$input['message']."
				//         </p>
				//     </div>
				// </div>
				// 			<p>If you have more problem, you can contact us through available contact</p>";
				// notif_to_all_needed_contact2($id,$ticket,Auth::user()->name." has reply a new comment to ticket with Ticket Number ".ticketNumber($id),$content,"ticket_comment",$recipients);
				$content = "<p>".Auth::user()->name." has reply a new comment to your ticket with Ticket Number ".ticketNumber($id)."</p>
					<div style='border: 2px solid #333; box-shadow: 0px 10px 15px -3px rgba(0,0,0,0.1);'>
						<div style='padding: 10px;'>
							<p style='background-color: #fff;'>
							".$input['message']."
							</p>
						</div>
					</div>
					<p>If you have more problem, you can contact us through available contact</p>";

				foreach ($input['notif'] as $contact_id) {
					sendNotifEmail($contact_id, Auth::user()->name." has reply a new comment to ticket with Ticket Number ".ticketNumber($id), $content,"ticket_comment",$id);
				}

				// sendBulkNotifEmail($input['notif'], Auth::user()->name." has reply a new comment to ticket with Ticket Number ".ticketNumber($id), $content,"ticket_comment",$id);
			}

			if($ticket->status == 'Waiting for User' && $ticket->created_by == Auth::user()->id) {
				DB::table('ticket')->where('id',$input['id'])->update(['status'=>'Submit for Approval']);
				DB::table('ticket_log')->insertGetId(
					[
						'message' => 'Ticket status is changed to <b>Submit for Approval</b>',
						'ticket_id' => $ticket->id,
						'created_at' => date("Y-m-d H:i:s"),
						'created_by' => Auth::user()->id,
					]
				);
				$id = $ticket->id;
				$content = "<p>You have respond to the ask more information for Ticket Number ".ticketNumber($id)." </p>
				<div style='border: 2px solid #333; box-shadow: 0px 10px 15px -3px rgba(0,0,0,0.1);'>
					<div style='padding: 10px;'>
						<p style='background-color: #fff;'>
						".$input['message']."
						</p>
					</div>
				</div>
							<p>Now the status ticket is <b>Submit for Approval</b></p>
							<p>If you have more problem, you can contact us through available contact</p>";
				sendNotifEmailByUserId($ticket->created_by, " You have Respond to the ask more information  for Ticket Number ".ticketNumber($id), $content,"ticket_monitoring",$id,get_cc($id));

				if(!empty($ticket->asker_more_info) && $ticket->asker_more_info > 0) {
					//notif ke penanya
					$content = "<p>".Auth::user()->name." has respond to Asking More Information request for ticket with Ticket Number ".ticketNumber($id)."</p>
					<div style='border: 2px solid #333; box-shadow: 0px 10px 15px -3px rgba(0,0,0,0.1);'>
						<div style='padding: 10px;'>
							<p style='background-color: #fff;'>
							".$input['message']."
							</p>
						</div>
					</div>
								<p>If you have more problem, you can contact us through available contact</p>";
					sendNotifEmail($ticket->asker_more_info, Auth::user()->name." has respond to Asking More Information request for ticket with Ticket Number ".ticketNumber($id), $content,"approve_request",$id);
				}
			}

			DB::commit();
			$html = view('comments')->with('ticket',$ticket)->render();
			echo json_encode(["success" => true, 'message' => "Your message has been sent", 'content'=>$html]);
		} catch (\Throwable $th) {
			DB::rollBack();
			echo json_encode(["success" => false, 'message' => $th->getMessage(), ]);

		}
    }

}
