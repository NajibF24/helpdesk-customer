<?php

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

// ======================== START CUSTOMIZING FUNCTION HELPERS ====================
//START : untuk Report
if (! function_exists('achievement')) {
    function achievement($data) {
			$achievement_percentage_delivery_time = achievement_percentage_delivery_time($data);
			$sla = json_decode($data->sla_json);
			$target_sla_percentage_delivery_time =  $sla->service_target ?? null;
			$achievement = null;
			if(!empty($target_sla_percentage_delivery_time) && !empty($achievement_percentage_delivery_time) &&
				is_numeric($target_sla_percentage_delivery_time) && is_numeric($achievement_percentage_delivery_time)) {
				$achievement = $achievement_percentage_delivery_time * 100 / ($target_sla_percentage_delivery_time/100);
			}
			return $achievement;
	}
}
if (! function_exists('achievement_percentage_delivery_time')) {
    function achievement_percentage_delivery_time($data) {
		$target_sla_delivery_time = target_sla_delivery_time($data);
		$delivered_time = $data->time_resolved_duration;
		if($delivered_time === 0) {
			//resolusi cepat, diluar jam kerja dikerjakan jadi resolusi 0 menit
			return 1;
		}
		$result =  "-";
		if(!empty($target_sla_delivery_time) && !empty($delivered_time) &&
			is_numeric($target_sla_delivery_time) && is_numeric($delivered_time)) {
			if($target_sla_delivery_time/$delivered_time >= 1) {
				$result =  1;
			} else {
				$result = $target_sla_delivery_time/$delivered_time;
			}
		}
		return $result;
	}
}
if (! function_exists('target_sla_delivery_time')) {
    function target_sla_delivery_time($data) {
		$sla_minutes = sla_minutes($data);
		$sla = json_decode($data->sla_json);
		$target_sla_delivery_time = "-";
		if(!empty($sla->service_target) && !empty($sla_minutes) &&
			is_numeric($sla->service_target) && is_numeric($sla_minutes)) {
			$target_sla_delivery_time = $sla_minutes * $sla->service_target/100;
		}
		return $target_sla_delivery_time;
	}
}
if (! function_exists('sla_minutes')) {
	function sla_minutes($data) {
		$sla_minutes = "-";
		$sla = json_decode($data->sla_json);

		if(!empty($sla->target_resolution_time) && !empty($sla->target_resolution_unit)) {
			$sla_minutes = $sla->target_resolution_time;
			if($sla->target_resolution_unit == "days") {
				$sla_minutes = $sla->target_resolution_time * 24 * 60;
			} else if($sla->target_resolution_unit == "hours") {
				$sla_minutes = $sla->target_resolution_time * 60;
			} else if($sla->target_resolution_unit == "minutes") {
				$sla_minutes = $sla->target_resolution_time;
			}
		}
		return $sla_minutes;
	}
}
//END : untuk Report

if (!function_exists('getAssignTime')) {
    function getAssignTime($agent_id) {
		//assign time didapat dengan mencari waktu yang sesuai di coverage windows
		//jika masuk salah satu jadwal maka assign time adalah adalah saat ini
		//jika tidak masuk, maka dicari jadwal waktu terdekat agent tsb

		$coverage_windows = DB::table('coverage_windows')
			->selectRaw('coverage_windows.*')
			->join('contact', 'contact.coverage_windows', '=', 'coverage_windows.id')
			->where('contact.id', $agent_id)->first();

		Log::info("getAssignTime");
		Log::info(json_encode($coverage_windows));

		//TAHAP 1: cek jam sekarang masuk coverage tidak
		if(!$coverage_windows) {
			Log::info("not found coverage windows agent_id".$agent_id);
			//tidak ada coverage windowsnya maka langsung sekarang jadi assign time
			return date("Y-m-d H:i:s");
		}
		$coverage_hours = $coverage_windows->coverage_hours;
		$list_cov = explode(",",$coverage_hours);
		$list_cov = array_map('trim', $list_cov);
		$list_cov = array_filter($list_cov); //remove empty elements

		if(empty($list_cov)) {
			Log::info("not found coverage hours agent_id".$agent_id);
			//tidak ada coverage_hoursnya maka langsung sekarang jadi assign time
			return date("Y-m-d H:i:s");
		}
		foreach($list_cov as $cov) {
			$d = explode(" - ",$cov);
			$e_start = explode(":",$d[0]);//contoh : 3:00:30
			$e_end = explode(":",$d[1]);//contoh : 4:30

			$day_number = $e_start[0];
			$start_hour = $e_start[1];
			$start_minute = $e_start[2];

			$start_time = $start_hour.':'.$start_minute;
			$end_time = $d[1];

			$now = date('H:i');
			$current_day_number = date('w');//get to number of the day (0 to 6, 0 being sunday, and 6 being saturday)


			//cek saat ini masuk range coverage windows agent
			//yang dicek hari nya dan juga jam kerjanya

			if(($day_number == $current_day_number) &&
				($start_time <= $now) && $now <= $end_time)
			{
				Log::info("STEP1");
				//assign time adalah sekarang
				//karena masuk jadwal
				return date("Y-m-d H:i:s");
			}
		}


		//TAHAP 2: JIKA TIDAK DITEMUKAN MAKA CEK WAKTU TERDEKAT DI HARI INI
		//KEMUDIAN CEK HARI2 BERIKUTNYA
		$list_agent_waktu_terdekat_hari2_berikutnya = [];
		$jarak_waktu_terdekat = null;

		//yang pertama dicek adalah hari ini, selanjutnya hari-hari berikutnya selama 6 hari ke depan
		$selected_day_number = date('w');//get to number of the day (0 to 6, 0 being sunday, and 6 being saturday)

		$today = date('w');//ini tidak  diubah di loop

		$penambahan_hari_dari_current_day = 0;
		for($i=1;$i<=6;$i++) {

			foreach($list_cov as $cov) {
				$d = explode(" - ",$cov);
				$e_start = explode(":",$d[0]);//contoh : 3:00:30
				$e_end = explode(":",$d[1]);//contoh : 4:30

				$day_number = $e_start[0];
				$start_hour = $e_start[1];
				$start_minute = $e_start[2];

				$start_time = $start_hour.':'.$start_minute;
				$end_time = $d[1];

				$now = date('H:i');

				if ($day_number == $selected_day_number) {
					Log::info("STEP2:".$penambahan_hari_dari_current_day.":".$selected_day_number);
					Log::info("$cov");
					if($today == $selected_day_number) {
						//cek sudah lewat atau belum di current day
						if($now >  $end_time) {
							//sudah lewat jadwalnya utk range hours ini, jadi diskip untuk loop ini
							continue;
						}
					}

					//ini jadwal terdekat di hari ini/hari2 berikutnya
					return date("Y-m-d", strtotime(' +'.$penambahan_hari_dari_current_day.' day'))." ".$start_time.':00';
				}

			}

			//geser hari ke hari berikutnya
			if($selected_day_number == 6) {
				$selected_day_number = 0;
			} else {
				$selected_day_number++;
			}
			$penambahan_hari_dari_current_day++;
		}
		Log::info("not found all logic for agent_id".$agent_id);
		//assign time adalah sekarang karena logic di atas sama sekali tidak ditemukan
		return date("Y-m-d H:i:s");
	}
}

if (! function_exists('setOnProgress')) {
	function setOnProgress($ticket_id) {
		//SAMAKAN DENGAN di ticketAction kondisi on_progresss
		$id = $ticket_id;
		$ticket = 	DB::table('ticket')
						->where('id', $id)->first();
		if(empty($ticket)) {
			return 0;
		}

		if(in_array($ticket->status,["Open","Resolved","Closed"])) {
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

		}
	}
}

if (! function_exists('update_related_ticket_string')) {
	function update_related_ticket_string($problem_ticket_id,$service_incident_ticket_id) {
		$ticket_problem = DB::table('ticket')->where('id',$problem_ticket_id)->first();
		$ticket_service_incident = DB::table('ticket')->where('id',$service_incident_ticket_id)->first();
		if($ticket_problem && $ticket_service_incident) {
			$problem_ticketNumber = $ticket_problem->ref;
			$service_incident_ticketNumber = $ticket_service_incident->ref;
			$related_to_problem = explode(", ",$ticket_problem->related_ticket);
			$related_to_service_incident = explode(", ",$ticket_service_incident->related_ticket);
			$related_to_problem[] = $service_incident_ticketNumber;
			$related_to_service_incident[] = $problem_ticketNumber;
			DB::table('ticket')->where('id',$problem_ticket_id)->update(['related_ticket'=>implode(", ",array_filter($related_to_problem))]);
			DB::table('ticket')->where('id',$service_incident_ticket_id)->update(['related_ticket'=>implode(", ",array_filter($related_to_service_incident))]);
		}
	}
}
if (!function_exists('get_company_role')) {
	function get_company_role()
	{
		$role = DB::table('lnkuserstoroles')->where('users_id', Auth::user()->id)->first();

		if ($role) {
			return DB::table('authorization_company')->where('role_id', $role->roles_id)->pluck('company')->toArray();
		}

		return array();
	}
}

if (! function_exists('statusHtml')) {
	function statusHtml($status) {
		$badge_class = "badge-primary";

		switch($status){

			case "Closed":
				$badge_class = "badge-lime";
				break;
			case "On Progress":
				$badge_class = "badge-yellow";
				break;
			case "Resolved":
				$badge_class = "badge-purple";
				break;
			case "Submit for Approval":
				$badge_class = "badge-cyan";
				//text_class = "text-cyan";
				break;
			case "Rejected":
				$badge_class = "badge-tomato";
				break;
			default:
				break;
		}

		return "<span class='element-for-status badge ".$badge_class."'>".$status."</span>";
		//$color = "text-warning";
		//$label = "";
		//if ($status == "Open") {
			//$color = "text-blue";
			//$label = "label-primary";
		//}
		//if($status == "Resolved") {
			//$color = "text-purple";
			//$label = "label-purple  ";
		//}
		//if($status == "Closed") {
			//$color = "text-success";
			//$label = "label-lime";
		//}
		//if($status == "Rejected") {
			//$color = "text-danger";
			//$label = "label-tomato";
		//}
		//if($status == "Submit for Approval") {
			////$color = "text-danger";
			//$label = "label-cyan";
		//}
		//if($status == "On Progress") {
			//$color = "text-warning";
			//$label = "label-yellow";
		//}
		//$str = '<h5 class="element-for-status label label-lg font-weight-bold '.$label.' label-inline">'.$status.'</h5>';
		//return $str;
	}
}

if (! function_exists('isActiveUser')) {
	function isActiveNik($nik) {
		$contact = DB::table('contact')->where('status','Active')->whereNull('deleted_at')->where('nik',$nik)->first();
		if(!empty($contact)) {
			return "Active";
		}
		$contact = DB::table('contact')->where('status','Inactive')->whereNull('deleted_at')->where('nik',$nik)->first();
		if(!empty($contact)) {
			return "Inactive";
		}

		$contact = DB::table('contact')->where('nik',$nik)->first();
		if(!empty($contact)) {
			if($contact->deleted_at != null) {
				return "Deleted";
			}

			if($contact->status =='Active') {
				return "Active";
			} else {
				return "Inactive";
			}

		} else {
			return "Not Exist";
		}
	}
}
if (! function_exists('isActiveEmailUser')) {
	function isActiveEmailUser($email) {
		$contact = DB::table('contact')->where('status','Active')->whereNull('deleted_at')->where('email',$email)->first();
		if(!empty($contact)) {
			return "Active";
		}
		$contact = DB::table('contact')->where('status','Inactive')->whereNull('deleted_at')->where('email',$email)->first();
		if(!empty($contact)) {
			return "Inactive";
		}

		$contact = DB::table('contact')->where('email',$email)->first();
		if(!empty($contact)) {
			if($contact->deleted_at != null) {
				return "Deleted";
			}

			if($contact->status =='Active') {
				return "Active";
			} else {
				return "Inactive";
			}

		} else {
			return "Not Exist";
		}
	}
}

if (! function_exists('print_approver_case_journey')) {
	function print_approver_case_journey($need_html_output_case_journey,$atasan,$ticket_approval) {
		if($need_html_output_case_journey == "need_html_output_case_journey") {
			?>
<!--begin::Item-->
<div class="timeline-item align-items-start">
    <!--begin::Label-->
    <div class="timeline-label font-weight-bolder text-dark-75 font-size-lg"></div>
    <!--end::Label-->
    <!--begin::Badge-->
    <?php
				if(!empty($ticket_approval->status)) {

					if ($ticket_approval->status == 'rejected') {
						$has_rejected = true;
					}
					?>
    <div class="timeline-badge">
        <i
            class="fa fa-genderless text-<?= App\Helpers\TicketStatusHelper::status_round_color($ticket_approval->status) ?> icon-xl"></i>
    </div>
    <?php } else { ?>
    <div class="timeline-badge">
        <i class="fa fa-genderless text-danger icon-xl"></i>
    </div>
    <?php } ?>
    <!--end::Badge-->
    <!--begin::Desc-->
    <div class="timeline-content font-weight-bolder font-size-lg text-dark-75 pl-3">
        <?php if(!empty($ticket_approval->status)) {
						echo ucfirst($ticket_approval->status);
					} else {
						echo "Wait for Approval";
					}
					?>
        <i class="text-light-50" <?= popoverJobTitle($atasan['contact_id']) ?>>
            <?="by " . sanitize($atasan['name'])?>
        </i>

        <?php if(!empty($ticket_approval)) { ?>
        <br><span><?= date('M d, Y H:i', strtotime($ticket_approval->created_at)) ?></span>
        <?php } else { ?>
        <br><span></span>
        <?php } ?>
    </div>
    <!--end::Desc-->
</div>
<!--end::Item-->
<?php
		}
	}
}

if (!function_exists('getContactCaseJourney')) {
	//fungsi ini dipakai di approval secara keseluruhan
	//baik di ApproveRequestController, case_journey_content, submit ServiceCatalog,ProblemCatalog,related tag notif
	function getContactCaseJourney($ticket,$include_self = "",$include_request_management_notif = "include_request_management_notif",$need_html_output_case_journey = "", $need_list_contact_not_unique=""){

		//use App\Helpers\TicketStatusHelper;
		//use Illuminate\Support\Facades\Auth;

						$list_contact_case_journey = [];//prepare data untuk notifikasi reply, dan cc di page ini
						$request_management = DB::table('request_management')->where('id', $ticket->request_management)->first();
					if(in_array($ticket->status,[
					//'Draft',
					//'new','pending','waiting_for_approval',
					//'Waiting for User','Submit for Approval',
					'Rejected','Open','On Progress','Resolved','Closed','Re-Open','Withdrawn'
					])) {
						$ticket_approval_list = DB::table('ticket_approval')
											->where('ticket_id',$ticket->id)
											->get();
						if(!empty($ticket_approval_list)) {
							foreach($ticket_approval_list as $ticket_approval) {
								$contact = DB::table('contact')->where('id',$ticket_approval->approval_id)->first();
								if(!empty($contact)) {
									$atasan = ['contact_id'=>$contact->id,'id'=>$contact->id,'name'=>$contact->name];

									$atasan['step_approval'] = "approval";
									$atasan['type_approval'] = "approval";

									$list_contact_case_journey[] = $atasan;
									print_approver_case_journey($need_html_output_case_journey,$atasan,$ticket_approval);
								}
							}
						}
					}
					else {

						//var_dump($request_management);
						//APPROVAL USER
						//if(empty($request_management->approval_user_custom)) {
						//1. MAX USER SUPERORDINATE
						if($request_management->approval_user_type == "max_user_superordinate") {
							if(empty($request_management->max_user_superordinate)) {
								//milih max_user_superordinate tapi belum input, diskip

							}
							else {

								//max_user_superordinate
								//ADA 3 kondisi
								//1. requester posisinya di bawah maxusersuperordinate
								//2. requester posisinya dari maxusersuperordinate ke atas (ambil 1 atasan saja)
								//3. ada kesalahan data sehingga tidak ditemukan maxusersuperordinate maka  (ambil 1 atasan saja)
								$semua_atasan = cekSemuaAtasan($ticket->requester);
								if(!empty($semua_atasan)) {

									$max_user_ditemukan = false;
									$diatas_atau_sama_dgn_max_user_position_level = false;
									foreach($semua_atasan as $atasan) {
										if($atasan['position_id'] == $request_management->max_user_superordinate) {
											$max_user_ditemukan = true;
										}

										$max_user_approval_level = DB::table('position')
															->where('id', $request_management->max_user_superordinate ?? null)
															->value('level');

										if(!empty($max_user_approval_level) && $max_user_approval_level <= $atasan['position_level']) {
											$diatas_atau_sama_dgn_max_user_position_level = true;
										}
									}
									if($max_user_ditemukan) {
										//user di bawah maxusersuperordinate
										foreach($semua_atasan as $atasan) {
											$ticket_approval = DB::table('ticket_approval')
																->where('ticket_id',$ticket->id)
																->where('approval_id',$atasan['contact_id'])
																->first();

											$atasan['step_approval'] = "approval user";
											$atasan['type_approval'] = "max user superordinate";
											$list_contact_case_journey[] = $atasan;
											print_approver_case_journey($need_html_output_case_journey,$atasan,$ticket_approval);

											if($atasan['position_id'] == $request_management->max_user_superordinate) {
												//UDAH MAKSIMUM
												break;
											}
										}
									}
									else if($diatas_atau_sama_dgn_max_user_position_level) {
										//user di bawah maxusersuperordinate
										foreach($semua_atasan as $atasan) {
											//var_dump($atasan);
											//echo "SSS";
											// check dulu jika position_id atasan lebih dari max_user_superordinate maka ambil 1 atasan saja untuk kebutuhan approval

											// if($atasan['position_id'] > $request_management->max_user_superordinate) {
											// }else{
											// }
											$ticket_approval = DB::table('ticket_approval')
																->where('ticket_id',$ticket->id)
																->where('approval_id',$atasan['contact_id'])
																->first();
											$atasan['step_approval'] = "approval user";
											$atasan['type_approval'] = "max user superordinate";
											$list_contact_case_journey[] = $atasan;
											print_approver_case_journey($need_html_output_case_journey,$atasan,$ticket_approval);

											$max_user_approval_level = DB::table('position')
																->where('id', $request_management->max_user_superordinate ?? null)
																->value('level');

											if(!empty($max_user_approval_level) && $max_user_approval_level <= $atasan['position_level']) {
												//UDAH MAKSIMUM
												break;
											}
										}
									}
									else {
										//user di atas atau sama dgn maxusersuperordinate
										//atau kondisinya tidak ditemukan maxusersuperordinate
										foreach($semua_atasan as $atasan) {
											if($atasan['position_id'] > $request_management->max_user_superordinate) {

											}else{
												$ticket_approval = DB::table('ticket_approval')
																	->where('ticket_id',$ticket->id)
																	->where('approval_id',$atasan['contact_id'])
																	->first();

												$atasan['step_approval'] = "approval user";
												$atasan['type_approval'] = "max user superordinate";
												$list_contact_case_journey[] = $atasan;
												print_approver_case_journey($need_html_output_case_journey,$atasan,$ticket_approval);
												//UDAH DAPAT 1 ATASAN LANGSUNG BREAK
												break;
											}
										}
									}
								}
							}
						}
						//else {
						//2. APPROVAL USER CUSTOM JOB TITLE
						else if($request_management->approval_user_type == "approval_user_custom_job_title") {
							if(empty($request_management->approval_user_custom)) {
								//milih approval_user_custom_job_title tapi belum input, diskip

							}
							else {
								//approval_user_custom job _title
								$list_approval_user_custom = explode(",",$request_management->approval_user_custom);

								for($i=0;$i<count($list_approval_user_custom);$i++) {
									$contact = DB::table('contact')
										->where('contact.status', '=', 'Active')
										->whereNull('contact.deleted_at')
										->where('contact.job_title',$list_approval_user_custom[$i])->first();

									if($contact) {
										$atasan = ['contact_id'=>$contact->id,'id'=>$contact->id,'name'=>$contact->name];
									} else {
										$need_check_jobtitle = $list_approval_user_custom[$i];
										$atasan = getMinimalSatuLevelAtasan($need_check_jobtitle);
									}

									//var_dump($atasan);die;
									//ini bukan atasan tapi biar mudah tinggal copy dari yg diatas

									//echo $approve_support_agent_id."<-approve_support_agent_id";
									if(!empty($atasan['id'])) { //kalau kosong diskip ke loop berikutnya
										$ticket_approval = DB::table('ticket_approval')
															->where('ticket_id',$ticket->id)
															->where('approval_id',$atasan['id'])
															->first();
										$atasan['step_approval'] = "approval user";
										$atasan['type_approval'] = "approval user custom";
										$list_contact_case_journey[] = $atasan;
										print_approver_case_journey($need_html_output_case_journey,$atasan,$ticket_approval);
									} else {
										$ticket_approval = null;
									}


								}

							}
							//////////////////////
						}
						//3. APPROVAL USER CUSTOM POSITION
						else {
							if(empty($request_management->approval_user_custom)) {
								//milih approval_user_custom_job_position tapi belum input, diskip

							}
							else {
								//approval user custom position
								$list_approval_user_custom = explode(",",$request_management->approval_user_custom);
								$semua_atasan = cekSemuaAtasan($ticket->requester);

								foreach($list_approval_user_custom as $auc_position_id) {
									//var_dump($auc_position_id);
									$atasan_posisi_ditemukan = false;
									if(!empty($semua_atasan)) {
										foreach($semua_atasan as $atasan) {
												//var_dump($atasan);
												if($atasan['position_id'] == $auc_position_id) {

														$atasan_posisi_ditemukan = true;


														$ticket_approval = DB::table('ticket_approval')
																			->where('ticket_id',$ticket->id)
																			->where('approval_id',$atasan['contact_id'])
																			->first();
															//$approval_name = DB::table('users')->where('id', $status->created_by)->first();
														$atasan['step_approval'] = "approval user";
														$atasan['type_approval'] = "approval user custom";
														$list_contact_case_journey[] = $atasan;
														print_approver_case_journey($need_html_output_case_journey,$atasan,$ticket_approval);

												}
										}
										if($atasan_posisi_ditemukan == false) {
											//tidak ditemukan
											//cari ke atasnya satu

											$auc_position_level = DB::table('position')
																->where('id', $contact->position ?? null)
																->value('level');
											if($auc_position_level > 0) {
												foreach($semua_atasan as $atasan) {

													if($atasan['position_level'] >= $auc_position_level) {
														//ketemu satu yang diatasnya atau sama levelnya

														$ticket_approval = DB::table('ticket_approval')
																			->where('ticket_id',$ticket->id)
																			->where('approval_id',$atasan['contact_id'])
																			->first();
														$atasan['step_approval'] = "approval user";
														$atasan['type_approval'] = "approval user custom";
														$list_contact_case_journey[] = $atasan;
														print_approver_case_journey($need_html_output_case_journey,$atasan,$ticket_approval);
														break;
													}
												}
											}
										}
									} else {
										//atasan ga ada skip
									}


								}
							}
						}

						//OPTIONAL APPROVAL USER JOB TITLE
						if(empty($request_management->optional_approval_user_custom_job_title)) {
							//milih approval_user_custom_job_title tapi belum input, diskip

						}
						else {
							//approval_user_custom job _title
							$list_approval_user_custom = explode(",",$request_management->optional_approval_user_custom_job_title);

							for($i=0;$i<count($list_approval_user_custom);$i++) {
								$contact = DB::table('contact')
									->where('contact.status', '=', 'Active')
									->whereNull('contact.deleted_at')
									->where('contact.job_title',$list_approval_user_custom[$i])->first();

								if($contact) {
									$atasan = ['contact_id'=>$contact->id,'id'=>$contact->id,'name'=>$contact->name];
								} else {
									$need_check_jobtitle = $list_approval_user_custom[$i];
									$atasan = getMinimalSatuLevelAtasan($need_check_jobtitle);
								}

								//var_dump($atasan);die;
								//ini bukan atasan tapi biar mudah tinggal copy dari yg diatas

								//echo $approve_support_agent_id."<-approve_support_agent_id";
								if(!empty($atasan['id'])) { //kalau kosong diskip ke loop berikutnya
									$ticket_approval = DB::table('ticket_approval')
														->where('ticket_id',$ticket->id)
														->where('approval_id',$atasan['id'])
														->first();
									$atasan['step_approval'] = "approval user";
									$atasan['type_approval'] = "approval user custom";
									$list_contact_case_journey[] = $atasan;
									print_approver_case_journey($need_html_output_case_journey,$atasan,$ticket_approval);
								} else {
									$ticket_approval = null;
								}


							}

						}

						//OPTIONAL APPROVAL USER POSITION
						if(empty($request_management->optional_approval_user_custom_position)) {
							//milih approval_user_custom_job_position tapi belum input, diskip

						}
						else {
							//approval user custom position
							$list_approval_user_custom = explode(",",$request_management->optional_approval_user_custom_position);
							$semua_atasan = cekSemuaAtasan($ticket->requester);

							foreach($list_approval_user_custom as $auc_position_id) {
								//var_dump($auc_position_id);
								$atasan_posisi_ditemukan = false;
								if(!empty($semua_atasan)) {
									foreach($semua_atasan as $atasan) {
											//var_dump($atasan);
											if($atasan['position_id'] == $auc_position_id) {

													$atasan_posisi_ditemukan = true;


													$ticket_approval = DB::table('ticket_approval')
																		->where('ticket_id',$ticket->id)
																		->where('approval_id',$atasan['contact_id'])
																		->first();
														//$approval_name = DB::table('users')->where('id', $status->created_by)->first();
													$atasan['step_approval'] = "approval user";
													$atasan['type_approval'] = "approval user custom";
													$list_contact_case_journey[] = $atasan;
													print_approver_case_journey($need_html_output_case_journey,$atasan,$ticket_approval);

											}
									}
									if($atasan_posisi_ditemukan == false) {
										//tidak ditemukan
										//cari ke atasnya satu

										$auc_position_level = DB::table('position')
															->where('id', $contact->position ?? null)
															->value('level');
										if($auc_position_level > 0) {
											foreach($semua_atasan as $atasan) {

												if($atasan['position_level'] >= $auc_position_level) {
													//ketemu satu yang diatasnya atau sama levelnya

													$ticket_approval = DB::table('ticket_approval')
																		->where('ticket_id',$ticket->id)
																		->where('approval_id',$atasan['contact_id'])
																		->first();
													$atasan['step_approval'] = "approval user";
													$atasan['type_approval'] = "approval user custom";
													$list_contact_case_journey[] = $atasan;
													print_approver_case_journey($need_html_output_case_journey,$atasan,$ticket_approval);
													break;
												}
											}
										}
									}
								} else {
									//atasan ga ada skip
								}


							}
						}

						//SIAPKAN SELECTED CONTACT EMPLOYEE UNTUK STEP APPROVAL BERIKUTNYA
						$assign_type_list = explode(",",$request_management->assignment_type);
						$assign_list = explode(",",$request_management->assignment_tier);
						if($assign_type_list[0] == 4) {
							//manual
							$employee_id_selected = $assign_list[0] ?? 0;
							$employee = DB::table('contact')
											->where('contact.status', '=', 'Active')
											->whereNull('contact.deleted_at')
											->where('id',$employee_id_selected)->first();
							//dd($employee);
							if(!empty($employee->job_title)) {
								//$job_title_id = $employee->job_title;
								$selected_contact_employee = $employee;
							}
						} else {
							//roundrobin loadbalance random
							$team_id = $assign_list[0] ?? 0;
							$ret_val = getEmployeeJabatanTerbawah($team_id);
							$job_title_id = $ret_val[0];
							$employee = $ret_val[1];
							$selected_contact_employee = $employee;
						}

						//APPROVAL SUPPORT
						//1. MAX SUPPORT APPROVAL
						if($request_management->approval_support_type == "max_support_approval_job_title") {
						//if(empty($request_management->approval_support_custom)) {
							if(empty($request_management->max_support_approval)) {
								//milih max_support_approval_job_title tapi belum input, diskip

							}
							else {
								//max_support_approval

								//dd($selected_contact_employee);
								$semua_atasan = cekSemuaAtasan($selected_contact_employee->id ?? 0);
								if(!empty($semua_atasan)) {
									$max_support_ditemukan = false;
									foreach($semua_atasan as $atasan) {
										if($atasan['job_title_id'] == $request_management->max_support_approval) {
											$max_support_ditemukan = true;
										}
									}
									if($max_support_ditemukan) {
										//user di bawah max support approval
										foreach($semua_atasan as $atasan) {
											//var_dump($atasan);
											//echo "SSS";
											$ticket_approval = DB::table('ticket_approval')
																->where('ticket_id',$ticket->id)
																->where('approval_id',$atasan['contact_id'])
																->first();
											$atasan['step_approval'] = "approval support";
											$atasan['type_approval'] = "max support approval";
											$list_contact_case_journey[] = $atasan;
											print_approver_case_journey($need_html_output_case_journey,$atasan,$ticket_approval);

											if($atasan['job_title_id'] == $request_management->max_support_approval) {
												//UDAH MAKSIMUM
												break;
											}
										}
									}
									else {
										//user di atas atau sama dgn max support approval
										//atau kondisinya tidak ditemukan max support approval
										foreach($semua_atasan as $atasan) {
											$ticket_approval = DB::table('ticket_approval')
																->where('ticket_id',$ticket->id)
																->where('approval_id',$atasan['contact_id'])
																->first();

											$atasan['step_approval'] = "approval support";
											$atasan['type_approval'] = "max support approval";
											$list_contact_case_journey[] = $atasan;
											print_approver_case_journey($need_html_output_case_journey,$atasan,$ticket_approval);
											//UDAH DAPAT 1 ATASAN LANGSUNG BREAK
											break;
										}
									}

								}
							}
						}
						else if($request_management->approval_support_type == "max_support_approval_position") {
							if(empty($request_management->max_support_approval)) {
								//milih max_support_approval_position tapi belum input, diskip
							}
							else {

								$semua_atasan = cekSemuaAtasan($selected_contact_employee->id ?? 0);
								if(!empty($semua_atasan)) {
									$max_support_ditemukan = false;
									$diatas_atau_sama_dgn_max_support_position_level = false;
									foreach($semua_atasan as $atasan) {
										if($atasan['position_id'] == $request_management->max_support_approval) {
											$max_support_ditemukan = true;
										}

										$max_support_approval_level = DB::table('position')
															->where('id', $request_management->max_support_approval ?? null)
															->value('level');

										if(!empty($max_support_approval_level) && $max_support_approval_level <= $atasan['position_level']) {
											$diatas_atau_sama_dgn_max_support_position_level = true;
										}

									}
									if($max_support_ditemukan) {
										//user di bawah max support approval
										foreach($semua_atasan as $atasan) {
											//var_dump($atasan);
											//echo "SSS";
											$ticket_approval = DB::table('ticket_approval')
																->where('ticket_id',$ticket->id)
																->where('approval_id',$atasan['contact_id'])
																->first();
											$atasan['step_approval'] = "approval support";
											$atasan['type_approval'] = "max support approval";
											$list_contact_case_journey[] = $atasan;
											print_approver_case_journey($need_html_output_case_journey,$atasan,$ticket_approval);

											if($atasan['position_id'] == $request_management->max_support_approval) {
												//UDAH MAKSIMUM
												break;
											}

										}
									}
									else if($diatas_atau_sama_dgn_max_support_position_level) {
										//user di bawah max support approval
										foreach($semua_atasan as $atasan) {
											//var_dump($atasan);
											//echo "SSS";
											$ticket_approval = DB::table('ticket_approval')
																->where('ticket_id',$ticket->id)
																->where('approval_id',$atasan['contact_id'])
																->first();
											$atasan['step_approval'] = "approval support";
											$atasan['type_approval'] = "max support approval";
											$list_contact_case_journey[] = $atasan;
											print_approver_case_journey($need_html_output_case_journey,$atasan,$ticket_approval);

											$max_support_approval_level = DB::table('position')
																->where('id', $request_management->max_support_approval ?? null)
																->value('level');

											if(!empty($max_support_approval_level) && $max_support_approval_level <= $atasan['position_level']) {
												//UDAH MAKSIMUM
												break;
											}
										}
									}
									else {
										//user di atas atau sama dgn max support approval
										//atau kondisinya tidak ditemukan max support approval
										foreach($semua_atasan as $atasan) {
											$ticket_approval = DB::table('ticket_approval')
																->where('ticket_id',$ticket->id)
																->where('approval_id',$atasan['contact_id'])
																->first();

											$atasan['step_approval'] = "approval support";
											$atasan['type_approval'] = "max support approval";
											$list_contact_case_journey[] = $atasan;
											print_approver_case_journey($need_html_output_case_journey,$atasan,$ticket_approval);
											//UDAH DAPAT 1 ATASAN LANGSUNG BREAK
											break;
										}
									}

								}




							}
						}
						else if($request_management->approval_support_type == "approval_support_custom") {
                            $jumlah_atasan_flow_normal = 0;
                            $id_orang_yg_approve_flow_normal = [];
                            $semua_atasan = cekSemuaAtasan($selected_contact_employee->id ?? 0);
                            // dd($semua_atasan);
							if(empty($request_management->approval_support_custom)) {
								//milih approval_support_custom tapi belum input, diskip

							}
							else {
								//approval_support_custom

                                // $max_user_ditemukan = false;
                                foreach($semua_atasan as $atasan) {
                                    $jumlah_atasan_flow_normal += 1;
                                    $id_orang_yg_approve_flow_normal[] = $atasan['contact_id'];
                                    if($atasan['position_id'] == $request_management->max_user_superordinate) {
                                        // $max_user_ditemukan = true;
                                        break;
                                    }
                                }
								$list_approval_support_custom = explode(",",$request_management->approval_support_custom);
                                // dd([$request_management, $semua_atasan, $list_approval_support_custom]);

								for($i=0;$i<count($list_approval_support_custom);$i++) {
									$contact = DB::table('contact')
													->where('contact.status', '=', 'Active')
													->whereNull('contact.deleted_at')
													->where('contact.job_title',$list_approval_support_custom[$i])->first();
									if($contact) {
										$atasan = ['contact_id'=>$contact->id,'id'=>$contact->id,'name'=>$contact->name];
									} else {
										$need_check_jobtitle = $list_approval_support_custom[$i];
										$atasan = getMinimalSatuLevelAtasan($need_check_jobtitle);
									}
									//var_dump($atasan);die;
									//ini bukan atasan tapi biar mudah tinggal copy dari yg diatas

									//echo $approve_support_agent_id."<-approve_support_agent_id";
                                    $cek_multiple = 0;
                                    // dd([$atasan['id'], $id_orang_yg_approve_flow_normal]);
                                    if (in_array($atasan['id'], $id_orang_yg_approve_flow_normal)) {
                                        $cek_multiple = 1;
                                    }
									if(!empty($atasan['id'])) { //kalau kosong diskip ke loop berikutnya
										//tambahkan ke list
										$atasan['step_approval'] = "approval support";
										$atasan['type_approval'] = "approval support custom";
										$list_contact_case_journey[] = $atasan;
										$jml_approver_tertentu_di_case_journey = 0;
										foreach($list_contact_case_journey as $select_contact) {
											if($select_contact['contact_id'] == $atasan['id']) {
												$jml_approver_tertentu_di_case_journey++;
											}
										}


										$count_approver_has_approve = DB::table('ticket_approval')
											->selectRaw('COUNT(approval_id) as count_approve')
											->where('ticket_id',$ticket->id)
											->where('approval_id',$atasan['id'])
											->value('count_approve');

										$t_ap = null;
										if($jml_approver_tertentu_di_case_journey <= $count_approver_has_approve) {
											//kalau approver x sudah melalukan approve lebih banyak atau sama dengan yg
											//total approver x yg tercatat di case journey
											//berarti dianggap sudah approved
											//kalau sebaliknya, maka dianggap belum approve utk step approver saat ini

											//get row approval yg paling tepat saat
											//approver tsb melakukan approve
											//agar tanggalny sesuai
                                            $t_ap = DB::table('ticket_approval')
												->where('ticket_id',$ticket->id)
												->where('approval_id',$atasan['id'])
												->offset($jml_approver_tertentu_di_case_journey - 1)->limit(1) //misal jml ad 2, maka ambil offsetnya 1, karena offset dimulai dari 0
												->first();
										}

										print_approver_case_journey($need_html_output_case_journey,$atasan,$t_ap);

									}

								}
							}

						}



					}
						//ASSIGNMENT STEP
						$ticket_assignment_log = DB::table('ticket_assignment_log')->where('ticket_id',$ticket->id)->get();
						//var_dump();
						if($ticket_assignment_log->count() <= 0) {
							//echo "KOSONG";
							if(!empty($request_management->assignment_tier)) {
								$assign_list = explode(",",$request_management->assignment_tier);
								$n = 0;
								foreach($assign_list as $team_id) {
									$n++;
									if($n == 1) {
										$contact = DB::table('contact')->where('id',$team_id)->first();

									}
								}
							}
						}
						else {
							foreach($ticket_assignment_log as $log) {
									$team = DB::table('contact')->where('id',$log->team_id)->first();
									$agent = DB::table('contact')->where('id',$log->agent_id)->first();

									if(!empty($agent->id)) {
										$list_contact_case_journey[] = ['id'=>$agent->id,'name'=>$agent->name];
									}
							}
						}

		$list_contact_not_unique = [];
		//var_dump($list_contact_case_journey);
		$list_contact = [];
		if(!empty($list_contact_case_journey)) {
			foreach($list_contact_case_journey as $l) {
				if(!empty($l['id'])) {
					$contact = DB::table('contact')->select('id','name','email')->where('id',$l['id'])->first();
				}
				if(!empty($l['contact_id'])) {
					$contact = DB::table('contact')->select('id','name','email')->where('id',$l['contact_id'])->first();
				}
				if(!empty($contact)) {
					if($include_self == "include self") {
						if(!empty($l['step_approval'])) {
							$contact->step_approval = $l['step_approval'] ?? "";
							$contact->type_approval = $l['type_approval'] ?? "";
						}
						$list_contact[$contact->id] = $contact;
						$list_contact_not_unique[] = $contact;
					} else {
						if(Auth::user()->person != $contact->id) {
							if(!empty($l['step_approval'])) {
								$contact->step_approval = $l['step_approval'] ?? "";
								$contact->type_approval = $l['type_approval'] ?? "";
							}
							$list_contact[$contact->id] = $contact;
							$list_contact_not_unique[] = $contact;
						}
					}
				}
			}
		}
		if($include_request_management_notif == "include_request_management_notif") {
			if(!empty($request_management->notif)) {
				$contacts = DB::table('contact')->whereIn('id',explode(",",$request_management->notif))->get()->toArray();
				foreach($contacts as $contact) {
					if(!empty($contact)) {
						if((!empty(Auth::user())) && (Auth::user()->person != $contact->id)) {
							$list_contact[$contact->id] = $contact;
						}
					}
				}
			}
			$contact = DB::table('contact')->select('id','name','email')->where('id',$ticket->requester)->first();
			if(!empty($contact->id)) {
				$list_contact[$contact->id] = $contact;
			}
		}

		//menyiapkan data2 sudah approved atau belum untuk masing2 approver
		if($need_list_contact_not_unique == "need_list_contact_not_unique"){
			//list_contact_not_unique menyediakan statusnya juga apakah sudah approve atau belum
			if(!empty($list_contact_not_unique)) {

				$array_contacts = [];
				foreach($list_contact_not_unique as $contact) {
					$array_contacts[] = $contact->id;
				}
				//menghitung banyakny contact di casejourney
				// https://www.php.net/manual/en/function.array-count-values.php
				$count_contact_in_casejourney = array_count_values($array_contacts);

				//get list approver yang sudah approve
				$ticket_approved_list = DB::table('ticket_approval')
						->selectRaw('approval_id')
						->where('ticket_id',$ticket->id)
						->whereIn('approval_id',$array_contacts)
						// ->groupBy('approval_id')
						->get();
				$approver_has_approved_list = [];
				foreach($ticket_approved_list as $ticket_approved) {
					$approver_has_approved_list[] = $ticket_approved->approval_id;
				}

				foreach($list_contact_not_unique as $key => $contact) {

					$index_approver = array_search($contact->id, $approver_has_approved_list);
					if($index_approver === false) {
						$has_approved = false;
					} else {
						unset($approver_has_approved_list[$index_approver]);
						$has_approved = true;
					}

					$list_contact_not_unique[$key]->has_approved = $has_approved;
				}

			}
			return $list_contact_not_unique;
		}

		return $list_contact;
	}
}

if (!function_exists('getInventoryManagementCaseJourneyReturn')) {
	//fungsi ini dipakai di approval secara keseluruhan
	//baik di ApproveRequestController, case_journey_content, submit ServiceCatalog,ProblemCatalog,related tag notif
	function getInventoryManagementCaseJourneyReturn($detail,$include_self = "",$include_request_management_notif = "include_request_management_notif",$need_html_output_case_journey = "", $need_list_contact_not_unique = "need_list_contact_not_unique"){

						$list_contact_case_journey = [];//prepare data untuk notifikasi reply, dan cc di page ini
						$request_management = DB::table('request_management')->where('id', $detail->request_management_id)->first();
						$semua_atasan = [];
					if(in_array(strtolower($detail->status),[
					//'Draft',
					//'new','pending','waiting_for_approval',
					//'Waiting for User','Submit for Approval',
					'rejected'
					])) {
						$detail_approval_list = DB::table('goods_receive_approvals')
											->where('goods_receive_id',$detail->id)
											->get();
						if(!empty($detail_approval_list)) {
							foreach($detail_approval_list as $detail_approval) {
								$contact = DB::table('contact')->where('id',$detail_approval->approver_id)->first();
								if(!empty($contact)) {
									$atasan = ['contact_id'=>$contact->id,'id'=>$contact->id,'name'=>$contact->name];

									$atasan['step_approval'] = "approval";
									$atasan['type_approval'] = "approval";

									$list_contact_case_journey[] = $atasan;
									print_approver_case_journey($need_html_output_case_journey,$atasan,$detail_approval);
								}
							}
						}
					}
					else {

						//var_dump($request_management);
						//APPROVAL USER
						//if(empty($request_management->approval_user_custom)) {
						//1. MAX USER SUPERORDINATE
						if($request_management->approval_user_type == "max_user_superordinate") {
							if(empty($request_management->max_user_superordinate)) {
								//milih max_user_superordinate tapi belum input, diskip
							}
							else {

								//max_user_superordinate
								//ADA 3 kondisi
								//1. requester posisinya di bawah maxusersuperordinate
								//2. requester posisinya dari maxusersuperordinate ke atas (ambil 1 atasan saja)
								//3. ada kesalahan data sehingga tidak ditemukan maxusersuperordinate maka  (ambil 1 atasan saja)
								$semua_atasan = cekSemuaAtasan($detail->requestor);
								if(!empty($semua_atasan)) {

									$max_user_ditemukan = false;
									$diatas_atau_sama_dgn_max_user_position_level = false;
									foreach($semua_atasan as $atasan) {
										if($atasan['position_id'] == $request_management->max_user_superordinate) {
											$max_user_ditemukan = true;
										}

										$max_user_approval_level = DB::table('position')
															->where('id', $request_management->max_user_superordinate ?? null)
															->value('level');

										if(!empty($max_user_approval_level) && $max_user_approval_level <= $atasan['position_level']) {
											$diatas_atau_sama_dgn_max_user_position_level = true;
										}
									}
									if($max_user_ditemukan) {
										//user di bawah maxusersuperordinate
										foreach($semua_atasan as $atasan) {
											$detail_approval = DB::table('goods_receive_approvals')
																->where('goods_receive_id',$detail->id)
																->where('approver_id',$atasan['contact_id'])
																->first();


											$atasan['step_approval'] = "approval user";
											$atasan['type_approval'] = "max user superordinate";
											$list_contact_case_journey[] = $atasan;
											print_approver_case_journey($need_html_output_case_journey,$atasan,$detail_approval);

											if($atasan['position_id'] == $request_management->max_user_superordinate) {
												//UDAH MAKSIMUM
												break;
											}
										}
                                        // dd([$atasan, $detail_approvals]);
									}
									else if($diatas_atau_sama_dgn_max_user_position_level) {
										//user di bawah maxusersuperordinate
										foreach($semua_atasan as $atasan) {
											//var_dump($atasan);
											//echo "SSS";
											$detail_approval = DB::table('goods_receive_approvals')
																->where('goods_receive_id',$detail->id)
																->where('approver_id',$atasan['contact_id'])
																->first();
											$atasan['step_approval'] = "approval user";
											$atasan['type_approval'] = "max user superordinate";
											$list_contact_case_journey[] = $atasan;
											print_approver_case_journey($need_html_output_case_journey,$atasan,$detail_approval);

											$max_user_approval_level = DB::table('position')
																->where('id', $request_management->max_user_superordinate ?? null)
																->value('level');

											if(!empty($max_user_approval_level) && $max_user_approval_level <= $atasan['position_level']) {
												//UDAH MAKSIMUM
												break;
											}
										}
									}
									else {
										//user di atas atau sama dgn maxusersuperordinate
										//atau kondisinya tidak ditemukan maxusersuperordinate
										foreach($semua_atasan as $atasan) {
											$detail_approval = DB::table('goods_receive_approvals')
																->where('goods_receive_id',$detail->id)
																->where('approver_id',$atasan['contact_id'])
																->first();

											$atasan['step_approval'] = "approval user";
											$atasan['type_approval'] = "max user superordinate";
											$list_contact_case_journey[] = $atasan;
											print_approver_case_journey($need_html_output_case_journey,$atasan,$detail_approval);
											//UDAH DAPAT 1 ATASAN LANGSUNG BREAK
											break;
										}
									}
								}
							}
						}
						//else {
						//2. APPROVAL USER CUSTOM JOB TITLE
						else if($request_management->approval_user_type == "approval_user_custom_job_title") {
							if(empty($request_management->approval_user_custom)) {
								//milih approval_user_custom_job_title tapi belum input, diskip

							}
							else {
								//approval_user_custom job _title
								$list_approval_user_custom = explode(",",$request_management->approval_user_custom);

								for($i=0;$i<count($list_approval_user_custom);$i++) {
									$contact = DB::table('contact')
										->where('contact.status', '=', 'Active')
										->whereNull('contact.deleted_at')
										->where('contact.job_title',$list_approval_user_custom[$i])->first();

									if($contact) {
										$atasan = ['contact_id'=>$contact->id,'id'=>$contact->id,'name'=>$contact->name];
									} else {
										$need_check_jobtitle = $list_approval_user_custom[$i];
										$atasan = getMinimalSatuLevelAtasan($need_check_jobtitle);
									}

									//var_dump($atasan);die;
									//ini bukan atasan tapi biar mudah tinggal copy dari yg diatas

									//echo $approve_support_agent_id."<-approve_support_agent_id";
									if(!empty($atasan['id'])) { //kalau kosong diskip ke loop berikutnya
										$detail_approval = DB::table('goods_receive_approvals')
															->where('goods_receive_id',$detail->id)
															->where('approver_id',$atasan['id'])
															->first();
										$atasan['step_approval'] = "approval user";
										$atasan['type_approval'] = "approval user custom";
										$list_contact_case_journey[] = $atasan;
										print_approver_case_journey($need_html_output_case_journey,$atasan,$detail_approval);
									} else {
										$detail_approval = null;
									}


								}

							}
							//////////////////////
						}
						//3. APPROVAL USER CUSTOM POSITION
						else {
							if(empty($request_management->approval_user_custom)) {
								//milih approval_user_custom_job_position tapi belum input, diskip

							}
							else {
								//approval user custom position
								$list_approval_user_custom = explode(",",$request_management->approval_user_custom);
								$semua_atasan = cekSemuaAtasan($detail->requestor);

								foreach($list_approval_user_custom as $auc_position_id) {
									//var_dump($auc_position_id);
									$atasan_posisi_ditemukan = false;
									if(!empty($semua_atasan)) {
										foreach($semua_atasan as $atasan) {
												//var_dump($atasan);
												if($atasan['position_id'] == $auc_position_id) {

														$atasan_posisi_ditemukan = true;


														$detail_approval = DB::table('goods_receive_approvals')
																			->where('goods_receive_id',$detail->id)
																			->where('approver_id',$atasan['contact_id'])
																			->first();
															//$approval_name = DB::table('users')->where('id', $status->created_by)->first();
														$atasan['step_approval'] = "approval user";
														$atasan['type_approval'] = "approval user custom";
														$list_contact_case_journey[] = $atasan;
														print_approver_case_journey($need_html_output_case_journey,$atasan,$detail_approval);

												}
										}
										if($atasan_posisi_ditemukan == false) {
											//tidak ditemukan
											//cari ke atasnya satu

											$auc_position_level = DB::table('position')
																->where('id', $contact->position ?? null)
																->value('level');
											if($auc_position_level > 0) {
												foreach($semua_atasan as $atasan) {

													if($atasan['position_level'] >= $auc_position_level) {
														//ketemu satu yang diatasnya atau sama levelnya

														$detail_approval = DB::table('goods_receive_approvals')
																			->where('goods_receive_id',$detail->id)
																			->where('approver_id',$atasan['contact_id'])
																			->first();
														$atasan['step_approval'] = "approval user";
														$atasan['type_approval'] = "approval user custom";
														$list_contact_case_journey[] = $atasan;
														print_approver_case_journey($need_html_output_case_journey,$atasan,$detail_approval);
														break;
													}
												}
											}
										}
									} else {
										//atasan ga ada skip
									}


								}
							}
						}

						//OPTIONAL APPROVAL USER JOB TITLE
						if(empty($request_management->optional_approval_user_custom_job_title)) {
							//milih approval_user_custom_job_title tapi belum input, diskip

						}
						else {
							//approval_user_custom job _title
							$list_approval_user_custom = explode(",",$request_management->optional_approval_user_custom_job_title);

							for($i=0;$i<count($list_approval_user_custom);$i++) {
								$contact = DB::table('contact')
									->where('contact.status', '=', 'Active')
									->whereNull('contact.deleted_at')
									->where('contact.job_title',$list_approval_user_custom[$i])->first();

								if($contact) {
									$atasan = ['contact_id'=>$contact->id,'id'=>$contact->id,'name'=>$contact->name];
								} else {
									$need_check_jobtitle = $list_approval_user_custom[$i];
									$atasan = getMinimalSatuLevelAtasan($need_check_jobtitle);
								}

								//var_dump($atasan);die;
								//ini bukan atasan tapi biar mudah tinggal copy dari yg diatas

								//echo $approve_support_agent_id."<-approve_support_agent_id";
								if(!empty($atasan['id'])) { //kalau kosong diskip ke loop berikutnya
									$detail_approval = DB::table('goods_receive_approvals')
														->where('goods_receive_id',$detail->id)
														->where('approver_id',$atasan['id'])
														->first();
									$atasan['step_approval'] = "approval user";
									$atasan['type_approval'] = "approval user custom";
									$list_contact_case_journey[] = $atasan;
									print_approver_case_journey($need_html_output_case_journey,$atasan,$detail_approval);
								} else {
									$detail_approval = null;
								}


							}

						}

						//OPTIONAL APPROVAL USER POSITION
						if(empty($request_management->optional_approval_user_custom_position)) {
							//milih approval_user_custom_job_position tapi belum input, diskip

						}
						else {
							//approval user custom position
							$list_approval_user_custom = explode(",",$request_management->optional_approval_user_custom_position);
							$semua_atasan = cekSemuaAtasan($detail->requestor);

							foreach($list_approval_user_custom as $auc_position_id) {
								//var_dump($auc_position_id);
								$atasan_posisi_ditemukan = false;
								if(!empty($semua_atasan)) {
									foreach($semua_atasan as $atasan) {
											//var_dump($atasan);
											if($atasan['position_id'] == $auc_position_id) {

													$atasan_posisi_ditemukan = true;


													$detail_approval = DB::table('goods_receive_approvals')
																		->where('goods_receive_id',$detail->id)
																		->where('approver_id',$atasan['contact_id'])
																		->first();
														//$approval_name = DB::table('users')->where('id', $status->created_by)->first();
													$atasan['step_approval'] = "approval user";
													$atasan['type_approval'] = "approval user custom";
													$list_contact_case_journey[] = $atasan;
													print_approver_case_journey($need_html_output_case_journey,$atasan,$detail_approval);

											}
									}
									if($atasan_posisi_ditemukan == false) {
										//tidak ditemukan
										//cari ke atasnya satu

										$auc_position_level = DB::table('position')
															->where('id', $contact->position ?? null)
															->value('level');
										if($auc_position_level > 0) {
											foreach($semua_atasan as $atasan) {

												if($atasan['position_level'] >= $auc_position_level) {
													//ketemu satu yang diatasnya atau sama levelnya

													$detail_approval = DB::table('goods_receive_approvals')
																		->where('goods_receive_id',$detail->id)
																		->where('approver_id',$atasan['contact_id'])
																		->first();
													$atasan['step_approval'] = "approval user";
													$atasan['type_approval'] = "approval user custom";
													$list_contact_case_journey[] = $atasan;
													print_approver_case_journey($need_html_output_case_journey,$atasan,$detail_approval);
													break;
												}
											}
										}
									}
								} else {
									//atasan ga ada skip
								}


							}
						}

                        //SIAPKAN SELECTED CONTACT EMPLOYEE UNTUK STEP APPROVAL BERIKUTNYA
						$assign_type_list = explode(",",$request_management->assignment_type);
						$assign_list = explode(",",$request_management->assignment_tier);

                        // dd($request_management);
                        if($request_management->approval_support_type == "approval_support_custom") {
                            $jumlah_atasan_flow_normal = 0;
                            $id_orang_yg_approve_flow_normal = [];
							if(empty($request_management->approval_support_custom)) {
								//milih approval_support_custom tapi belum input, diskip

							}
							else {
								//approval_support_custom

                                // $max_user_ditemukan = false;
                                foreach($semua_atasan as $atasan) {
                                    $jumlah_atasan_flow_normal += 1;
                                    $id_orang_yg_approve_flow_normal[] = $atasan['contact_id'];
                                    if($atasan['position_id'] == $request_management->max_user_superordinate) {
                                        // $max_user_ditemukan = true;
                                        break;
                                    }
                                }
								$list_approval_support_custom = explode(",",$request_management->approval_support_custom);
                                // dd([$request_management, $semua_atasan, $list_approval_support_custom]);

								for($i=0;$i<count($list_approval_support_custom);$i++) {
									$contact = DB::table('contact')
													->where('contact.status', '=', 'Active')
													->whereNull('contact.deleted_at')
													->where('contact.job_title',$list_approval_support_custom[$i])->first();
									if($contact) {
										$atasan = ['contact_id'=>$contact->id,'id'=>$contact->id,'name'=>$contact->name];
									} else {
										$need_check_jobtitle = $list_approval_support_custom[$i];
										$atasan = getMinimalSatuLevelAtasan($need_check_jobtitle);
									}
									//var_dump($atasan);die;
									//ini bukan atasan tapi biar mudah tinggal copy dari yg diatas

									//echo $approve_support_agent_id."<-approve_support_agent_id";
                                    $cek_multiple = 0;
                                    // dd([$atasan['id'], $id_orang_yg_approve_flow_normal]);
                                    if (in_array($atasan['id'], $id_orang_yg_approve_flow_normal)) {
                                        $cek_multiple = 1;
                                    }
									if(!empty($atasan['id'])) { //kalau kosong diskip ke loop berikutnya
										$detail_approval = DB::table('goods_receive_approvals')
															->where('goods_receive_id',$detail->id)
															->where('approver_id',$atasan['id'])
															->get();
                                                            // dd([$cek_multiple, $detail_approval]);
										//tambahkan ke list
										$atasan['step_approval'] = "approval support";
										$atasan['type_approval'] = "approval support custom";
										$list_contact_case_journey[] = $atasan;
                                        if ($detail_approval->isEmpty()) {
                                            $t_ap = DB::table('goods_receive_approvals')
                                            ->where('goods_receive_id',$detail->id)
                                            ->where('approver_id',$atasan['id'])
                                            ->first();
                                        }
                                        if (empty($detail_approval[$cek_multiple]) && $cek_multiple == 1 && !empty($detail_approval[0])) {
                                            $detail_approval[$cek_multiple] = $detail_approval[0];
                                            $detail_approval[$cek_multiple]->status = null;
                                            $t_ap = $detail_approval[$cek_multiple];
                                        }
                                        if (empty($t_ap)) {
                                            $t_ap = DB::table('goods_receive_approvals')
                                            ->where('goods_receive_id',$detail->id)
                                            ->where('approver_id',$atasan['id'])
                                            ->first();
                                        }
										print_approver_case_journey($need_html_output_case_journey,$atasan,$t_ap);
                                        // dd($detail_approval);
									} else {
										//atasan tidak ditemukan
										$detail_approval = null;
									}

								}
							}


						}

					}


		//var_dump($list_contact_case_journey);
		$list_contact = [];

		if(!empty($list_contact_case_journey)) {
			foreach($list_contact_case_journey as $l) {
				if(!empty($l['id'])) {
					$contact = DB::table('contact')->select('id','name','email')->where('id',$l['id'])->first();
				}
				if(!empty($l['contact_id'])) {
					$contact = DB::table('contact')->select('id','name','email')->where('id',$l['contact_id'])->first();
				}
				if(!empty($contact)) {
					if($include_self == "include self") {
						if(!empty($l['step_approval'])) {
							$contact->step_approval = $l['step_approval'] ?? "";
							$contact->type_approval = $l['type_approval'] ?? "";
						}
						$list_contact[$contact->id] = $contact;
					} else {
						if(Auth::user()->person != $contact->id) {
							if(!empty($l['step_approval'])) {
								$contact->step_approval = $l['step_approval'] ?? "";
								$contact->type_approval = $l['type_approval'] ?? "";
							}
							$list_contact[$contact->id] = $contact;
						}
					}
				}
			}
		}
		if($include_request_management_notif == "include_request_management_notif") {
			if(!empty($request_management->notif)) {
				$contacts = DB::table('contact')->whereIn('id',explode(",",$request_management->notif))->get()->toArray();
				foreach($contacts as $contact) {
					if(!empty($contact)) {
						if((!empty(Auth::user())) && (Auth::user()->person != $contact->id)) {
							$list_contact[$contact->id] = $contact;
						}
					}
				}
			}
			$contact = DB::table('contact')->select('id','name','email')->where('id',$detail->requestor)->first();
			if(!empty($contact->id)) {
				$list_contact[$contact->id] = $contact;
			}
		}
		$contact_case_journey = [];
        foreach ($list_contact as $key => $value) {
            if ($value->type_approval == 'approval support custom') {
                $contact_case_journey[$key] = $value;
            } else {
				$contact_case_journey[$value->id] = $value;
			}
        }

		$list_contact_not_unique = [];
		//var_dump($list_contact_case_journey);
		$list_contact = [];
		if(!empty($list_contact_case_journey)) {
			foreach($list_contact_case_journey as $l) {
				if(!empty($l['id'])) {
					$contact = DB::table('contact')->select('id','name','email')->where('id',$l['id'])->first();
				}
				if(!empty($l['contact_id'])) {
					$contact = DB::table('contact')->select('id','name','email')->where('id',$l['contact_id'])->first();
				}
				if(!empty($contact)) {
					if($include_self == "include self") {
						if(!empty($l['step_approval'])) {
							$contact->step_approval = $l['step_approval'] ?? "";
							$contact->type_approval = $l['type_approval'] ?? "";
						}
						$list_contact[$contact->id] = $contact;
						$list_contact_not_unique[] = $contact;
					} else {
						if(Auth::user()->person != $contact->id) {
							if(!empty($l['step_approval'])) {
								$contact->step_approval = $l['step_approval'] ?? "";
								$contact->type_approval = $l['type_approval'] ?? "";
							}
							$list_contact[$contact->id] = $contact;
							$list_contact_not_unique[] = $contact;
						}
					}
				}
			}
		}
		if($include_request_management_notif == "include_request_management_notif") {
			if(!empty($request_management->notif)) {
				$contacts = DB::table('contact')->whereIn('id',explode(",",$request_management->notif))->get()->toArray();
				foreach($contacts as $contact) {
					if(!empty($contact)) {
						if((!empty(Auth::user())) && (Auth::user()->person != $contact->id)) {
							$list_contact[$contact->id] = $contact;
						}
					}
				}
			}
			$contact = DB::table('contact')->select('id','name','email')->where('id',$detail->requestor)->first();
			if(!empty($contact->id)) {
				$list_contact[$contact->id] = $contact;
			}
		}

		if($need_list_contact_not_unique == "need_list_contact_not_unique"){
			//list_contact_not_unique menyediakan statusnya juga apakah sudah approve atau belum
			if(!empty($list_contact_not_unique)) {

				$array_contacts = [];
				foreach($list_contact_not_unique as $contact) {
					$array_contacts[] = $contact->id;
				}
				//menghitung banyakny contact di casejourney
				// https://www.php.net/manual/en/function.array-count-values.php
				$count_contact_in_casejourney = array_count_values($array_contacts);

				//get list approver yang sudah approve
				$ticket_approved_list = DB::table('goods_receive_approvals')
						->selectRaw('approver_id')
						->where('goods_receive_id',$detail->id)
						->whereIn('approver_id',$array_contacts)
						// ->groupBy('approval_id')
						->get();
				$approver_has_approved_list = [];
				foreach($ticket_approved_list as $ticket_approved) {
					$approver_has_approved_list[] = $ticket_approved->approver_id;
				}

				foreach($list_contact_not_unique as $key => $contact) {

					$index_approver = array_search($contact->id, $approver_has_approved_list);
					if($index_approver === false) {
						$has_approved = false;
					} else {
						unset($approver_has_approved_list[$index_approver]);
						$has_approved = true;
					}

					$list_contact_not_unique[$key]->has_approved = $has_approved;
				}

			}

			// dd($list_contact_not_unique);
			return $list_contact_not_unique;
		}


        return $contact_case_journey;
	}
}

if(!function_exists('label_required')) {
	function label_required($fieldName) {
		return \Html::decode($fieldName.' &lt;span class=&quot;text-danger&quot;&gt;*&lt;/span&gt;');
	}
}

if (!function_exists('checkReallyLastApprover')) {
	function checkReallyLastApprover($contact_case_journey, $ticket_id, $last_approver) {

		$array_contacts = [];
		foreach($contact_case_journey as $contact) {
			$array_contacts[] = $contact->id;
		}

		$count_approver_has_approve = DB::table('ticket_approval')
			->selectRaw('COUNT(approval_id) as count_approve')
			->where('ticket_id',$ticket_id)
			->where('approval_id',$last_approver->id)
			// ->groupBy('approval_id')
			->value('count_approve');

		//menghitung banyakny contact di casejourney
		// https://www.php.net/manual/en/function.array-count-values.php

		//misal di case journey ada 2, yg sudah diapprove ada 1 maka
		//flag true
		//tapi jika yg diapprove 0 maka
		//flag false

		$flag_last_approver = false;
		$count_contact_in_casejourney = array_count_values($array_contacts);

		if($count_contact_in_casejourney[$last_approver->id] <= (($count_approver_has_approve ?? 0) +1) ) {
			$flag_last_approver = true;
		}

		return	$flag_last_approver;
	}
}

if (!function_exists('getInventoryManagementCaseJourney')) {
	//fungsi ini dipakai di approval secara keseluruhan
	//baik di ApproveRequestController, case_journey_content, submit ServiceCatalog,ProblemCatalog,related tag notif
	function getInventoryManagementCaseJourney($detail,$include_self = "",$include_request_management_notif = "include_request_management_notif",$need_html_output_case_journey = "", $need_list_contact_not_unique="need_list_contact_not_unique"){
				$semua_atasan = [];
						$list_contact_case_journey = [];//prepare data untuk notifikasi reply, dan cc di page ini
						$request_management = DB::table('request_management')->where('id', $detail->request_management_id)->first();
					if(in_array(strtolower($detail->status),[
					//'Draft',
					//'new','pending','waiting_for_approval',
					//'Waiting for User','Submit for Approval',
					'rejected'
					])) {
						$detail_approval_list = DB::table('goods_issue_approvals')
											->where('goods_issue_id',$detail->id)
											->get();
						if(!empty($detail_approval_list)) {
							foreach($detail_approval_list as $detail_approval) {
								$contact = DB::table('contact')->where('id',$detail_approval->approver_id)->first();
								if(!empty($contact)) {
									$atasan = ['contact_id'=>$contact->id,'id'=>$contact->id,'name'=>$contact->name];

									$atasan['step_approval'] = "approval";
									$atasan['type_approval'] = "approval";

									$list_contact_case_journey[] = $atasan;
									print_approver_case_journey($need_html_output_case_journey,$atasan,$detail_approval);
								}
							}
						}
					}
					else {

						//var_dump($request_management);
						//APPROVAL USER
						//if(empty($request_management->approval_user_custom)) {
						//1. MAX USER SUPERORDINATE
						if($request_management->approval_user_type == "max_user_superordinate") {
							if(empty($request_management->max_user_superordinate)) {
								//milih max_user_superordinate tapi belum input, diskip
							}
							else {

								//max_user_superordinate
								//ADA 3 kondisi
								//1. requester posisinya di bawah maxusersuperordinate
								//2. requester posisinya dari maxusersuperordinate ke atas (ambil 1 atasan saja)
								//3. ada kesalahan data sehingga tidak ditemukan maxusersuperordinate maka  (ambil 1 atasan saja)
								$semua_atasan = cekSemuaAtasan($detail->requestor);
								// dd($semua_atasan);
								if(!empty($semua_atasan)) {

									$max_user_ditemukan = false;
									$diatas_atau_sama_dgn_max_user_position_level = false;
									foreach($semua_atasan as $atasan) {
										if($atasan['position_id'] == $request_management->max_user_superordinate) {
											$max_user_ditemukan = true;
										}

										$max_user_approval_level = DB::table('position')
															->where('id', $request_management->max_user_superordinate ?? null)
															->value('level');


										if(!empty($max_user_approval_level) && $max_user_approval_level <= $atasan['position_level']) {
											$diatas_atau_sama_dgn_max_user_position_level = true;
										}
									}

									if($max_user_ditemukan) {
										//user di bawah maxusersuperordinate
										foreach($semua_atasan as $atasan) {
											$detail_approval = DB::table('goods_issue_approvals')
																->where('goods_issue_id',$detail->id)
																->where('approver_id',$atasan['contact_id'])
																->first();


											$atasan['step_approval'] = "approval user";
											$atasan['type_approval'] = "max user superordinate";
											$list_contact_case_journey[] = $atasan;
											print_approver_case_journey($need_html_output_case_journey,$atasan,$detail_approval);

											if($atasan['position_id'] == $request_management->max_user_superordinate) {
												//UDAH MAKSIMUM
												break;
											}
										}
                                        // dd([$atasan, $detail_approvals]);
									}
									else if($diatas_atau_sama_dgn_max_user_position_level) {
										//user di bawah maxusersuperordinate
										foreach($semua_atasan as $atasan) {
											//var_dump($atasan);
											//echo "SSS";
											$detail_approval = DB::table('goods_issue_approvals')
																->where('goods_issue_id',$detail->id)
																->where('approver_id',$atasan['contact_id'])
																->first();
											$atasan['step_approval'] = "approval user";
											$atasan['type_approval'] = "max user superordinate";
											$list_contact_case_journey[] = $atasan;
											print_approver_case_journey($need_html_output_case_journey,$atasan,$detail_approval);

											$max_user_approval_level = DB::table('position')
																->where('id', $request_management->max_user_superordinate ?? null)
																->value('level');

																// dd(123);
											if(!empty($max_user_approval_level) && $max_user_approval_level <= $atasan['position_level']) {
												//UDAH MAKSIMUM
												// dd(123);
												break;
											}
										}
									}
									else {
										//user di atas atau sama dgn maxusersuperordinate
										//atau kondisinya tidak ditemukan maxusersuperordinate
										foreach($semua_atasan as $atasan) {
											$detail_approval = DB::table('goods_issue_approvals')
																->where('goods_issue_id',$detail->id)
																->where('approver_id',$atasan['contact_id'])
																->first();

											$atasan['step_approval'] = "approval user";
											$atasan['type_approval'] = "max user superordinate";
											$list_contact_case_journey[] = $atasan;
											print_approver_case_journey($need_html_output_case_journey,$atasan,$detail_approval);
											//UDAH DAPAT 1 ATASAN LANGSUNG BREAK
											break;
										}
									}
								}
							}
						}
						//else {
						//2. APPROVAL USER CUSTOM JOB TITLE
						else if($request_management->approval_user_type == "approval_user_custom_job_title") {
							if(empty($request_management->approval_user_custom)) {
								//milih approval_user_custom_job_title tapi belum input, diskip

							}
							else {
								//approval_user_custom job _title
								$list_approval_user_custom = explode(",",$request_management->approval_user_custom);

								for($i=0;$i<count($list_approval_user_custom);$i++) {
									$contact = DB::table('contact')
										->where('contact.status', '=', 'Active')
										->whereNull('contact.deleted_at')
										->where('contact.job_title',$list_approval_user_custom[$i])->first();

									if($contact) {
										$atasan = ['contact_id'=>$contact->id,'id'=>$contact->id,'name'=>$contact->name];
									} else {
										$need_check_jobtitle = $list_approval_user_custom[$i];
										$atasan = getMinimalSatuLevelAtasan($need_check_jobtitle);
									}

									//var_dump($atasan);die;
									//ini bukan atasan tapi biar mudah tinggal copy dari yg diatas

									//echo $approve_support_agent_id."<-approve_support_agent_id";
									if(!empty($atasan['id'])) { //kalau kosong diskip ke loop berikutnya
										$detail_approval = DB::table('goods_issue_approvals')
															->where('goods_issue_id',$detail->id)
															->where('approver_id',$atasan['id'])
															->first();
										$atasan['step_approval'] = "approval user";
										$atasan['type_approval'] = "approval user custom";
										$list_contact_case_journey[] = $atasan;
										print_approver_case_journey($need_html_output_case_journey,$atasan,$detail_approval);
									} else {
										$detail_approval = null;
									}


								}

							}
							//////////////////////
						}
						//3. APPROVAL USER CUSTOM POSITION
						else {
							if(empty($request_management->approval_user_custom)) {
								//milih approval_user_custom_job_position tapi belum input, diskip

							}
							else {
								//approval user custom position
								$list_approval_user_custom = explode(",",$request_management->approval_user_custom);
								$semua_atasan = cekSemuaAtasan($detail->requestor);

								foreach($list_approval_user_custom as $auc_position_id) {
									//var_dump($auc_position_id);
									$atasan_posisi_ditemukan = false;
									if(!empty($semua_atasan)) {
										foreach($semua_atasan as $atasan) {
												//var_dump($atasan);
												if($atasan['position_id'] == $auc_position_id) {

														$atasan_posisi_ditemukan = true;


														$detail_approval = DB::table('goods_issue_approvals')
																			->where('goods_issue_id',$detail->id)
																			->where('approver_id',$atasan['contact_id'])
																			->first();
															//$approval_name = DB::table('users')->where('id', $status->created_by)->first();
														$atasan['step_approval'] = "approval user";
														$atasan['type_approval'] = "approval user custom";
														$list_contact_case_journey[] = $atasan;
														print_approver_case_journey($need_html_output_case_journey,$atasan,$detail_approval);

												}
										}
										if($atasan_posisi_ditemukan == false) {
											//tidak ditemukan
											//cari ke atasnya satu

											$auc_position_level = DB::table('position')
																->where('id', $contact->position ?? null)
																->value('level');
											if($auc_position_level > 0) {
												foreach($semua_atasan as $atasan) {

													if($atasan['position_level'] >= $auc_position_level) {
														//ketemu satu yang diatasnya atau sama levelnya

														$detail_approval = DB::table('goods_issue_approvals')
																			->where('goods_issue_id',$detail->id)
																			->where('approver_id',$atasan['contact_id'])
																			->first();
														$atasan['step_approval'] = "approval user";
														$atasan['type_approval'] = "approval user custom";
														$list_contact_case_journey[] = $atasan;
														print_approver_case_journey($need_html_output_case_journey,$atasan,$detail_approval);
														break;
													}
												}
											}
										}
									} else {
										//atasan ga ada skip
									}


								}
							}
						}

						//OPTIONAL APPROVAL USER JOB TITLE
						if(empty($request_management->optional_approval_user_custom_job_title)) {
							//milih approval_user_custom_job_title tapi belum input, diskip

						}
						else {
							//approval_user_custom job _title
							$list_approval_user_custom = explode(",",$request_management->optional_approval_user_custom_job_title);

							for($i=0;$i<count($list_approval_user_custom);$i++) {
								$contact = DB::table('contact')
									->where('contact.status', '=', 'Active')
									->whereNull('contact.deleted_at')
									->where('contact.job_title',$list_approval_user_custom[$i])->first();

								if($contact) {
									$atasan = ['contact_id'=>$contact->id,'id'=>$contact->id,'name'=>$contact->name];
								} else {
									$need_check_jobtitle = $list_approval_user_custom[$i];
									$atasan = getMinimalSatuLevelAtasan($need_check_jobtitle);
								}

								//var_dump($atasan);die;
								//ini bukan atasan tapi biar mudah tinggal copy dari yg diatas

								//echo $approve_support_agent_id."<-approve_support_agent_id";
								if(!empty($atasan['id'])) { //kalau kosong diskip ke loop berikutnya
									$detail_approval = DB::table('goods_issue_approvals')
														->where('goods_issue_id',$detail->id)
														->where('approver_id',$atasan['id'])
														->first();
									$atasan['step_approval'] = "approval user";
									$atasan['type_approval'] = "approval user custom";
									$list_contact_case_journey[] = $atasan;
									print_approver_case_journey($need_html_output_case_journey,$atasan,$detail_approval);
								} else {
									$detail_approval = null;
								}


							}

						}

						//OPTIONAL APPROVAL USER POSITION
						if(empty($request_management->optional_approval_user_custom_position)) {
							//milih approval_user_custom_job_position tapi belum input, diskip

						}
						else {
							//approval user custom position
							$list_approval_user_custom = explode(",",$request_management->optional_approval_user_custom_position);
							$semua_atasan = cekSemuaAtasan($detail->requestor);

							foreach($list_approval_user_custom as $auc_position_id) {
								//var_dump($auc_position_id);
								$atasan_posisi_ditemukan = false;
								if(!empty($semua_atasan)) {
									foreach($semua_atasan as $atasan) {
											//var_dump($atasan);
											if($atasan['position_id'] == $auc_position_id) {

													$atasan_posisi_ditemukan = true;


													$detail_approval = DB::table('goods_issue_approvals')
																		->where('goods_issue_id',$detail->id)
																		->where('approver_id',$atasan['contact_id'])
																		->first();
														//$approval_name = DB::table('users')->where('id', $status->created_by)->first();
													$atasan['step_approval'] = "approval user";
													$atasan['type_approval'] = "approval user custom";
													$list_contact_case_journey[] = $atasan;
													print_approver_case_journey($need_html_output_case_journey,$atasan,$detail_approval);

											}
									}
									if($atasan_posisi_ditemukan == false) {
										//tidak ditemukan
										//cari ke atasnya satu

										$auc_position_level = DB::table('position')
															->where('id', $contact->position ?? null)
															->value('level');
										if($auc_position_level > 0) {
											foreach($semua_atasan as $atasan) {

												if($atasan['position_level'] >= $auc_position_level) {
													//ketemu satu yang diatasnya atau sama levelnya

													$detail_approval = DB::table('goods_issue_approvals')
																		->where('goods_issue_id',$detail->id)
																		->where('approver_id',$atasan['contact_id'])
																		->first();
													$atasan['step_approval'] = "approval user";
													$atasan['type_approval'] = "approval user custom";
													$list_contact_case_journey[] = $atasan;
													print_approver_case_journey($need_html_output_case_journey,$atasan,$detail_approval);
													break;
												}
											}
										}
									}
								} else {
									//atasan ga ada skip
								}


							}
						}

                        //SIAPKAN SELECTED CONTACT EMPLOYEE UNTUK STEP APPROVAL BERIKUTNYA
						$assign_type_list = explode(",",$request_management->assignment_type);
						$assign_list = explode(",",$request_management->assignment_tier);
                        if($request_management->approval_support_type == "approval_support_custom") {
                            $jumlah_atasan_flow_normal = 0;
                            $id_orang_yg_approve_flow_normal = [];
							if(empty($request_management->approval_support_custom)) {
								//milih approval_support_custom tapi belum input, diskip

							}
							else {
								//approval_support_custom

                                // $max_user_ditemukan = false;
                                foreach($semua_atasan as $atasan) {
                                    $jumlah_atasan_flow_normal += 1;
                                    $id_orang_yg_approve_flow_normal[] = $atasan['contact_id'];
                                    if($atasan['position_id'] == $request_management->max_user_superordinate) {
                                        // $max_user_ditemukan = true;
                                        break;
                                    }
                                }
								$list_approval_support_custom = explode(",",$request_management->approval_support_custom);
                                // dd([$request_management, $semua_atasan, $list_approval_support_custom]);

								for($i=0;$i<count($list_approval_support_custom);$i++) {
									$contact = DB::table('contact')
													->where('contact.status', '=', 'Active')
													->whereNull('contact.deleted_at')
													->where('contact.job_title',$list_approval_support_custom[$i])->first();
									if($contact) {
										$atasan = ['contact_id'=>$contact->id,'id'=>$contact->id,'name'=>$contact->name];
									} else {
										$need_check_jobtitle = $list_approval_support_custom[$i];
										$atasan = getMinimalSatuLevelAtasan($need_check_jobtitle);
									}
									//var_dump($atasan);die;
									//ini bukan atasan tapi biar mudah tinggal copy dari yg diatas

									//echo $approve_support_agent_id."<-approve_support_agent_id";
                                    $cek_multiple = 0;
                                    // dd([$atasan['id'], $id_orang_yg_approve_flow_normal]);
                                    if (in_array($atasan['id'], $id_orang_yg_approve_flow_normal)) {
                                        $cek_multiple = 1;
                                    }
									if(!empty($atasan['id'])) { //kalau kosong diskip ke loop berikutnya
										$detail_approval = DB::table('goods_issue_approvals')
															->where('goods_issue_id',$detail->id)
															->where('approver_id',$atasan['id'])
															->get();
                                                            // dd([$cek_multiple, $detail_approval]);
										//tambahkan ke list
										$atasan['step_approval'] = "approval support";
										$atasan['type_approval'] = "approval support custom";
										$list_contact_case_journey[] = $atasan;
                                        if ($detail_approval->isEmpty()) {
                                            $t_ap = DB::table('goods_issue_approvals')
                                            ->where('goods_issue_id',$detail->id)
                                            ->where('approver_id',$atasan['id'])
                                            ->first();
                                        }
                                        if (empty($detail_approval[$cek_multiple]) && $cek_multiple == 1 && !empty($detail_approval[0])) {
                                            $detail_approval[$cek_multiple] = $detail_approval[0];
                                            $detail_approval[$cek_multiple]->status = null;
                                            $t_ap = $detail_approval[$cek_multiple];
                                        }
                                        if (empty($t_ap)) {
                                            $t_ap = DB::table('goods_issue_approvals')
                                            ->where('goods_issue_id',$detail->id)
                                            ->where('approver_id',$atasan['id'])
                                            ->first();
                                        }
										print_approver_case_journey($need_html_output_case_journey,$atasan,$t_ap);
                                        // dd($detail_approval);
									} else {
										//atasan tidak ditemukan
										$detail_approval = null;
									}

								}
							}

						}



					}


		$list_contact_not_unique = [];
		//var_dump($list_contact_case_journey);
		$list_contact = [];
		if(!empty($list_contact_case_journey)) {
			foreach($list_contact_case_journey as $l) {
				if(!empty($l['id'])) {
					$contact = DB::table('contact')->select('id','name','email')->where('id',$l['id'])->first();
				}
				if(!empty($l['contact_id'])) {
					$contact = DB::table('contact')->select('id','name','email')->where('id',$l['contact_id'])->first();
				}
				if(!empty($contact)) {
					if($include_self == "include self") {
						if(!empty($l['step_approval'])) {
							$contact->step_approval = $l['step_approval'] ?? "";
							$contact->type_approval = $l['type_approval'] ?? "";
						}
						$list_contact[$contact->id] = $contact;
						$list_contact_not_unique[] = $contact;
					} else {
						if(Auth::user()->person != $contact->id) {
							if(!empty($l['step_approval'])) {
								$contact->step_approval = $l['step_approval'] ?? "";
								$contact->type_approval = $l['type_approval'] ?? "";
							}
							$list_contact[$contact->id] = $contact;
							$list_contact_not_unique[] = $contact;
						}
					}
				}
			}
		}
		if($include_request_management_notif == "include_request_management_notif") {
			if(!empty($request_management->notif)) {
				$contacts = DB::table('contact')->whereIn('id',explode(",",$request_management->notif))->get()->toArray();
				foreach($contacts as $contact) {
					if(!empty($contact)) {
						if((!empty(Auth::user())) && (Auth::user()->person != $contact->id)) {
							$list_contact[$contact->id] = $contact;
						}
					}
				}
			}
			$contact = DB::table('contact')->select('id','name','email')->where('id',$detail->requestor)->first();
			if(!empty($contact->id)) {
				$list_contact[$contact->id] = $contact;
			}
		}

		if($need_list_contact_not_unique == "need_list_contact_not_unique"){
			//list_contact_not_unique menyediakan statusnya juga apakah sudah approve atau belum
			if(!empty($list_contact_not_unique)) {

				$array_contacts = [];
				foreach($list_contact_not_unique as $contact) {
					$array_contacts[] = $contact->id;
				}
				//menghitung banyakny contact di casejourney
				// https://www.php.net/manual/en/function.array-count-values.php
				$count_contact_in_casejourney = array_count_values($array_contacts);

				//get list approver yang sudah approve
				$ticket_approved_list = DB::table('goods_issue_approvals')
						->selectRaw('approver_id')
						->where('goods_issue_id',$detail->id)
						->whereIn('approver_id',$array_contacts)
						// ->groupBy('approval_id')
						->get();
				$approver_has_approved_list = [];
				foreach($ticket_approved_list as $ticket_approved) {
					$approver_has_approved_list[] = $ticket_approved->approver_id;
				}

				foreach($list_contact_not_unique as $key => $contact) {

					$index_approver = array_search($contact->id, $approver_has_approved_list);
					if($index_approver === false) {
						$has_approved = false;
					} else {
						unset($approver_has_approved_list[$index_approver]);
						$has_approved = true;
					}

					$list_contact_not_unique[$key]->has_approved = $has_approved;
				}

			}

			// dd($list_contact_not_unique);
			return $list_contact_not_unique;
		}

		// dd($list_contact);


		return $list_contact;
	}
}
if(!function_exists('set_ref_id')) {
	function set_ref_id($type, $id)
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
			case 'goods_issue':
				// $ref_id = env('PREFIX_PROBLEM_REQUEST')."-".str_pad($id,env('TICKET_PADDING'),"0",STR_PAD_LEFT);
				$ref_id = "GI-".str_pad($id,8,"0",STR_PAD_LEFT);
				break;
			case 'goods_receive':
				// $ref_id = env('PREFIX_PROBLEM_REQUEST')."-".str_pad($id,env('TICKET_PADDING'),"0",STR_PAD_LEFT);
				$ref_id = "GR-".str_pad($id,8,"0",STR_PAD_LEFT);
				break;
            default:
                $ref_id = "DFT-".str_pad($id,8,"0",STR_PAD_LEFT);
                break;
		}

		return $ref_id;
	}
}

if (!function_exists('getMinimalSatuLevelAtasan')) {
	function getMinimalSatuLevelAtasan($job_title_id = -100){
		$semua_atasan = cekSemuaAtasan($job_title_id,"mode_job_title_id");
		if(!empty($semua_atasan)) {
			foreach($semua_atasan as $atasan) {
				return $atasan;
			}
		} else {
			return FALSE;
		}
	}
}

if (! function_exists('cekSemuaAtasan')) {
	function cekSemuaAtasan($requester,$mode = "requester") {
		$atasan = [];
		if ($mode == "requester") {
			//parameter pertama requester
			$contact = DB::table('contact')->where('id',$requester)->first();
			$atasan = [];
			if(empty($contact->job_title)) {
				return false;
			}
			$job_title_id = $contact->job_title;
		} else {
			//parameter pertama adalah jobtitle
			$job_title_id = $requester;
		}

		$job_title = DB::table('job_title')->where('id',$job_title_id)->first();
		if(empty($job_title)) {
			return false;
		}

		$parent_job_title_id = $job_title->parent;
		while($parent_job_title_id) {
			$job_title = DB::table('job_title')->where('id',$parent_job_title_id)->first();
			if($job_title) {
				//job title harus ada juga
				$contact = DB::table('contact')
									->where('contact.status', '=', 'Active')
									->whereNull('contact.deleted_at')
									->where('job_title',$job_title->id)->first();

				$position_level = DB::table('position')
									->where('id', $contact->position ?? null)
									->value('level');


				if($contact) {
					//dimasukan jika contakny tersedia, kalo tidak, maka posisi tidak terdeteksi
					$atasan[] = [
						'job_title_id'=>$job_title->id,
						'position_id'=>$contact->position,
						'position_level'=>$position_level,
						'contact_id'=>$contact->id,
						'name'=>$contact->name,
						'id'=>$contact->id,
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
}
if (! function_exists('generateRandomString')) {
	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}
if (! function_exists('getRequestManagement')) {
	function getRequestManagement($service_id,$contact) {
		//cek lokasi dan company
		//yang cocok di request management
		//1. cek all location
		$request_management = DB::table('request_management')
								->select('request_management.*')
								->join('request_management_location AS rml', 'rml.request_management', '=', 'request_management.id')
								->where('rml.type','All Location')
								->where('request_name',$service_id)->first();
		if(empty($request_management)) {
			//2. cek company
			$request_management = DB::table('request_management')
									->select('request_management.*')
									->join('request_management_location AS rml', 'rml.request_management', '=', 'request_management.id')
									->where('rml.type','Company')
									->where('rml.company',$contact->company)
									->where('request_name',$service_id)->first();
			if(empty($request_management)) {
				////3. cek location - company
				$request_management = DB::table('request_management')
										->select('request_management.*')
										->join('request_management_location AS rml', 'rml.request_management', '=', 'request_management.id')
										->where('rml.type','Location Company')
										->where('rml.company',$contact->company)
										->where('rml.location',$contact->location)
										->where('request_management.request_name',$service_id)->first();

			}

		}
		return $request_management;
	}
}


if (! function_exists('checkDueDate')) {
	//fungsi ini jadi multifungsi
	//cek duedate, cek escalatian date, cek sisa sla dan sla yang telah digunakan
	//kemudian cek duedate setelah direopen dimana sebelumnya sla sempat distop/dipaused
	//supaya sinkron masing2 fungsi di atas, tidak perlu sinkronisasi antar fungsi
	function checkDueDate($ticket_id,$start_date,$mode="due date",$target_resolution_time = "",$target_resolution_unit="",$pause_date_time = "", $contact = null) {
		$target_resolution_unit = strtolower($target_resolution_unit);
		date_default_timezone_set('Asia/Jakarta');
		$sisa = 0;
		if(empty($start_date) || empty($ticket_id)) {
			return "";
		}

		$due_date = "";
		//fitur ini untuk cek AUTOESCALATION time nya kapan
		//dan juga sebagai berakirnya waktu pengerjaan

		// cek coverage windows, holiday,
		$ticket = DB::table('ticket')->where('id',$ticket_id)->first();
		$request_management = DB::table('request_management')->where('id',$ticket->request_management)->first();
		$sla_delivery = DB::table('sla')->where('id',$request_management->SLA_delivery)->first();

		//var_dump($sla_delivery);

		//NANTI DIFIX LAGI COVERAGE WINDOWS MANA YANG DIPAKAI
		$coverage_windows = DB::table('coverage_windows')->first();

		if(empty($contact)) {
			$contact = DB::table('contact')->where('id',$ticket->agent_id)->first();
		}
		if($contact) {
			$coverage_windows_contact = DB::table('coverage_windows')->where('id',$contact->coverage_windows)->first();
			if($coverage_windows_contact) {
				$coverage_windows = $coverage_windows_contact;
			}
		}
        // dd($ticket);

        DB::table('ticket')
			->where('id', $ticket->id)
			->update([	'coverage_windows'=>$coverage_windows->id,]);

		if($mode=="due date") {
			//perhitungan dari masa sekarang ke masa depan (due date)
			//echo "DUE";
			//dd($sla_delivery);
			$target_resolution_time = $sla_delivery->target_resolution_time;
			$target_resolution_unit = $sla_delivery->target_resolution_unit;

		}
		if($mode=="checkRemainingSLA_SLA_is_paused_or_stopped") {
			//kalau mode ini berarti fungsi dipanggil oleh checkRemainingSLA_SLA_is_paused_or_stopped
			//perhitungan dari masa lalu sampai masa sekarang

			$target_resolution_time = $sla_delivery->target_resolution_time;
			$target_resolution_unit = $sla_delivery->target_resolution_unit;

			$start_date = $ticket->ticket_open_time;

			//jika sebelumnya pernah dipause
			//dan sekarang dipause lagi maka ambil dari remaining_SLA (sisa SLA)
			if(!empty($ticket->continue_at)
				&& !empty($ticket->remaining_SLA)
				&& !empty($ticket->remaining_SLA_unit)) {
					//echo "MASUK";
					//die;
				//prioritas dari sisa
				$start_date = $ticket->continue_at;
				$target_resolution_time = $ticket->remaining_SLA;
				$target_resolution_unit = $ticket->remaining_SLA_unit;
			}

		}

		if($mode=="SLA continue") {
			//SLA dilanjut bisa saat ticket convertnya direject
			//atau ticket convert to problem dari incident diapprove
			//atau ticket sudah diresolved (SLA stopped) kemudian direopen kembali

			//perhitungan dari masa sekarang ke masa depan

			$target_resolution_time = $sla_delivery->target_resolution_time;
			$target_resolution_unit = $sla_delivery->target_resolution_unit;

			//$start_date sudah ditentukan pas pemanggilan fungsi
			//yaitu sekarang
			//ini kondisi utama sebetulnya, penentuan di atas untuk penjagaan saja
			//karena sudah pasti harusnya ada
			if( !empty($ticket->remaining_SLA)
				&& !empty($ticket->remaining_SLA_unit)) {
				//ambil dari sisa SLA
				$target_resolution_time = $ticket->remaining_SLA;
				$target_resolution_unit = $ticket->remaining_SLA_unit;
			}
		}


		if(is_null($target_resolution_time) || is_null($target_resolution_unit)) {
			//kalau null berarti tidak ada maka hasilnya juga null
			//kalau "" masih bisa masuk
			return null;
		}
		if(!is_numeric($target_resolution_time)) {
			$target_resolution_time = 0;
		}

		if($target_resolution_unit  == "days") {
			//khusus days beda hitungannya
			//harus hitung hari kerja aktif
			$sisa = $target_resolution_time;
		} else if($target_resolution_unit  == "hours") {
			$sisa = $target_resolution_time * 60;
		} else if($target_resolution_unit  == "minutes") {
			$sisa = $target_resolution_time;
		}

		//untuk cek durasi
		//mesti dibelokin maka
		//modifikasi sla saja
		if($mode == "checkDurationActive") {
			//dengan menyeting angka besar nantinya
			//total_SLA dikurangi sisa maka didapat durasi pemakaian

			$target_resolution_time = 1000000000;
			$sisa = $target_resolution_time;
			$target_resolution_unit  = "minutes";
			//return $total_SLA;
		}


		$total_SLA = $sisa;


		$current_loop_date = $start_date;//mulai dari start date

		$coverage_hours = $coverage_windows->coverage_hours;
		$list_cov = explode(",",$coverage_hours);
		$ar_cov = [];

		$list_cov = array_map('trim', $list_cov);

		// dd("ticket", $ticket, "req_management", $request_management, "coverage_windows", $coverage_windows, $coverage_hours, $list_cov);

		$new_list_cov = [];
        // dd($list_cov);
		foreach($list_cov as $cov) {
			$s = explode(" - ",$cov);
			$a = explode(":",$s[0]);
			//echo "<pre>";
			//var_dump($a);
			//echo "</pre>";
			if(!empty($a[1])) {
				$new_key = $a[0].":".sprintf("%02d", $a[1]).":".sprintf("%02d", $a[2]);
				$new_list_cov[$new_key] = $cov;
			}
		}
		//dd($new_list_cov);
		//die;

		ksort($new_list_cov);
		$list_cov = $new_list_cov;

		//echo "<pre>";
		//var_dump($list_cov);
		//var_dump($new_list_cov);
		//echo "</pre>";
		//die;
		foreach($list_cov as $cov) {

			if(empty(trim($cov))) {
				//skip karena kosong (string " ")

			} else if (str_contains($cov, '-')) {
				//range
				$d = explode(" - ",$cov);
				$e_start = explode(":",$d[0]);//contoh : 3:00:30
				$e_end = explode(":",$d[1]);//contoh : 4:30

				$day_number = $e_start[0];
				$start_hour = $e_start[1];
				$start_minute = $e_start[2];
				$end_hour = $e_end[0];
				$end_minute = $e_end[1];
				$duration = ($end_hour * 60 + $end_minute) - ($start_hour * 60 + $start_minute);

				$ar_cov[$day_number][] = ['day'=>$e_start[0],'start_hour'=>$start_hour,'start_minute'=>$start_minute,
													'end_hour'=>$end_hour,'end_minute'=>$end_minute,
													'duration'=> $duration,
								 ];

			} else {
				//range stengah jam saja
				$e_start = explode(":",$cov);
				//var_dump($cov);
				//die;
				$day_number = $e_start[0];
				$start_hour = $e_start[1];
				$start_minute = $e_start[2];

				$end_total = ($start_hour * 60 + $start_minute) + 30;
				$end_hour = floor($end_total / 60);
				$end_minute = $end_total % 60;
				$ar_cov[$day_number][] = ['day'=>$day_number,'start_hour'=>$start_hour,'start_minute'=>$start_minute,
												'end_hour'=>$end_hour,'end_minute'=>$end_minute,
												'duration'=> 30,
								];
			}

		}
		//-check
		$first_day = true;
		$loop_while = 0;
		//dd($sisa);
		while($sisa > 0 && $loop_while < 200) {
			$loop_while++;
			//echo "<br/>SISA".$sisa."E";
			//echo $current_loop_date;

			//die;

			$a = explode(" ",$current_loop_date);
			$date_part = $a[0];

			//if(empty($a[1])) {
				//die;
			//}
			$hour_minute_part = $a[1];

			$c = explode(":",$hour_minute_part);
			$hour_part = $c[0];
			$minute_part = $c[1];

			//$holiday = DB::table('holiday')->where('date',$a[0])->first();

			$holiday = DB::table('holiday')
						->select('holiday.*')
						->join('lnkemployeetoholiday', 'lnkemployeetoholiday.holiday_id', '=', 'holiday.id')
						->where('lnkemployeetoholiday.employee_id',$ticket->agent_id)
						->where('date',$a[0])->first();

			//check holiday, kalau holiday berarti diskip
			if($holiday) {
				//skip loop


			} else {
				$current_day_number = date('w', strtotime($current_loop_date)) + 1;//get to number of the day (0 to 6, 0 being sunday, and 6 being saturday)
				//mesti ditambah 1 karena beda standar dgn di kode di php 0 => minggu, di code 1 => minggu

				if($target_resolution_unit  == "days") {
					if($first_day) {
						$first_day = false;
						//hari submit tidak dihitung, mulai dihitung hari selanjutnya
					} else {
						//echo "days";
						//patokan hari aktif kerja, tidak ada patokan jam atau menit

						//ada di loop sudah pasti
						foreach($ar_cov as $day_number => $list_cov) {
							//harinya harus sama dgn tanggal yang sedang dicek
							if($day_number == $current_day_number) {

								//START HITUNG REMAINING SLA
								if(!empty($pause_date_time)) {
									$p_arr = explode(" ",$pause_date_time);
									$pause_date = $p_arr[0];
									if($pause_date <= $date_part) {
										//echo "kesini";

										//berhenti di hari pause
										$have_been_used_SLA = $total_SLA - $sisa;

										//hitung terpakai berapa menit/jam
										return ['state'=>'','remaining_SLA' => $sisa,'have_been_used_SLA'=>$have_been_used_SLA,'total_SLA'=>$total_SLA,'remaining_SLA_unit'=>'days'];
									}
								}
								//END HITUNG REMAINING SLA

								//kalau ada hari yang sama berarti hari aktif kerja
								//echo "minus";
								$sisa--;

								if($sisa <= 0) {
									//jam akhir mesti dicek dulu di tiap range
									$jam_paling_akhir = [];
									foreach($list_cov as $coverage) {
										if(empty($jam_paling_akhir)) {
											$jam_paling_akhir = ['end_hour'  =>$coverage['end_hour'],
																 'end_minute'=>$coverage['end_minute']];
										} else {
											if($coverage['end_hour']>$jam_paling_akhir['end_hour']) {
												//ada time yang lebih akhir, maka direplace
												$jam_paling_akhir = ['end_hour'  =>$coverage['end_hour'],
																	 'end_minute'=>$coverage['end_minute']];
											}

										}
									}
									//$next_day_date = date('Y-m-d', strtotime('+1 day', strtotime($current_loop_date)));
									//$b = explode(" ",$next_day_date);
									//$date_part_next_day = $b[0];
									$b = explode(" ",$current_loop_date);
									$date_part_due_date = $b[0];
									$due_date = $date_part_due_date." ".$jam_paling_akhir['end_hour'].":".$jam_paling_akhir['end_minute'].":00";
								}
							}

						}
					}
				}
				else {
					//echo "minute";
					//echo $current_day_number."daynumber";
					//patokan durasi jam menit aktif

					//cek current looping date, berapa jam di hari tsb

					foreach($ar_cov as $day_number => $list_cov) {
						//harinya harus sama dgn tanggal yang sedang dicek
						if($day_number == $current_day_number) {
							//cek di hari pertama berapa jam terpakai
							if($first_day) {
								$first_day = false;
									$type = "";
									for($i=0;$i<count($list_cov);$i++) {

										$coverage = $list_cov[$i];
										$total_start_hour = $coverage['start_hour'] * 60 + $coverage['start_minute'];
										$total_end_hour = $coverage['end_hour'] * 60 + $coverage['end_minute'];
										//echo "loop".$coverage['start_hour'];
										$total_current_hour = $hour_part *60 + $minute_part;

										if($i == 0) {
											//loop pertama
											if($total_current_hour < $total_start_hour) {
												//karena posisi assign sebelum jam kerja maka
												//dihitung full durasi aktif kerja di hari tsb
												$type = "full day duration";
											}


										}
										if ($total_start_hour <= $total_current_hour &&
																				$total_current_hour <= $total_end_hour) {
											//ditengah2 working hour

											$type = "middle working hour";
											$selected_loop = $i;
										}

										if($i == (count($list_cov)-1)) {
											//echo "JJJ".$total_current_hour.'{}'.$total_end_hour;
											//loop terakhir
											if($total_end_hour < $total_current_hour) {
												//karena posisi assign setelah jam kerja maka
												//hari tsb tidak dihitung (diskip) lanjut loop berikutnya
												$type = "after working hour, skip";
											}
										}
										//echo "LLL";
										if(!empty($previous_total_end_hour) &&
											$previous_total_end_hour < $total_current_hour
																&& $total_current_hour < $total_start_hour) {
											$type = "between two working hour";
											$selected_loop = $i;
										}

										$previous_total_start_hour = $total_start_hour;
										$previous_total_end_hour = $total_end_hour;

									}
									//echo "TYPE".$type;
									if($type == "middle working hour") {
											//echo "selected_loop$selected_loop|";
											$loop = 0;
											$mode = "";
											$loop_lebih_besar = false;
											foreach($list_cov as $coverage) {
												if($loop == $selected_loop) {
													//var_dump($coverage);
													//loop pertama di tengah working hour

													$total_start_hour = $coverage['start_hour'] * 60 + $coverage['start_minute'];
													$total_end_hour = $coverage['end_hour'] * 60 + $coverage['end_minute'];

													$total_current_hour = $hour_part *60 + $minute_part;

													if(($total_end_hour - $total_current_hour) < $sisa) {
														$duration = $total_end_hour - $total_current_hour;
														$mode = "belum habis. for next loop";

														if($sisa > 0) {//lajut loop jika belum 0 atau minus

															//START HITUNG REMAINING SLA
															//if(!empty($pause_date_time)) {
																//if($pause_date_time <= ($date_part." ".$coverage['end_hour'].":".$coverage['end_minute'].":00")) {
																	////berhenti di range ini
																	//$p_arr = explode(" ",$pause_date_time);
																	//$pause_date = $p_arr[0];
																	//$pause_time = $p_arr[1];

																	//$p_time_arr = explode(":",$pause_time);

																	//$pause_hour = $p_time_arr[0];
																	//$pause_minute = $p_time_arr[1];

																	//$total_pause_time = $pause_hour * 60 +$pause_minute;
																	//$durasi_penggunaan = $total_pause_time - $total_start_hour;
																	//$remaining_SLA = $sisa - $durasi_penggunaan;
																	//$have_been_used_SLA = $total_SLA - $remaining_SLA;

																	////hitung terpakai berapa menit/jam
																//}
															//}
															//START HITUNG REMAINING SLA
															$calculateResult = calculateRemainingSLA($start_date,$ticket,$date_part,$coverage,$pause_date_time,$total_SLA,$sisa);
															if(!empty($calculateResult)) {
																return $calculateResult;
															}
															//END HITUNG REMAINING SLA

															//echo "<br/>sisa".$sisa."kurangi".$duration."<br/>";
															$new_sisa = $sisa - $duration;
															if($new_sisa <=0) {
																//berarti habis di range hour ini
																//tentukan jam akhir
																//echo "masuk".$new_sisa;
																//var_dump($coverage);
																//echo $sisa."|";
																//$total_end_time = $coverage['start_hour'] * 60 + $sisa;

																$total_end_time = ($hour_part * 60 + $minute_part) + $sisa;

																//echo $coverage['start_hour']."XXX";
																//echo $total_end_time."YYY";
																$end_due_date_hour = floor($total_end_time / 60);
																//echo $end_due_date_hour."MMM";
																$end_due_date_minute = $total_end_time % 60;
																//echo $end_due_date_minute."TTT";
																//dapet due datenya
																$due_date = $date_part." ".$end_due_date_hour.":".twoDigit($end_due_date_minute).":00";
																$sisa = $new_sisa;
															} else {
																//resolution hour belum habis, lanjut loop berikutnya
																$sisa = $sisa - $duration;
															}
														}


													}
													else if(($total_end_hour - $total_current_hour) == $sisa) {
														//pas habis
														$duration = $total_end_hour - $total_current_hour;
														$mode = "pas habis";


														if($sisa > 0) {//lajut loop jika belum 0 atau minus

															//START HITUNG REMAINING SLA
															$calculateResult = calculateRemainingSLA($start_date,$ticket,$date_part,$coverage,$pause_date_time,$total_SLA,$sisa);
															if(!empty($calculateResult)) {
																return $calculateResult;
															}
															//END HITUNG REMAINING SLA

															//echo "<br/>sisa".$sisa."kurangi".$duration."<br/>";
															$new_sisa = $sisa - $duration;
															if($new_sisa <=0) {
																//berarti habis di range hour ini
																//tentukan jam akhir
																//echo "masuk".$new_sisa;
																//var_dump($coverage);
																//echo $sisa."|";
																//$total_end_time = $coverage['start_hour'] * 60 + $sisa;
																$total_end_time = ($hour_part * 60 + $minute_part) + $sisa;

																//echo $coverage['start_hour']."XXX";
																//echo $total_end_time."YYY";
																$end_due_date_hour = floor($total_end_time / 60);
																//echo $end_due_date_hour."MMM";
																$end_due_date_minute = $total_end_time % 60;
																//echo $end_due_date_minute."TTT";
																//dapet due datenya
																$due_date = $date_part." ".$end_due_date_hour.":".twoDigit($end_due_date_minute).":00";
																$sisa = $new_sisa;
															} else {
																//resolution hour belum habis, lanjut loop berikutnya
																$sisa = $sisa - $duration;
															}
														}

													}
													else {

														//else artinya
														//($total_end_hour - $total_current_hour) > $sisa
														//di loop ini langsung habis durasinya
														$duration = $sisa;
														$mode = "pas habis di tengah range";

														if($sisa > 0) {//lajut loop jika belum 0 atau minus


															//START HITUNG REMAINING SLA
															$calculateResult = calculateRemainingSLA($start_date,$ticket,$date_part,$coverage,$pause_date_time,$total_SLA,$sisa);
															if(!empty($calculateResult)) {
																return $calculateResult;
															}
															//END HITUNG REMAINING SLA


															//echo "<br/>sisa".$sisa."kurangi".$duration."<br/>";
															$new_sisa = $sisa - $duration;
															if($new_sisa <=0) {
																//berarti habis di range hour ini
																//tentukan jam akhir
																//echo "masuk".$new_sisa;
																//var_dump($coverage);
																//echo $sisa."|";
																//$total_end_time = $coverage['start_hour'] * 60 + $sisa;

																$total_end_time = ($hour_part * 60 + $minute_part) + $sisa;
																//echo $hour_part."SSSS";
																//echo $coverage['start_hour']."XXX";
																//echo $total_end_time."YYY";
																$end_due_date_hour = floor($total_end_time / 60);
																//echo $end_due_date_hour."MMM";
																$end_due_date_minute = $total_end_time % 60;
																//echo $end_due_date_minute."TTT";
																//dapet due datenya
																$due_date = $date_part." ".$end_due_date_hour.":".twoDigit($end_due_date_minute).":00";
																$sisa = $new_sisa;
															} else {
																//resolution hour belum habis, lanjut loop berikutnya
																$sisa = $sisa - $duration;
															}
														}
													}
												}
												else if($loop > $selected_loop) {
													//loop setelah loop pertama (loop pertama di tengah working hour)
													//
													$total_start_hour = $coverage['start_hour'] * 60 + $coverage['start_minute'];
													$total_end_hour = $coverage['end_hour'] * 60 + $coverage['end_minute'];

													$total_current_hour = $hour_part *60 + $minute_part;

													$duration = $coverage['duration'];

													if($sisa > 0) {//lajut loop jika belum 0 atau minus

														//START HITUNG REMAINING SLA
														$calculateResult = calculateRemainingSLA($start_date,$ticket,$date_part,$coverage,$pause_date_time,$total_SLA,$sisa);
														if(!empty($calculateResult)) {
															return $calculateResult;
														}
														//END HITUNG REMAINING SLA


														//echo "<br/>sisa".$sisa."kurangi".$duration."<br/>";
														$new_sisa = $sisa - $duration;
														if($new_sisa <=0) {
															//berarti habis di range hour ini
															//tentukan jam akhir
															//echo "masuk".$new_sisa;
															//var_dump($coverage);
															//echo $sisa."|";
															$total_end_time = $coverage['start_hour'] * 60 + $coverage['start_minute'] + $sisa;

															//echo $coverage['start_hour']."XXX";
															//echo $total_end_time."YYY";
															$end_due_date_hour = floor($total_end_time / 60);
															//echo $end_due_date_hour."MMM";
															$end_due_date_minute = $total_end_time % 60;
															//echo $end_due_date_minute."TTT";
															//dapet due datenya
															$due_date = $date_part." ".$end_due_date_hour.":".twoDigit($end_due_date_minute).":00";
															$sisa = $new_sisa;
														} else {
															//resolution hour belum habis, lanjut loop berikutnya
															$sisa = $sisa - $duration;
														}
													}



													$loop_lebih_besar = true;


												}

												$loop++;
											}
									}

									if($type == "between two working hour") {
											//echo "betweenselected_loop$selected_loop|";
											$loop = 0;
											$mode = "";
											$loop_lebih_besar = false;
											foreach($list_cov as $coverage) {
												if($loop >= $selected_loop) {
													$total_start_hour = $coverage['start_hour'] * 60 + $coverage['start_minute'];
													$total_end_hour = $coverage['end_hour'] * 60 + $coverage['end_minute'];

													$total_current_hour = $hour_part *60 + $minute_part;

													$duration = $coverage['duration'];

													if($sisa > 0) {//lajut loop jika belum 0 atau minus

														//START HITUNG REMAINING SLA
														$calculateResult = calculateRemainingSLA($start_date,$ticket,$date_part,$coverage,$pause_date_time,$total_SLA,$sisa);
														if(!empty($calculateResult)) {
															return $calculateResult;
														}
														//END HITUNG REMAINING SLA


														//echo "<br/>sisa".$sisa."kurangi".$duration."<br/>";
														$new_sisa = $sisa - $duration;
														if($new_sisa <=0) {
															//berarti habis di range hour ini
															//tentukan jam akhir
															//echo "masuk".$new_sisa;
															//var_dump($coverage);
															//echo $sisa."|";
															$total_end_time = $coverage['start_hour'] * 60 + $coverage['start_minute'] + $sisa;

															//echo $coverage['start_hour']."XXX";
															//echo $total_end_time."YYY";
															$end_due_date_hour = floor($total_end_time / 60);
															//echo $end_due_date_hour."MMM";
															$end_due_date_minute = $total_end_time % 60;
															//echo $end_due_date_minute."TTT";
															//dapet due datenya
															$due_date = $date_part." ".$end_due_date_hour.":".twoDigit($end_due_date_minute).":00";
															$sisa = $new_sisa;
														} else {
															//resolution hour belum habis, lanjut loop berikutnya
															$sisa = $sisa - $duration;
														}
													}


												} else if($loop > $selected_loop) {
													$loop_lebih_besar = true;
												}

												$loop++;
											}
									}

									if($type == "after working hour, skip") {
										//no action, just skip

									}
									if($type == "full day duration") {
											foreach($list_cov as $coverage) {

												if($sisa > 0) {//lajut loop jika belum 0 atau minus

													//START HITUNG REMAINING SLA
													$calculateResult = calculateRemainingSLA($start_date,$ticket,$date_part,$coverage,$pause_date_time,$total_SLA,$sisa);
													if(!empty($calculateResult)) {
														return $calculateResult;
													}
													//END HITUNG REMAINING SLA


													//echo "<br/>sisa".$sisa."kurangi".$coverage['duration']."<br/>";
													$new_sisa = $sisa - $coverage['duration'];
													if($new_sisa <=0) {
														//berarti habis di range hour ini
														//tentukan jam akhir
														//echo "masuk".$new_sisa;
														//var_dump($coverage);
														//echo $sisa."|";
														$total_end_time = $coverage['start_hour'] * 60 + $coverage['start_minute'] + $sisa;

														//echo $coverage['start_hour']."XXX";
														//echo $total_end_time."YYY";
														$end_due_date_hour = floor($total_end_time / 60);
														//echo $end_due_date_hour."MMM";
														$end_due_date_minute = $total_end_time % 60;
														//echo $end_due_date_minute."TTT";
														//dapet due datenya
														$due_date = $date_part." ".$end_due_date_hour.":".twoDigit($end_due_date_minute).":00";
														$sisa = $new_sisa;
													} else {
														//resolution hour belum habis, lanjut loop berikutnya
														$sisa = $sisa - $coverage['duration'];
													}
												}
											}
									}
							}
							else {
							//setelah hari pertama masuk sini
							//artinya dihitung dari awal working hour
							//var_dump($list_cov);
								foreach($list_cov as $coverage) {

									if($sisa > 0) {//lajut loop jika belum 0 atau minus

										//START HITUNG REMAINING SLA
										$calculateResult = calculateRemainingSLA($start_date,$ticket,$date_part,$coverage,$pause_date_time,$total_SLA,$sisa);
										if(!empty($calculateResult)) {
											return $calculateResult;
										}
										//END HITUNG REMAINING SLA


										//echo "<br/>sisa".$sisa."kurangi".$coverage['duration']."<br/>";
										$new_sisa = $sisa - $coverage['duration'];
										if($new_sisa <=0) {
											//berarti habis di range hour ini
											//tentukan jam akhir
											//echo "masuk".$new_sisa;
											//var_dump($coverage);
											//echo "_sis_".$sisa."|";
											$total_end_time = $coverage['start_hour'] * 60 + $coverage['start_minute'] + $sisa;

											//echo $coverage['start_hour']."XXX";
											//echo $total_end_time."YYY";
											$end_due_date_hour = floor($total_end_time / 60);
											//echo $end_due_date_hour."MMM";
											$end_due_date_minute = $total_end_time % 60;
											//echo $end_due_date_minute."TTT";
											//dapet due datenya
											$due_date = $date_part." ".$end_due_date_hour.":".twoDigit($end_due_date_minute).":00";
											$sisa = $new_sisa;
										} else {
											//resolution hour belum habis, lanjut loop berikutnya
											$sisa = $sisa - $coverage['duration'];
										}
									}
								}
							}
						}
					}
				}

			}
			//geser ke hari berikutnya
			$current_loop_date = date("Y-m-d H:i:s", strtotime('+1 day', strtotime($current_loop_date)));
		}

		//START REMAINING SLA
		if(!empty($pause_date_time)) {
			//pause date time gak kena di coverage berarti dianggap habis sisanya
			return ['remaining_SLA' => 0,'have_been_used_SLA'=>$total_SLA,'total_SLA'=>$total_SLA];
		}
		//END REMAINING SLA

		if(!empty($due_date)) {
			$due_date = str_replace("24:00:00","23:59:59",$due_date);
		}
		return (empty($due_date)? null: $due_date);

	}
}
if (! function_exists('calculateRemainingSLA')) {
	function calculateRemainingSLA($start_date,$ticket,$date_part,$coverage,$pause_date_time,$total_SLA,$sisa) {
		if(!empty($pause_date_time)) {
			if($pause_date_time <= ($date_part." ".$coverage['start_hour'].":".$coverage['start_minute'].":00")) {
				//echo "kesini";

				//berhenti sebelum coverage hour item
				$have_been_used_SLA = $total_SLA - $sisa;

				//hitung terpakai berapa menit/jam
				return ['state'=>'before','remaining_SLA' => $sisa,'have_been_used_SLA'=>$have_been_used_SLA,'total_SLA'=>$total_SLA,'remaining_SLA_unit'=>'minutes'];
			}
			else if($pause_date_time <= ($date_part." ".$coverage['end_hour'].":".$coverage['end_minute'].":00")) {
				//berhenti di range ini

				$total_start_hour = $coverage['start_hour'] * 60 + $coverage['start_minute'];

				$p_arr = explode(" ",$pause_date_time);
				$pause_date = $p_arr[0];
				$pause_time = $p_arr[1];

				$p_time_arr = explode(":",$pause_time);

				$pause_hour = $p_time_arr[0];
				$pause_minute = $p_time_arr[1];

				$total_pause_time = $pause_hour * 60 + $pause_minute;

				$a_arr = explode(" ",$start_date);//patokan dari satrt_date karena bisa jadi waktu dari awal atau tengah hitungnya
				$assign_date = $a_arr[0];
				$assign_time = $a_arr[1];

				$a_time_arr = explode(":",$assign_time);

				$assign_hour = $a_time_arr[0];
				$assign_minute = $a_time_arr[1];

				$total_assign_time = $assign_hour * 60 + $assign_minute;

				if($assign_date == $date_part) {
					//diconvert di tanggal yg sama dgn saat assign
					if($total_assign_time > $total_start_hour) {
						//durasi dari waktu assign ke waktu pause
						$durasi_penggunaan = $total_pause_time - $total_assign_time;
					} else {
						//durasi dari waktu start hour ke waktu pause
						$durasi_penggunaan = $total_pause_time - $total_start_hour;
					}
				} else {
					$durasi_penggunaan = $total_pause_time - $total_start_hour;
				}
				//var_dump($total_pause_time);
				//var_dump($total_start_hour);
				//var_dump($total_assign_time);
				//var_dump($durasi_penggunaan);
				//die;
				$remaining_SLA = $sisa - $durasi_penggunaan;
				$have_been_used_SLA = $total_SLA - $remaining_SLA;

				//hitung terpakai berapa menit/jam
				return ['state'=>'in the middle range','remaining_SLA' => $remaining_SLA,'have_been_used_SLA'=>$have_been_used_SLA,'total_SLA'=>$total_SLA,'remaining_SLA_unit'=>'minutes'];
			}
		}
		return null;
	}
}
if (! function_exists('checkRemainingSLA_SLA_is_paused_or_stopped')) {
	function checkRemainingSLA_SLA_is_paused_or_stopped($ticket_id,$pause_date_time) {
		$target_resolution_time = "";$target_resolution_unit = "";
		$parent_ticket = DB::table('ticket')->where('id', $ticket_id)->first();
		if(empty($parent_ticket)) {
			return null;
		}
		$start_date = $parent_ticket->ticket_open_time;
		//startdate nanti ditentukan di fungsi checkDueDate, bisa dari ticket_open_time atau dari continue_at
		$mode="checkRemainingSLA_SLA_is_paused_or_stopped";
		return checkDueDate($ticket_id,$start_date,$mode,$target_resolution_time,$target_resolution_unit,$pause_date_time);
	}
}
if (! function_exists('checkDurationActive')) {
	function checkDurationActive($ticket_id,$contact_id,$start_date,$end_date) {
		$contact = DB::table('contact')->where('id',$contact_id)->first();
		$mode="checkDurationActive";//resolution time dan unit diset di fungsi helper checkDueDate
		$target_resolution_time = "";$target_resolution_unit = "";
		$retval = checkDueDate($ticket_id,$start_date,$mode,$target_resolution_time,$target_resolution_unit,$end_date,$contact);
		//var_dump($retval);
		//die;
		$duration = $retval['have_been_used_SLA'] ?? 0;
		return $duration;
	}
}

if (! function_exists('twoDigit')) {
	function twoDigit($num) {
		$num_padded = sprintf("%02d", $num);
		return $num_padded; // returns 04
	}
}

if (! function_exists('checkEscalationDate')) {
	function checkEscalationDate($ticket_id,$start_date,$target_resolution_time,$target_resolution_unit) {
		return checkDueDate($ticket_id,$start_date,"escalation date",$target_resolution_time,$target_resolution_unit);
	}
}

if (! function_exists('ticketNumber')) {
	function ticketNumber($id) {
		//return "I-00000".$id;
        $ticket = \DB::table('ticket')->where('id',$id)->first();
        //return "I-".str_pad($id,env('TICKET_PADDING'),"0", STR_PAD_LEFT);
        return $ticket?$ticket->ref:"Not Found";
	}
}
if (! function_exists('goodsIssueNumber')) {
	function goodsIssueNumber($id) {
		//return "I-00000".$id;
        $goods_issues = DB::table('goods_issues')->where('id',$id)->first();
        //return "I-".str_pad($id,env('TICKET_PADDING'),"0", STR_PAD_LEFT);
        return $goods_issues->code;
	}
}
if (! function_exists('goodsReceiveNumber')) {
	function goodsReceiveNumber($id) {
		//return "I-00000".$id;
        $goods_receive = DB::table('goods_receives')->where('id',$id)->first();
        //return "I-".str_pad($id,env('TICKET_PADDING'),"0", STR_PAD_LEFT);
        return $goods_receive->code;
	}
}
if (! function_exists('acronym')) {
	function acronym($name) {
		$words = explode(" ", $name);
		$acronym = "";

		foreach ($words as $w) {
			if(!empty($w[0])) {
				$acronym .= $w[0];
			}
		}
		return $acronym;
	}
}
if (! function_exists('directHelpdesk')) {
    function directHelpdesk($url) {
		$baseurl_helpdesk = env('GRP_HELPDESK_URL');
		//Helpdesk
		//create token
		$token = generateRandomString(60);
		DB::table('users')->where('id',Auth::user()->id)->update(['autologin_token'=>$token,'autologin_token_expire'=>date("Y-m-d H:i:s", strtotime("+1 hours"))]);
		//echo $url."/onetime_login_token/".$token;
		//die;
		return $baseurl_helpdesk."/onetime_login_token/".$token."?url=".$url;
	}
}
if (! function_exists('getLink')) {
	function getLink($type,$id) {
		if($type == 'goods_issue') {
			$ticket = DB::table('goods_issues')->where('id',$id)->first();
		} else if($type == 'goods_receives') {
			$ticket = DB::table('goods_receives')->where('id',$id)->first();
		} else {
			$ticket = DB::table('ticket')->where('id',$id)->first();
			if(empty($ticket->token)) {
				return URL('/');
			}
		}

		$ticket = DB::table('ticket')->where('id',$id)->first();
		if(empty($ticket->token)) {
			return URL('/');
		}
		$ticket = DB::table('ticket')->where('id',$id)->first();
		if(empty($ticket)) {
			return URL('/');
		}
		else if($type == "assign_ticket" || (($ticket->requester != Auth::user()->person) && ($ticket->agent_id == Auth::user()->person))) {

			if(empty($ticket->finalclass)) {
				return URL('/');
				//return directHelpdesk('/edit/'.$ticket->token.'/ticket');
			}
			else if($ticket->finalclass == 'incident_management') {
				//return URL('/').'/ticket-monitoring/'.$ticket->token;
				//return URL('/').'/edit/'.$ticket->token.'/incident_request';
				return directHelpdesk('/edit/'.$ticket->token.'/incident_request');
			}
			else if($ticket->finalclass == 'service_request') {
				//return URL('/').'/ticket-monitoring/'.$ticket->token;
				//return URL('/').'/edit/'.$ticket->token.'/service_request';
				return directHelpdesk('/edit/'.$ticket->token.'/service_request');
			}
			else if($ticket->finalclass == 'problem') {
				//return URL('/').'/ticket-monitoring/'.$ticket->token;
				//return URL('/').'/edit/'.$ticket->token.'/problem_request';
				return directHelpdesk('/edit/'.$ticket->token.'/problem_request');
			}
			else {
				return URL('/');
			}
		} else if($type == "approve_request") {
			return URL('/').'/approve-request/'.$ticket->token;
		} else if($type == "ticket_monitoring") {
			return URL('/').'/ticket-monitoring/'.$ticket->token;
		} else if($type == 'goods_issue') {
			return URL('/').'/goods_issue/'.$ticket->id;
		} else if($type == 'goods_receives') {
			return URL('/').'/goods_receive/'.$ticket->id;
		} else if($type == "ticket_comment") {
			return URL('/').'/ticket-monitoring/'.$ticket->token;
		} else {
			return URL('/');
		}
		return URL('/');
	}
}

if (! function_exists('is_image')) {
    function is_image($filename){
		$supported_image = array(
			'gif',
			'jpg',
			'jpeg',
			'png'
		);


		$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION)); // Using strtolower to overcome case sensitive
		if (in_array($ext, $supported_image)) {
			//echo "it's image";
			return TRUE;
		} else {
			return FALSE;
			//echo 'not image';
		}
	}
}
if (! function_exists('nicetime')) {
	function nicetime($ptime)
	{
		$ptime = strtotime($ptime);
		$etime = time() - $ptime;

		if ($etime < 1)
		{
			return '0 seconds';
		}

		$a = array( 365 * 24 * 60 * 60  =>  'year',
					 30 * 24 * 60 * 60  =>  'month',
						  24 * 60 * 60  =>  'day',
							   60 * 60  =>  'hour',
									60  =>  'minute',
									 1  =>  'second'
					);
		$a_plural = array( 'year'   => 'years',
						   'month'  => 'months',
						   'day'    => 'days',
						   'hour'   => 'hours',
						   'minute' => 'minutes',
						   'second' => 'seconds'
					);

		foreach ($a as $secs => $str)
		{
			$d = $etime / $secs;
			if ($d >= 1)
			{
				$r = round($d);
				return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
			}
		}
	}
}
if (!function_exists('get_cc')) {
	function get_cc($ticket_id){

		$ticket = DB::table('ticket')->where('id', $ticket_id)->first();


		$request_management = DB::table('request_management')->where('request_name', $ticket->service_id)->first();
		//var_dump($request_management);
		//APPROVAL USER
		if(empty($request_management->approval_user_custom)) {
			if(empty($request_management->max_user_superordinate)) {
				//dua duanya kosong
				//diskip

			}
			else {
				//max_user_superordinate
				$semua_atasan = cekSemuaAtasan($ticket->requester);
				foreach($semua_atasan as $atasan) {
					$contact = DB::table('contact')->where('id', $atasan['contact_id'])->first();
					if(!empty($contact->email)) {
						$list_cc[] = $contact->email;
					}

					if($atasan['position_id'] == $request_management->max_user_superordinate) {
						//UDAH MAKSIMUM
						break;
					}
				}
			}
		}
		else {
			//approval_user_custom

			$list_approval_user_custom = explode(",",$request_management->approval_user_custom);
			$semua_atasan = cekSemuaAtasan($ticket->requester);

			foreach($list_approval_user_custom as $auc_position_id) {
				//var_dump($auc_position_id);
				if(!empty($semua_atasan)) {
				foreach($semua_atasan as $atasan) {
						//var_dump($atasan);
						if($atasan['position_id'] == $auc_position_id) {

							$contact = DB::table('contact')->where('id', $atasan['contact_id'])->first();
							if(!empty($contact->email)) {
								$list_cc[] = $contact->email;
							}

						}
				}
				}
			}
			//////////////////////
		}
		//APPROVAL SUPPORT

		if(empty($request_management->approval_support_custom)) {
			if(empty($request_management->max_support_approval)) {
				//dua duanya kosong
				//diskip
			}
			else {
				//max_support_approval
				$assign_type_list = explode(",",$request_management->assignment_type);

				$assign_list = explode(",",$request_management->assignment_tier);

				if($assign_type_list[0] == 4) {

					//manual
					$employee_id_selected = $assign_list[0] ?? 0;
					$employee = DB::table('contact')->where('id',$employee_id_selected)->first();
					//dd($employee);
					if(!empty($employee->job_title)) {
						//$job_title_id = $employee->job_title;
						$selected_contact_employee = $employee;
					}
				} else {
					//roundrobin loadbalance random
					$team_id = $assign_list[0] ?? 0;

					$list_employee_team = DB::table('lnkemployeetoteam')->where('team_id',$team_id)->get();
					$job_title = null;
					$selected_contact_employee;
					foreach($list_employee_team as $et) {
						$employee = DB::table('contact')->where('id',$et->employee_id)->first();
						if(!empty($employee->job_title)) {
							//$job_title_id = $employee->job_title;
							$selected_contact_employee = $employee;
						}
					}
				}
				//dd($selected_contact_employee);
				$semua_atasan = cekSemuaAtasan($selected_contact_employee->id ?? 0);
				if(!empty($semua_atasan)) {
				foreach($semua_atasan as $atasan) {
					//var_dump($atasan);
					//echo "SSS";

					$contact = DB::table('contact')->where('id', $atasan['contact_id'])->first();
					if(!empty($contact->email)) {
						$list_cc[] = $contact->email;
					}


					if($atasan['job_title_id'] == $request_management->max_support_approval) {
						break;
					}

				}


			}
				}
		}
		else {
			//approval_support_custom
			$list_approval_support_custom = explode(",",$request_management->approval_support_custom);

			for($i=0;$i<count($list_approval_support_custom);$i++) {
				$contact = DB::table('contact')->where('contact.job_title',$list_approval_support_custom[$i])->first();
				if(!empty($contact)) {
					$atasan = ['contact_id'=>$contact->id,'name'=>$contact->name];
					//var_dump($atasan);die;
					//ini bukan atasan tapi biar mudah tinggal copy dari yg diatas

					$contact = DB::table('contact')->where('id', $atasan['contact_id'])->first();
					if(!empty($contact->email)) {
						$list_cc[] = $contact->email;
					}
				}

			}
		}


		//ASSIGNMENT STEP
		$ticket_assignment_log = DB::table('ticket_assignment_log')->where('ticket_id',$ticket->id)->get();
		//var_dump();
		if($ticket_assignment_log->count() <= 0) {
			//echo "KOSONG";
			if(!empty($request_management->assignment_tier)) {
				$assign_list = explode(",",$request_management->assignment_tier);
				$n = 0;
				foreach($assign_list as $team_id) {
					$n++;
					if($n == 1) {
						$contact = DB::table('contact')->where('id',$team_id)->first();
						//BELUM DIASSIGN KESIAPAPUN
						if($contact->type == "Employee") {
							//Manual Assignment
							if(!empty($contact->email)) {
								$list_cc[] = $contact->email;
							}
						}
					}
				}
			}
		}
		else {
			foreach($ticket_assignment_log as $log) {
					$team = DB::table('contact')->where('id',$log->team_id)->first();
					//$agent = DB::table('contact')->where('id',$log->agent_id)->first();
					$contact = DB::table('contact')->where('id', $log->agent_id)->first();
					if(!empty($contact->email)) {
						$list_cc[] = $contact->email;
					}


			}
		}




		return array_unique($list_cc);
	}
}

if (!function_exists('popoverJobTitle')) {
	function popoverJobTitle($contact_id){
		$jobss = DB::table('job_title')
        ->selectRaw('job_title.parent, job_title.id,
                    CONCAT(job_title.job_name,
                        COALESCE(CONCAT(\' - \', company.name), \'\'),
                        COALESCE(CONCAT(\' - \', location.name), \'\')) AS jobss,
                    c.name, c.id as contact_id')
        ->leftJoin('contact as c', 'c.job_title', '=', 'job_title.id')
        ->leftJoin('company', 'company.id', '=', 'job_title.company')
        ->leftJoin('location', 'location.id', '=', 'job_title.location')
        ->where('c.id', $contact_id)
        ->first();
		if(!empty($jobss->jobss)) {
			return 'data-container="body" data-toggle="popover" data-placement="bottom" data-content="'.$jobss->jobss.'"';
		}
		return "";
	}
}
if (!function_exists('sendNotifEmail')) {
	function sendNotifEmail($contact_id, $title, $message,$type="",$ref_id=0,$cc=[]){
		$email = DB::table('contact')->where('id',$contact_id)->value('email');

		if(empty($email)) {
			return ['status'=>FALSE, 'message'=>'Email Not Found'];
		}

		$user_id = DB::table('users')->where('person',$contact_id)->value('id');
		$role = DB::table('lnkuserstoroles')->where('users_id', $user_id)->first();

		DB::table('notification_message')->insertGetId(
			[
				'title' => $title,
				'user_id'=>$user_id,
				'contact_id'=>$contact_id,
				'message'=>$message,
				'type'=>$type,
				'ref_id'=>$ref_id,
				'created_at'=>date("Y-m-d H:i:s"),
				'updated_at'=>date("Y-m-d H:i:s"),
				'token' => generateRandomString(40),
			]
		);


        $data['ticket'] = DB::table('ticket')->where('id',$ref_id)->first();
        $data['ticket']->message = $message;
        $data['statuses'] = DB::table('ticket_approval')->where('ticket_id', $ref_id)->get();
		$data['message'] = $message;
		$data['title'] = $title;

		$url_customer = env('MAIL_CUSTOMER_REDIRECT_URL').'/ticket_detail/'.$data['ticket']->token;
		$url_agent = env('MAIL_AGENT_REDIRECT_URL').'/edit/'.$data['ticket']->token.'/ticket';
		$data['redirect_url'] = in_array($role->roles_id,  [11, 33]) ? $url_agent : $url_customer;

		try {
			if($email) {
				Illuminate\Support\Facades\Mail::to($email)->cc($cc)->send(new App\Mail\EmailSend($title,$data));
			}
		} catch (Exception $e) {
			return ['status'=>FALSE];
		}
		return ['status'=>TRUE];
	}
}

if (!function_exists('sendNotifEmailInventory')) {
	function sendNotifEmailInventory($contact_id, $title, $message,$type="",$ref_id=0,$cc=[]){
		$email = DB::table('contact')->where('id',$contact_id)->value('email');

		if(empty($email)) {
			return ['status'=>FALSE, 'message'=>'Email Not Found'];
		}

		$user_id = DB::table('users')->where('person',$contact_id)->value('id');
		$role = DB::table('lnkuserstoroles')->where('users_id', $user_id)->first();

		DB::table('notification_message')->insertGetId(
			[
				'title' => $title,
				'user_id'=>$user_id,
				'contact_id'=>$contact_id,
				'message'=>$message,
				'type'=>$type,
				'ref_id'=>$ref_id,
				'created_at'=>date("Y-m-d H:i:s"),
				'updated_at'=>date("Y-m-d H:i:s"),
				'token' => generateRandomString(40),
			]
		);
        if ($type == 'goods_issue') {
            $data['ticket'] = DB::table('goods_issues')->where('id',$ref_id)->first();
            $data['ticket']->message = $message;
            $data['statuses'] = DB::table('goods_issue_approvals')->where('goods_issue_id', $ref_id)->get();
            $data['message'] = $message;
            $data['title'] = $title;

            $url_customer = env('MAIL_CUSTOMER_REDIRECT_URL').'/goods_issue/'.$data['ticket']->id;
            $url_agent = env('MAIL_AGENT_REDIRECT_URL').'/goods_issue/'.$data['ticket']->id;
            $data['redirect_url'] = $role->roles_id == 11 ? $url_agent : $url_customer;
        }else{
            $data['ticket'] = DB::table('goods_receives')->where('id',$ref_id)->first();
            $data['ticket']->message = $message;
            $data['statuses'] = DB::table('goods_receive_approvals')->where('goods_receive_id', $ref_id)->get();
            $data['message'] = $message;
            $data['title'] = $title;

            $url_customer = env('MAIL_CUSTOMER_REDIRECT_URL').'/goods_receive/'.$data['ticket']->id;
            $url_agent = env('MAIL_AGENT_REDIRECT_URL').'/goods_receive/'.$data['ticket']->id;
            $data['redirect_url'] = $role->roles_id == 11 ? $url_agent : $url_customer;
        }
        // dd($data);
        //$html =  view('email_data_ticket')
            //->with('ticket', $ticket)
            //->with('title', $title)
            //->with('statuses', $ticket_statuses)
            //->with('breadcumb', $breadcumb)
            //->render();
        //$message .= $html;
		try {
			if($email) {
				Illuminate\Support\Facades\Mail::to($email)->cc($cc)->send(new App\Mail\EmailSendGoodsIssues($title,$data));
			}
        } catch (Exception $e) {
            echo $e->getMessage()."<br/>";
            dd($e->getMessage()."<br/>");
        }
		return ['status'=>TRUE];
	}
}

if (!function_exists('sendNotifEmailByUserIdInventory')) {
	function sendNotifEmailByUserIdInventory($user_id, $title, $message,$type="",$ref_id=0,$cc=[]){
		$user = DB::table('users')
			->where('users.id',$user_id)
			->leftJoin('lnkuserstoroles', 'lnkuserstoroles.users_id', '=', 'users.id')
			->first();

		if(empty($user)) {
			return ['status'=>FALSE, 'message'=>'Contact Not Found'];
		}
		$email = DB::table('contact')->where('id',$user->person)->value('email');
		if(empty($email)) {
			return ['status'=>FALSE, 'message'=>'Email Not Found'];
		}

		DB::table('notification_message')->insertGetId(
					[
						'title' => $title,
						'user_id'=>$user_id,
						'contact_id'=>$user->person,
						'message'=>$message,
						'type'=>$type,
						'ref_id'=>$ref_id,
						'created_at'=>date("Y-m-d H:i:s"),
						'updated_at'=>date("Y-m-d H:i:s"),
						'token' => generateRandomString(40),
					]
				);


        if ($type == 'goods_issue') {
            $data['ticket'] = DB::table('goods_issues')->where('id',$ref_id)->first();
            $data['ticket']->message = $message;
            $data['statuses'] = DB::table('goods_issue_approvals')->where('goods_issue_id', $ref_id)->get();
            $data['message'] = $message;
            $data['title'] = $title;

            $url_customer = env('MAIL_CUSTOMER_REDIRECT_URL').'/goods_issue/'.$data['ticket']->id;
            $url_agent = env('MAIL_AGENT_REDIRECT_URL').'/edit/'.$data['ticket']->id.'/goods_issue';
            $data['redirect_url'] = $user->roles_id == 11 ? $url_agent : $url_customer;
        }else{
            $data['ticket'] = DB::table('goods_receives')->where('id',$ref_id)->first();
            $data['ticket']->message = $message;
            $data['statuses'] = DB::table('goods_receive_approvals')->where('goods_receive_id', $ref_id)->get();
            $data['message'] = $message;
            $data['title'] = $title;

            $url_customer = env('MAIL_CUSTOMER_REDIRECT_URL').'/goods_receive/'.$data['ticket']->id;
            $url_agent = env('MAIL_AGENT_REDIRECT_URL').'/edit/'.$data['ticket']->id.'/goods_receive';
            $data['redirect_url'] = $user->roles_id == 11 ? $url_agent : $url_customer;
        }
		//$html =  view('email_data_ticket')
            //->with('ticket', $ticket)
            //->with('title', $title)
            //->with('statuses', $ticket_statuses)
            //->with('breadcumb', $breadcumb)
            //->render();
        //$message .= $html;

		try {
			if($email) {
                Illuminate\Support\Facades\Mail::to($email)->cc($cc)->send(new App\Mail\EmailSendGoodsIssues($title,$data));
			}
		} catch (Exception $e) {
			dd($e->getMessage());
		}
		//echo "TESS";
		return ['status'=>TRUE];
	}
}

if (!function_exists('sendNotifEmailByUserId')) {
	function sendNotifEmailByUserId($user_id, $title, $message,$type="",$ref_id=0,$cc=[]){
		$user = DB::table('users')
			->where('users.id',$user_id)
			->leftJoin('lnkuserstoroles', 'lnkuserstoroles.users_id', '=', 'users.id')
			->first();

		if(empty($user)) {
			return ['status'=>FALSE, 'message'=>'Contact Not Found'];
		}
		$email = DB::table('contact')->where('id',$user->person)->value('email');
		if(empty($email)) {
			return ['status'=>FALSE, 'message'=>'Email Not Found'];
		}

		DB::table('notification_message')->insertGetId(
					[
						'title' => $title,
						'user_id'=>$user_id,
						'contact_id'=>$user->person,
						'message'=>$message,
						'type'=>$type,
						'ref_id'=>$ref_id,
						'created_at'=>date("Y-m-d H:i:s"),
						'updated_at'=>date("Y-m-d H:i:s"),
						'token' => generateRandomString(40),
					]
				);


        $data['ticket'] = DB::table('ticket')->where('id',$ref_id)->first();
        $data['ticket']->message = $message;
        $data['statuses'] = DB::table('ticket_approval')->where('ticket_id', $ref_id)->get();
		$data['message'] = $message;
		$data['title'] = $title;

		$url_customer = env('MAIL_CUSTOMER_REDIRECT_URL').'/ticket_detail/'.$data['ticket']->token;
		$url_agent = env('MAIL_AGENT_REDIRECT_URL').'/edit/'.$data['ticket']->token.'/ticket';
		$data['redirect_url'] = $user->roles_id == 11 ? $url_agent : $url_customer;

		//$html =  view('email_data_ticket')
            //->with('ticket', $ticket)
            //->with('title', $title)
            //->with('statuses', $ticket_statuses)
            //->with('breadcumb', $breadcumb)
            //->render();
        //$message .= $html;

		try {
			if($email) {
				Illuminate\Support\Facades\Mail::to($email)->cc($cc)->send(new App\Mail\EmailSend($title,$data));
			}
		} catch (Exception $e) {
			//$e->getMessage()
		}
		//echo "TESS";
		return ['status'=>TRUE];
	}
}

if (!function_exists('notif_to_all_needed_contact')) {
	function notif_to_all_needed_contact($id,$ticket,$title,$content,$type_notif,$to_agent_id = ""){
			$list_contact = getContactCaseJourney($ticket);
			$contact = DB::table('contact')->select('id','name','email')->where('id',Auth::user()->person)->first();
			$list_contact[$contact->id] = $contact;
			if(!empty($ticket->notif)) {
				$list_id_contact = explode(",",$ticket->notif);
				$list_contact = DB::table('contact')->select('id','name','email')->whereIn('id',$list_id_contact)->get();
			}
			//var_dump($list_contact);die;
			$message = $content;
			$type = $type_notif;
			$ref_id = $id;
			$i = 0;
			$first_email = "";
			$list_cc = [];
			foreach($list_contact as $notif_contact) {
				$i++;
				//sendNotifEmail($notif_contact->id, $title, $content,$type_notif,$id);
				$contact_id = $notif_contact->id;


				$contact = DB::table('contact')
					->where('contact.id',$contact_id)
					->leftJoin('users', 'users.person' , '=', 'contact.id')
					->leftJoin('lnkuserstoroles', 'lnkuserstoroles.users_id', '=', 'users.id')
					->first();

				$email = $contact->email;

				if(!empty($to_agent_id)) {
					if($contact_id == $to_agent_id) {
						$first_email = $email;
					} else {
						$list_cc[] = $email;
					}
				} else {
					if($i == 1) {
						$first_email = $email;
					} else {
						$list_cc[] = $email;
					}
				}

				if(empty($email)) {
					//return ['status'=>FALSE, 'message'=>'Email Not Found'];
				}

				$user_id = DB::table('users')->where('person',$contact_id)->value('id');


				DB::table('notification_message')->insertGetId(
					[
						'title' => $title,
						'user_id'=>$user_id,
						'contact_id'=>$contact_id,
						'message'=>$message,
						'type'=>$type,
						'ref_id'=>$ref_id,
						'created_at'=>date("Y-m-d H:i:s"),
						'updated_at'=>date("Y-m-d H:i:s"),
						'token' => generateRandomString(40),
					]
				);

			}

			$data['ticket'] = DB::table('ticket')->where('id',$ref_id)->first();
			$data['ticket']->message = $message;
			$data['statuses'] = DB::table('ticket_approval')->where('ticket_id', $ref_id)->get();
			$data['message'] = $message;
			$data['title'] = $title;

			$url_customer = env('MAIL_CUSTOMER_REDIRECT_URL').'/ticket_detail/'.$data['ticket']->token;
			$url_agent = env('MAIL_AGENT_REDIRECT_URL').'/edit/'.$data['ticket']->token.'/ticket';
			$data['redirect_url'] = $contact->roles_id == 11 ? $url_agent : $url_customer;

			try {
				Illuminate\Support\Facades\Mail::to($email)->cc($list_cc)->send(new App\Mail\EmailSend($title,$data));
			} catch (Exception $e) {
				// echo $e->getMessage();
			}

	}
}

if (!function_exists('sendBulkNotifEmail')) {
	function sendBulkNotifEmail($contact_ids, $title, $message,$type="",$ref_id=0,$cc=[]){
		$emails = DB::table('contact')->whereIn('id',$contact_ids)->get(['email'])->pluck('email')->toArray();

		if(empty($emails)) {
			return ['status'=>FALSE, 'message'=>'Email Not Found'];
		}

		$users = User::whereIn('person', $contact_ids)->get(['id', 'person']);

		$payload = $users->map(function($row) use($title, $message, $type, $ref_id){
			return [
				'title' => $title,
				'user_id' => $row->id,
				'contact_id' => $row->person,
				'message' => $message,
				'type' => $type,
				'ref_id' => $ref_id,
				'created_at'=>date("Y-m-d H:i:s"),
				'updated_at'=>date("Y-m-d H:i:s"),
				'token' => generateRandomString(40)
			];
		})->toArray();

		DB::table('notification_message')->insert($payload);

        $data['ticket'] = DB::table('ticket')->where('id',$ref_id)->first();
        $data['ticket']->message = $message;
        $data['statuses'] = DB::table('ticket_approval')->where('ticket_id', $ref_id)->get();
		$data['message'] = $message;
		$data['title'] = $title;

		if($emails) {
			Illuminate\Support\Facades\Mail::to($emails)->cc($cc)->send(new App\Mail\EmailSend($title,$data));
		}

		return ['status'=>TRUE];
	}
}

if (!function_exists('notif_to_all_needed_contact2')) {
	function notif_to_all_needed_contact2($id,$ticket,$title,$content,$type_notif,$list_id_contact){
			//$list_contact = getContactCaseJourney($ticket);
			//$contact = DB::table('contact')->select('id','name','email')->where('id',Auth::user()->person)->first();
			//$list_contact[$contact->id] = $contact;
			if(!empty($list_id_contact)) {
				//$list_id_contact = explode(",",$ticket->notif);
				$list_contact = DB::table('contact')->select('id','name','email')->whereIn('id',$list_id_contact)->get();
			}
			//var_dump($list_contact);die;
			$message = $content;
			$type = $type_notif;
			$ref_id = $id;
			$i = 0;
			$first_email = "";
			$list_cc = [];
			foreach($list_contact as $notif_contact) {
				$i++;
				//sendNotifEmail($notif_contact->id, $title, $content,$type_notif,$id);
				$contact_id = $notif_contact->id;


				$email = DB::table('contact')->where('id',$contact_id)->value('email');

				if($i == 1) {
					$first_email = $email;
				} else {
					$list_cc[] = $email;
				}

				if(empty($email)) {
					//return ['status'=>FALSE, 'message'=>'Email Not Found'];
				}

				$user_id = DB::table('users')->where('person',$contact_id)->value('id');


				DB::table('notification_message')->insertGetId(
							[
								'title' => $title,
								'user_id'=>$user_id,
								'contact_id'=>$contact_id,
								'message'=>$message,
								'type'=>$type,
								'ref_id'=>$ref_id,
								'created_at'=>date("Y-m-d H:i:s"),
								'updated_at'=>date("Y-m-d H:i:s"),
								'token' => generateRandomString(40),
							]
						);

			}

			$data['ticket'] = DB::table('ticket')->where('id',$ref_id)->first();
			$data['ticket']->message = $message;
			$data['statuses'] = DB::table('ticket_approval')->where('ticket_id', $ref_id)->get();
			$data['message'] = $message;
			$data['title'] = $title;

			try {
				Illuminate\Support\Facades\Mail::to($email)->cc($cc)->send(new App\Mail\EmailSend($title,$data));
			} catch (Exception $e) {
				//$e->getMessage()
			}

	}
}


//if (!function_exists('template1')) {
	//function template_email($file_template,$listid, $type, $data, $subject, $title, $description, $token) {
		//$body = "";
		////$template = file_get_contents("block.txt");
		//$header = convertHTML($title, file_get_contents($file_template),"%title%");
		//$header = convertHTMLdesc($description, $header,"%description%");
		//$footer = "";//convertHTML($token, file_get_contents("footer.txt"),"%token%");
		//$content = "";
		////foreach($data as $r) {
				////$block = "";
				////$teks = strip_tags($r['text']);
				////if(strlen($teks) > 250) {$teks = substr($teks,0,250)."...";}

				////$block = convertHTML($r['name'], file_get_contents("block.txt"),"%listtitle%");
				////$block = convertHTML($teks, $block,"%listdesc%");
				////$block = convertHTML("http://asianmuseum.org/page/".$type."/".$r['url_name'], $block,"%listlink%");
				////$block = convertHTML("http://asianmuseum.org/assets/news/medium/".$r['userfile'], $block,"%image%");
				////$content .= $block;
		////}
		//return $header.$content.$footer;
	//}
//}
//if (!function_exists('convertHTML')) {
	//function convertHTML($content, $templateHTML,$target) {
		//$content = html_entity_decode(stripslashes($content));
		//return str_replace($target, $content, $templateHTML);
	//}
//}
//if (!function_exists('convertHTMLdesc')) {
	//function convertHTMLdesc($content, $templateHTML,$target) {
		//$content = $content;
		//return str_replace($target, $content, $templateHTML);
	//}
//}
if (!function_exists('getEmployeeJabatanTerbawah')) {
    function getEmployeeJabatanTerbawah($team_id) {
		$employee = null;
		$job_title_id = null;

							$list_employee_team = DB::table('lnkemployeetoteam')->where('team_id',$team_id)->get();
							$job_title = null;
							//var_dump($team_id);
							//var_dump($list_employee_team);
							$list_obj_employee = [];
							foreach($list_employee_team as $et) {
								$employee = DB::table('contact')
												->where('contact.status', '=', 'Active')
												->whereNull('contact.deleted_at')
												->where('id',$et->employee_id)->first();
								//var_dump($employee);
								if(!empty($employee->job_title)) {

									//kumpulkan data employee, atasan, dan jobtitlenya, utk nanti cross cek hirarki yg terbawah
									$list_job_title_atasan = [];
									$n = 0;
									$cek_job_title = DB::table('job_title')->where('id',$employee->job_title)->first();
									do {
										$n++;
										$atasan_found = false;

										if(!empty($cek_job_title->parent)) {
											$atasan_found = true;
											$list_job_title_atasan[] = $cek_job_title->parent;

											//next ganti ke parentnya
											$cek_job_title = DB::table('job_title')->where('id',$cek_job_title->parent)->first();
											//var_dump($cek_job_title);
										}

									} while($n < 100 && $atasan_found);

									$list_obj_employee[$employee->id] = ['obj_employee'=>$employee,
																		'job_title_id'=>$employee->job_title,
																		'list_job_title_atasan'=>$list_job_title_atasan,
																		];

									$job_title_id = $employee->job_title;
								}
							}

							//dd($list_obj_employee);
							$list_obj_employee2 = $list_obj_employee;
							$list_obj_employee3 = $list_obj_employee; //copy yg ketiga nanti jadi hasilnya
							//setelah employee terkumpul cek semua atasannya
							foreach($list_obj_employee as $id_employee => $obj_employee) {
								foreach($list_obj_employee2 as $id_employee2 => $obj_employee2) {
									if(in_array($obj_employee2['job_title_id'],$obj_employee['list_job_title_atasan'])) {
										//hapus employee ini karena employee atasan dari yang lain
										unset($list_obj_employee3[$id_employee2]);
									}
								}
							}
							//dd($list_obj_employee3);
							//semua unsur atasan sudah dihapus
							//sisanya yang kemungkinan bawahan ambil salah satu saja
							if(!empty($list_obj_employee3)) {
								$first_element = reset($list_obj_employee3);//ambil element pertama
								$employee = $first_element['obj_employee'];
								if(!empty($employee->job_title)) {
									$job_title_id = $employee->job_title;
								}
							}
							//dd($employee);
		return [$job_title_id,$employee];
	}
}

if (!function_exists('loadBalance')) {
    function loadBalance($team_id,$is_check_pending_leave="") {
		$list_employee = DB::table('lnkemployeetoteam')->where('team_id',$team_id)->get();

		if($is_check_pending_leave=="check_pending_leave") {
			$list_employee = filterActiveEmployeeForCheckingPendingLeave($list_employee->toArray());
		} else {
			$list_employee = filterActiveEmployee($list_employee->toArray());
		}

		//pilih agent2 yang waktu saat ini masuk di coverage windows,
		//jika tidak ada, maka pilih waktu terdekat jadwal kerja selanjutnya di coverage windows
		$list_employee = filterCoverageWindows($list_employee);

		//cek tiket yang masih open dari masing2 employee
		$contact_smallest_id = null;
		$contact_smallest_count = null;
		$contact_smallest = [];
		foreach($list_employee as $e) {
			$ticket_open_count = DB::table('ticket')->where('agent_id',$e->employee_id)->whereIn('status',['Open','On Progress','Re-Open'])->count();
			$ticket_resolved_count = DB::table('ticket')->where('agent_id',$e->employee_id)->whereIn('status',['Resolved'])->count();
			//echo $ticket_open_count;
			//echo "<br/>";
			//echo $e->employee_id;
			//echo "<br/>";
			if(empty($contact_smallest)) {
				$contact_smallest[$e->employee_id] = $ticket_resolved_count;
				$contact_smallest_count = $ticket_open_count;
			} else {
				if($ticket_open_count == $contact_smallest_count) {
					$contact_smallest[$e->employee_id] = $ticket_resolved_count;
				}
				if($ticket_open_count < $contact_smallest_count) {
					$contact_smallest = [];
					$contact_smallest[$e->employee_id] = $ticket_resolved_count;
					$contact_smallest_count = $ticket_open_count;
				}
			}
		}
		//dengan asort menjamin bahwa elemen pertama adalah yang resolved ticketny paling sedikit
		//karena value array berisi jumlah resolved ticket
		asort($contact_smallest);
		//echo $contact_smallest_id;
		//die;
		//var_dump($contact_smallest);
		//die;
		if(!empty($contact_smallest)) {
			foreach($contact_smallest as $id_employee => $jml_resolved) {
				//loop pertama langsung return, ambil elemen pertama
				Log::info($id_employee);
				return $id_employee;
			}
		} else {
			return null;
		}
	}
}

if (!function_exists('filterUserActive')) {
    function filterActiveEmployee($list_employee) {
		if(is_array($list_employee)) {
			//KALAU ARRAY DAPATKAN LIST ARRAY USER YANG AKTIF
			$list_employee2 = [];
			foreach($list_employee as $e) {
				$is_active = DB::table('contact')
                ->where(function($query) use ($e) {
                    $query->where('status', '=', 'Active')
                        ->orWhereNull('status');
                })
                ->where('type', '=', 'Employee')
                ->where('id', '=', $e->employee_id)
                ->first();
				if($is_active) {
					//cek cuti
					$on_leave = DB::table('leave')->whereRaw('(CURRENT_DATE BETWEEN start_date AND end_date) AND employee=?',[$e->employee_id])->first();
					if(!$on_leave) {
						$list_employee2[] = $e;
					}
				}
			}
		} else {
			//KALAU BUKAN ARRAY CEK TRUE FALSE
                $is_active = DB::table('contact')
                ->where(function ($query) {
                    $query->where('status', '=', 'Active')
                        ->orWhereNull('status');
                })
                ->where('type', '=', 'Employee')
                ->whereIn('id', [$list_employee])
                ->first();
				if($is_active) {
					//cek cuti
					$on_leave = DB::table('leave')->whereRaw('(CURRENT_DATE BETWEEN start_date AND end_date) AND employee=?',[$list_employee])->first();
					if(!$on_leave) {
						return TRUE;
					}
				}
				return FALSE;
		}
		return $list_employee2;
	}
}

if (!function_exists('filterActiveEmployeeForCheckingPendingLeave')) {
    function filterActiveEmployeeForCheckingPendingLeave($list_employee) {
		if(is_array($list_employee)) {
			//KALAU ARRAY DAPATKAN LIST ARRAY USER YANG AKTIF
			$list_employee2 = [];
			foreach($list_employee as $e) {
				$is_active = DB::table('contact')
                ->where(function($query) use ($e) {
                    $query->where('status', '=', 'Active')
                        ->orWhereNull('status');
                })
                ->where('type', '=', 'Employee')
                ->where('id', '=', $e->employee_id)
                ->first();
				if($is_active) {
					$list_employee2[] = $e;
					//cek cuti tidak usah
					//$on_leave = DB::table('leave')->whereRaw('(CURRENT_DATE BETWEEN start_date AND end_date) AND employee=?',[$e->employee_id])->first();
					//if(!$on_leave) {
						//$list_employee2[] = $e;
					//}
				}
			}
		} else {
			//KALAU BUKAN ARRAY CEK TRUE FALSE
                $is_active = DB::table('contact')
                ->where(function ($query) {
                    $query->where('status', '=', 'Active')
                        ->orWhereNull('status');
                })
                ->where('type', '=', 'Employee')
                ->whereIn('id', [$list_employee])
                ->first();
				if($is_active) {
					//cek cuti tidak usah
					return TRUE;
					//$on_leave = DB::table('leave')->whereRaw('(CURRENT_DATE BETWEEN start_date AND end_date) AND employee=?',[$list_employee])->first();
					//if(!$on_leave) {
						//return TRUE;
					//}
				}
				return FALSE;
		}
		return $list_employee2;
	}
}

if (!function_exists('filterCoverageWindows')) {
    function filterCoverageWindows($list_employee) {
		if(empty($list_employee)) {
			return [];
		}

		if(!is_array($list_employee)) {
			$list_employee = [$list_employee];
		}

		Log::info($list_employee);
		//TAHAP 1: filter agent-agent yang coverage windows nya masuk
		$list_employee2 = [];
		foreach($list_employee as $e) {
			//cek jam sekarang masuk coverage tidak
			$coverage_windows = DB::table('contact')
				->join('coverage_windows', 'coverage_windows.id', '=', 'contact.coverage_windows')
				->select('coverage_windows.*')
				->where('contact.id',$e->employee_id)
				->first();
			Log::info(json_encode($coverage_windows));
			if(!$coverage_windows) {
				//tidak ada coverage windowsnya, skip current loop, lanjut employee berikutnya
				continue;
			}
			$coverage_hours = $coverage_windows->coverage_hours;
			$list_cov = explode(",",$coverage_hours);
			$list_cov = array_map('trim', $list_cov);
			$list_cov = array_filter($list_cov); //remove empty elements

			if(empty($list_cov)) {
				//tidak ada coverage_hoursnya, skip current loop, lanjut employee berikutnya
				continue;
			}
			foreach($list_cov as $cov) {
				$d = explode(" - ",$cov);
				$e_start = explode(":",$d[0]);//contoh : 3:00:30
				$e_end = explode(":",$d[1]);//contoh : 4:30

				$day_number = $e_start[0];
				$start_hour = $e_start[1];
				$start_minute = $e_start[2];

				$start_time = $start_hour.':'.$start_minute;
				$end_time = $d[1];

				$now = date('H:i');
				$current_day_number = date('w');//get to number of the day (0 to 6, 0 being sunday, and 6 being saturday)


				//cek saat ini masuk range coverage windows agent
				//yang dicek hari nya dan juga jam kerjanya

				if(($day_number == $current_day_number) &&
					($start_time <= $now) && $now <= $end_time)
				{
					//agent ini saat ini sudah masuk jam kerjanya (coverage windows)
					$e->coverage_windows = $coverage_windows;
					$e->selected_coverage_hours = $cov;//just for log purpose
					$list_employee2[] = $e;
				}


			}
			$list_agent_in_coverage = $list_employee2;
		}

		//TAHAP 2: JIKA TAHAP 1 TAK ADA AGENT YG MASUK DI COVERAGE WINDOWS
		//MAKA CEK AGENT TERDEKAT JADWAL JAM KERJANYA DI COVERAGE WINDOWS
		if(empty($list_employee2)) {
			$list_agent_waktu_terdekat = [];
			$jarak_waktu_terdekat = null;
			foreach($list_employee as $e) {
				//cek jam sekarang masuk coverage tidak
				$coverage_windows = DB::table('contact')
					->join('coverage_windows', 'coverage_windows.id', '=', 'contact.coverage_windows')
					->select('coverage_windows.*')
					->where('contact.id',$e->employee_id)
					->first();
				if(!$coverage_windows) {
					//tidak ada coverage windowsnya, skip current loop, lanjut employee berikutnya
					continue;
				}
				$coverage_hours = $coverage_windows->coverage_hours;
				$list_cov = explode(",",$coverage_hours);
				$list_cov = array_map('trim', $list_cov);
				$list_cov = array_filter($list_cov); //remove empty elements

				if(empty($list_cov)) {
					//tidak ada coverage_hoursnya, skip current loop, lanjut employee berikutnya
					continue;
				}


				foreach($list_cov as $cov) {
					$d = explode(" - ",$cov);
					$e_start = explode(":",$d[0]);//contoh : 3:00:30
					$e_end = explode(":",$d[1]);//contoh : 4:30

					$day_number = $e_start[0];
					$start_hour = $e_start[1];
					$start_minute = $e_start[2];

					$start_time = $start_hour.':'.$start_minute;
					$end_time = $d[1];

					$now = date('H:i');
					$current_day_number = date('w');//get to number of the day (0 to 6, 0 being sunday, and 6 being saturday)

					$nowTime = strtotime($now);
					$startTime = strtotime($start_time);
					$time_difference = $startTime - $nowTime;

					$e->coverage_windows = $coverage_windows;

					if ($day_number == $current_day_number) {
						//kalau minus berarti udah lewat waktunya
						//jadi yang diterima yang positif saja
						if($time_difference > 0) {
							if(is_null($jarak_waktu_terdekat)) {
								$jarak_waktu_terdekat = $time_difference;
								$e->selected_coverage_hours = $cov;//just for log purpose
								$list_agent_waktu_terdekat = [$e];
							} else if($time_difference == $jarak_waktu_terdekat) {
								//ada yang sama kecilnya jaraknya
								//tambahkan ke list agent, berarti lebih dari 1 kandidat
								$e->selected_coverage_hours = $cov;//just for log purpose
								$list_agent_waktu_terdekat[] = $e;
							} else if($time_difference < $jarak_waktu_terdekat) {
								//ada yang lebih kecil waktu terdekat
								//maka kosongkan array diganti dgn 1 agent tsb
								$jarak_waktu_terdekat = $time_difference;
								$e->selected_coverage_hours = $cov;//just for log purpose
								$list_agent_waktu_terdekat = [$e];
							}
						}
					}

				}
			}

			$list_employee2 = $list_agent_waktu_terdekat;
		}


		//TAHAP 3: JIKA HARI INI TIDAK DITEMUKAN TERDEKAT
		//CEK HARI2 BERIKUTNYA
		if(empty($list_employee2)) {

			$list_agent_waktu_terdekat_hari2_berikutnya = [];
			$jarak_waktu_terdekat = null;

			$selected_day_number = date('w');//get to number of the day (0 to 6, 0 being sunday, and 6 being saturday)
			//dilakukan pengecekan tiap hari selama 6 hari ke depan sampai ketemu agent terdekat di hari2 berikutnya
			for($i=1;$i<=6;$i++) {
				if($selected_day_number == 6) {
					$selected_day_number = 0;
				} else {
					$selected_day_number++;
				}
				Log::info($selected_day_number);
				foreach($list_employee as $e) {
					//cek jam sekarang masuk coverage tidak
					$coverage_windows = DB::table('contact')
						->join('coverage_windows', 'coverage_windows.id', '=', 'contact.coverage_windows')
						->select('coverage_windows.*')
						->where('contact.id',$e->employee_id)
						->first();
					if(!$coverage_windows) {
						//tidak ada coverage windowsnya, skip current loop, lanjut employee berikutnya
						continue;
					}

					$coverage_hours = $coverage_windows->coverage_hours;
					$list_cov = explode(",",$coverage_hours);
					$list_cov = array_map('trim', $list_cov);
					$list_cov = array_filter($list_cov); //remove empty elements

					if(empty($list_cov)) {
						//tidak ada coverage_hoursnya, skip current loop, lanjut employee berikutnya
						continue;
					}

					foreach($list_cov as $cov) {
						$d = explode(" - ",$cov);
						$e_start = explode(":",$d[0]);//contoh : 3:00:30
						$e_end = explode(":",$d[1]);//contoh : 4:30

						$day_number = $e_start[0];
						$start_hour = $e_start[1];
						$start_minute = $e_start[2];

						$start_time = $start_hour.':'.$start_minute;
						$end_time = $d[1];

						$now = date('H:i');

						$nowTime = strtotime($now);
						$startTime = strtotime($start_time);


						$e->coverage_windows = $coverage_windows;


						if ($day_number == $selected_day_number) {
							if(is_null($jarak_waktu_terdekat)) {
								$jarak_waktu_terdekat = $start_time;
								$e->selected_coverage_hours = $cov;//just for log purpose
								$list_agent_waktu_terdekat_hari2_berikutnya = [$e];
								Log::info($cov);
							} else if($start_time == $jarak_waktu_terdekat) {
								//ada yang sama kecilnya jaraknya
								//tambahkan ke list agent, berarti lebih dari 1 kandidat
								$e->selected_coverage_hours = $cov;//just for log purpose
								$list_agent_waktu_terdekat_hari2_berikutnya[] = $e;
								Log::info($cov);
							} else if($start_time < $jarak_waktu_terdekat) {
								//ada yang lebih kecil waktu terdekat
								//maka kosongkan array diganti dgn 1 agent tsb
								$jarak_waktu_terdekat = $start_time;
								$e->selected_coverage_hours = $cov;//just for log purpose
								$list_agent_waktu_terdekat_hari2_berikutnya = [$e];
								Log::info($cov);
							}
						}

					}
				}

				if(!empty($list_agent_waktu_terdekat_hari2_berikutnya)) {
					//SUDAH KETEMU LIST AGENTNYA PADA HARI TERPILIH, MAKA TIDAK PERLU
					//PENGECEKAN DI HARI-HARI BERIKUTNYA
					break;
				}
			}
			$list_employee2 = $list_agent_waktu_terdekat_hari2_berikutnya;
		}

		Log::info("list_employee2");
		Log::info($list_employee2);
		Log::info("list_agent_in_coverage");
		Log::info($list_agent_in_coverage ?? null);
		Log::info("list_agent_waktu_terdekat");
		Log::info($list_agent_waktu_terdekat ?? null);
		Log::info("list_agent_waktu_terdekat_hari2_berikutnya");
		Log::info($list_agent_waktu_terdekat_hari2_berikutnya ?? null);
		return $list_employee2;
	}
}

if (!function_exists('roundRobin')) {
    function roundRobin($team_id,$is_check_pending_leave="") {
		$list_employee = DB::table('lnkemployeetoteam')->where('team_id',$team_id)->get();

		if($is_check_pending_leave=="check_pending_leave") {
			$list_employee = filterActiveEmployeeForCheckingPendingLeave($list_employee->toArray());
		} else {
			$list_employee = filterActiveEmployee($list_employee->toArray());
		}

		//pilih agent2 yang waktu saat ini masuk di coverage windows,
		//jika tidak ada, maka pilih waktu terdekat jadwal kerja selanjutnya di coverage windows
		$list_employee = filterCoverageWindows($list_employee);

		$assign_list_time = [];
		foreach($list_employee as $e) {
			$last_ticket_of_employee = DB::table('ticket_assign_time')->where('agent_id',$e->employee_id)->orderBy('assign_time','desc')->whereNotNull('assign_time')->first();
			if(empty($last_ticket_of_employee)) {
				//kalau ditemukan yang masih kosong maka yang itu saja
				Log::info($e->employee_id);
				return $e->employee_id;
			} else {
				//kumpulkan semua time assign yang terakhir dari employee
				$assign_list_time[$e->employee_id] = $last_ticket_of_employee->assign_time;
			}
		}

		asort($assign_list_time);
		//var_dump($assign_list_time);
		//die;
		foreach($assign_list_time as $key => $value) {
			$employee_id = $key;
			//echo $employee_id;
			//die;
			Log::info($employee_id);
			return $employee_id;//first loop employee is the target
		}
		//die;
		return null;
	}
}
if (!function_exists('random')) {
    function random($team_id,$is_check_pending_leave="") {
		$list_employee = DB::table('lnkemployeetoteam')->where('team_id',$team_id)->get();

		if($is_check_pending_leave=="check_pending_leave") {
			$list_employee = filterActiveEmployeeForCheckingPendingLeave($list_employee->toArray());
		} else {
			$list_employee = filterActiveEmployee($list_employee->toArray());
		}

		//pilih agent2 yang waktu saat ini masuk di coverage windows,
		//jika tidak ada, maka pilih waktu terdekat jadwal kerja selanjutnya di coverage windows
		$list_employee = filterCoverageWindows($list_employee);

		$element_number = rand(0,(count($list_employee)-1));

		Log::info($list_employee[$element_number]->employee_id ?? null);
		return $list_employee[$element_number]->employee_id ?? null;
	}
}

if (!function_exists('assignment_system')) {
	function assignment_system($ticket,$request_management,$next_agent = "",$escalation_or_approval = "approval") {
				$id = $ticket->id;


				$assign_list 			= explode(",",$request_management->assignment_tier);
				$list_assignment_tier 	= explode(",",$request_management->assignment_tier);
				$assign_type_list = explode(",",$request_management->assignment_type);

				$tier_flag = false;
				$next_tier_index = 0;
				$ticket->current_tier = $ticket->current_tier ?? 1;


				if($escalation_or_approval == "approval") {
					//trigernya tombol approval
					$next_team_tier = 0;
					$next_tier_index = 0;
					//ingat current tier mulai dari 1, sedangkan array mulai dari 0,
					if(!empty($list_assignment_tier[$next_tier_index])) {
						$next_team_tier = $list_assignment_tier[$next_tier_index];
					}

				} else {
					//trigernya tombol eskalasi
					$next_team_tier = 0;
					$next_tier_index = $ticket->current_tier;

					//ingat current tier mulai dari 1, sedangkan array mulai dari 0,
					//jadi current tier malah jadi next posisi array yang dimaksud
					if(!empty($list_assignment_tier[$next_tier_index])) {
						$next_team_tier = $list_assignment_tier[$next_tier_index];
					}
				}

				if(empty($next_team_tier) && empty($next_agent)) {
					//tidak ada lagi maka status tidak berubah
					//notif
					return ['status'=>false,'message'=>"There's no available team for next tier. "];
					//echo json_encode(['success'=>false,'message'=>"There's no available team for next tier. You have to select Employee in select box."]);
					//die;
				}

				$tier = $next_tier_index+1;
				//start dari $next_team_tier
				//$next_tier_index di atas sudah inisiasi
                // dd([$next_tier_index, count($assign_list), $assign_list]);
				for ($next_tier_index;$next_tier_index<count($assign_list);$next_tier_index++) {
					$assign_list = explode(",",$request_management->assignment_tier);
					$assign_type_list = explode(",",$request_management->assignment_type);
					$team_id = $assign_list[$next_tier_index];
					$tier = $next_tier_index+1;
                    // dd('a');

					if(!empty($next_agent)) {
						//jika next agent sudah ditentukan maka ambil yang next agent
						//tapi tentukan dulu dia di tim mana


						//flow mirip manual yang kondisi paling bawah
						$target_agent_id = $next_agent;

						$is_team_member = DB::table('lnkemployeetoteam')->where(['employee_id'=>$next_agent,'team_id'=>$team_id])->first();
						$agent_id = $next_agent;
						if(!$is_team_member) {
							$team_id = null;//jika buka member tim maka diunset saja
						}

						$is_active = filterActiveEmployee($target_agent_id);
						if(!$is_active) {
							//echo "TIDAK AKTIF";
							$agent_id = null;
						} else {
							//echo "AKTIF";

							$agent_id = $target_agent_id;
							//var_dump($agent_id);
						}

						$is_active_pending_leave = filterActiveEmployeeForCheckingPendingLeave($target_agent_id);
						if($is_active_pending_leave) {
							$pending_leave_array[] = ['agent_id_pending_leave'=>$target_agent_id,'team_id'=>$team_id,'tier'=>$next_tier_index+1];
						}


					}
					else {
						//ASSIGNMENT
						//pada prosesnya sekalian dicek pending leavenya ke siapa
						//nanti di akhir proses loop jika tidak ada agent satupun yang aktif,
						//maka pending_leave_array sudah tersimpan list agent yang cuti
						//maka ambil dalam array agent cuti di tier paling awal setelah current tier
						//kalau tidak ada maka nantinya notif bahwa belum terassign

						$agent_id = null;
						if($assign_type_list[$next_tier_index] == 1) {
							//echo "LOAD";
							$agent_id = loadBalance($team_id);
							$agent_id_pending_leave = loadBalance($team_id,"check_pending_leave");
							if(!empty($agent_id_pending_leave)) {
								//kalau ada tambahkan ke array, yang diambil yang tier paling awal
								$pending_leave_array[] = ['agent_id_pending_leave'=>$agent_id_pending_leave,'team_id'=>$team_id,'tier'=>$next_tier_index+1];
							}
						}
						else if($assign_type_list[$next_tier_index] == 2) {
							//echo "ROUND";
							$agent_id = roundRobin($team_id);
							$agent_id_pending_leave = roundRobin($team_id,"check_pending_leave");
							if(!empty($agent_id_pending_leave)) {
								//kalau ada tambahkan ke array, yang diambil yang tier paling awal
								$pending_leave_array[] = ['agent_id_pending_leave'=>$agent_id_pending_leave,'team_id'=>$team_id,'tier'=>$next_tier_index+1];
							}
						}
						else if($assign_type_list[$next_tier_index] == 3) {
							//echo "RANDOM";
							$agent_id = random($team_id);
							$agent_id_pending_leave = random($team_id,"check_pending_leave");
							if(!empty($agent_id_pending_leave)) {
								//kalau ada tambahkan ke array, yang diambil yang tier paling awal
								$pending_leave_array[] = ['agent_id_pending_leave'=>$agent_id_pending_leave,'team_id'=>$team_id,'tier'=>$next_tier_index+1];
							}
						}
						else if($assign_type_list[$next_tier_index] == 4) {
//echo "MANUAL";
							$target_agent_id = $team_id;//kalau manual maka isi team_id sebetulnya employee id yg terpilih
							$team_id = 0;//kosongkan

							$is_active = filterActiveEmployee($target_agent_id);
							if(!$is_active) {
								//echo "TIDAK AKTIF";
								$agent_id = null;
							} else {
								//echo "AKTIF";

								$agent_id = $target_agent_id;
								//var_dump($agent_id);
							}

							$is_active_pending_leave = filterActiveEmployeeForCheckingPendingLeave($target_agent_id);
							if($is_active_pending_leave) {
								$pending_leave_array[] = ['agent_id_pending_leave'=>$target_agent_id,'team_id'=>null,'tier'=>$next_tier_index+1];
							}
							//var_dump($agent_id);

						}
					}
					if(!empty($agent_id)) {
						//echo "ISBREK";
						break;//agent sudah ditemukan
					} else {
						//echo "GABREK";
					}

				}

				if(empty($agent_id)) {

					if(!empty($next_agent)) {
						//KALAU DI TIDAK ADA TIER BERIKUTNYA DI REQ MANAG MAKA BAKAL MASUK SINI
						//KALAU DIPILIH AGENT
						$target_agent_id = $next_agent;
						$team_id = null;
						$is_active = filterActiveEmployee($target_agent_id);
						if(!$is_active) {
							//echo "TIDAK AKTIF";
							$agent_id = null;
						} else {
							//echo "AKTIF";

							$agent_id = $target_agent_id;
							//var_dump($agent_id);
						}

						$is_active_pending_leave = filterActiveEmployeeForCheckingPendingLeave($target_agent_id);
						if($is_active_pending_leave) {
							$pending_leave_array[] = ['agent_id_pending_leave'=>$target_agent_id,'team_id'=>$team_id,'tier'=>$next_tier_index+1];
						}
						if(!empty($agent_id)) {
							return ['status'=>'ok','agent_id'=>$agent_id,'team_id'=>$team_id,'tier'=>$tier];

						} else if(!empty($pending_leave_array)) {
							//ada agent pending leave
							return ['status'=>'pending leave','agent_id'=>$pending_leave_array[0]['agent_id_pending_leave'],
										'team_id'=>$pending_leave_array[0]['team_id'],
										'tier'=>$pending_leave_array[0]['tier']];
						} else {
							return ['status'=>false,'message'=>"There's no available team for next tier. "];
						}
					} else if(!empty($pending_leave_array)) {
						//ada agent pending leave
						return ['status'=>'pending leave','agent_id'=>$pending_leave_array[0]['agent_id_pending_leave'],
									'team_id'=>$pending_leave_array[0]['team_id'],
									'tier'=>$pending_leave_array[0]['tier']];
					}
					else if(empty($pending_leave_array)) {
						return ['status'=>false,'message'=>"There's no available team for next tier. "];

					}
				} else {
					//
					return ['status'=>'ok','agent_id'=>$agent_id,'team_id'=>$team_id,'tier'=>$tier];
				}

	}
}

if (! function_exists('getArrayCoverage')) {
	function getArrayCoverage($coverage_windows) {

				$coverage_hours = $coverage_windows->coverage_hours;
				$list_cov = explode(",",$coverage_hours);
				$ar_cov = [];

				$list_cov = array_map('trim', $list_cov);

				$new_list_cov = [];
				foreach($list_cov as $cov) {
					$s = explode(" - ",$cov);
					$a = explode(":",$s[0]);
					//echo "<pre>";
					//var_dump($a);
					//echo "</pre>";
					if(!empty($a[1])) {
						$new_key = $a[0].":".sprintf("%02d", $a[1]).":".sprintf("%02d", $a[2]);
						$new_list_cov[$new_key] = $cov;
					}
				}
				//dd($new_list_cov);
				//die;

				ksort($new_list_cov);
				$list_cov = $new_list_cov;

				//echo "<pre>";
				//var_dump($list_cov);
				//var_dump($new_list_cov);
				//echo "</pre>";
				//die;
				foreach($list_cov as $cov) {

					if(empty(trim($cov))) {
						//skip karena kosong (string " ")

					} else if (str_contains($cov, '-')) {
						//range
						$d = explode(" - ",$cov);
						$e_start = explode(":",$d[0]);//contoh : 3:00:30
						$e_end = explode(":",$d[1]);//contoh : 4:30

						$day_number = $e_start[0];
						$start_hour = $e_start[1];
						$start_minute = $e_start[2];
						$end_hour = $e_end[0];
						$end_minute = $e_end[1];
						$duration = ($end_hour * 60 + $end_minute) - ($start_hour * 60 + $start_minute);

						$ar_cov[$day_number][] = ['day'=>$e_start[0],'start_hour'=>$start_hour,'start_minute'=>$start_minute,
															'end_hour'=>$end_hour,'end_minute'=>$end_minute,
															'duration'=> $duration,
										 ];

					} else {
						//range stengah jam saja
						$e_start = explode(":",$cov);
						//var_dump($cov);
						//die;
						$day_number = $e_start[0];
						$start_hour = $e_start[1];
						$start_minute = $e_start[2];

						$end_total = ($start_hour * 60 + $start_minute) + 30;
						$end_hour = floor($end_total / 60);
						$end_minute = $end_total % 60;
						$ar_cov[$day_number][] = ['day'=>$day_number,'start_hour'=>$start_hour,'start_minute'=>$start_minute,
														'end_hour'=>$end_hour,'end_minute'=>$end_minute,
														'duration'=> 30,
										];

					}

				}
		return $ar_cov;
	}
}


if (!function_exists('getObjectValue')) {
	function getObjectValue($type,$id) {

		if(in_array($type,['job_title'])) {
			return DB::table('job_title')
						->where('id',$id)->value('job_name');
		}
		if(in_array($type,['position'])) {
			return DB::table('position')
						->where('id',$id)->value('position_name');
		}

		if(in_array($type,['employee','person','team'])) {
			return DB::table('contact')->where('type',ucfirst($type))
						->where('id',$id)->value('name');
		}

		if(in_array($type,['location','sla','organization','company','service','service category','faq','faq category',])) {
			//echo "masuk";
			$type = str_replace(" ","_",strtolower($type));
			return DB::table($type)->where('id',$id)->value('name');

		}
		return $id;//$id as value
	}
}
if (!function_exists('t')) {
	function t($table){
		if($table == "slt") {
			return "SLT";
		} else if($table == "sla") {
			return "SLA";
		} else if($table == "sla_priority") {
			return "SLA Priority";
		} else {
			$ret = ucwords(str_replace("_"," ",$table));
		}
		return $ret;
	}

}
if (!function_exists('denied')) {
	function denied(){
			echo "

			 <!--<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>-->
			<style>
			.alert-danger {

				color: #a94442;
				background-color: #f2dede;
				border-color: #ebccd1;
			}
			button.close {
				-webkit-appearance: none;
				padding: 0;
				cursor: pointer;
				background: 0 0;
				border: 0;
			}
			.close {
				float: right;
				font-size: 21px;
				font-weight: 700;
				line-height: 1;
				color: #000;
				text-shadow: 0 1px 0 #fff;
				filter: alpha(opacity=20);
				opacity: .2;
			}
			.alert {
				padding: 15px;
				border: 1px solid transparent;
				border-radius: 4px;
				margin: 20px 10px 50px 10px;
			}
			</style>";
			echo '<div class="alert alert-danger" > Permission Denied <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>';
			die;
	}
}
if (!function_exists('accessv')) {
	function accessv($type,$sub_action,$return_value = ""){
		return TRUE;
		//return FALSE;
	}
}
if (!function_exists('accessv2')) {
	function accessv2($module,$action,$return_value = ""){
		//return TRUE;
		//echo Auth::user()->id.Auth::user()->name;
		if($module == "problem") {
			return TRUE;
		}
		if (in_array($module, ["ticket_series_01","profile"])) {
			return TRUE;
		}
		if (in_array($module, ["ticket"])) {
			if(empty($return_value)) {
				//dari ajax
				return TRUE;
			}
		}
		if (in_array($module, ["holiday_calendar"])) {
			$module = 'holiday';
		}
		if (in_array($module, ["sla_priority"])) {
			$module = 'sla';
		}
		if (in_array($module, DB::table('asset_management')->pluck('code','code')->toArray()+["asset"])) {
			$module = "asset_management";
		}
		if(in_array($module,DB::table('organization_level')->pluck('name','name')->toArray())){
			$module = "organization";
		}

		$list_roles = DB::table('lnkuserstoroles')
						->where('users_id',Auth::user()->id)->get();
		//var_dump($list_roles->toArray());
		$has_access = false;
		foreach($list_roles as $role) {
			//dd($role);
			$permission_access = DB::table('authorization')
							->where(['module'=>$module,
									'action'=>$action,
									'role_id'=>$role->roles_id
									])->first();
			//dd(['module'=>$module,
									//'action'=>$action,
									//'role_id'=>$role->roles_id
									//]);
			if($permission_access) {
				//echo "HASS";
				$has_access = true;
			}
		}
		//die;
		if($has_access) {
			return TRUE;
		}


		if($return_value) {
			return FALSE;
		} else {
			echo "

			 <!--<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>-->
			<style>
			.alert-danger {

				color: #a94442;
				background-color: #f2dede;
				border-color: #ebccd1;
			}
			button.close {
				-webkit-appearance: none;
				padding: 0;
				cursor: pointer;
				background: 0 0;
				border: 0;
			}
			.close {
				float: right;
				font-size: 21px;
				font-weight: 700;
				line-height: 1;
				color: #000;
				text-shadow: 0 1px 0 #fff;
				filter: alpha(opacity=20);
				opacity: .2;
			}
			.alert {
				padding: 15px;
				border: 1px solid transparent;
				border-radius: 4px;
				margin: 20px 10px 50px 10px;
			}
			</style>";
			echo '<div class="alert alert-danger" > Permission Denied <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>';
			die;
		}

		//return FALSE;
	}
}


if ( ! function_exists('form_open'))
{
	/**
	 * Form Declaration
	 *
	 * Creates the opening portion of the form.
	 *
	 * @param	string	the URI segments of the form destination
	 * @param	array	a key/value pair of attributes
	 * @param	array	a key/value pair hidden data
	 * @return	string
	 */
	function form_open($action = '', $attributes = array(), $hidden = array())
	{


		// If no action is provided then set to the current url
		if ( ! $action)
		{
			$action = url()->current();;
		}
		// If an action is not a full URL then turn it into one
		elseif (strpos($action, '://') === FALSE)
		{
			$action = url($action);
		}

		$attributes = _attributes_to_string($attributes);

		if (stripos($attributes, 'method=') === FALSE)
		{
			$attributes .= ' method="post"';
		}

		if (stripos($attributes, 'accept-charset=') === FALSE)
		{
			$attributes .= ' accept-charset="'.strtolower('UTF-8').'"';
		}

		$form = '<form action="'.$action.'"'.$attributes.">\n";

		if (is_array($hidden))
		{
			foreach ($hidden as $name => $value)
			{
				$form .= '<input type="hidden" name="'.$name.'" value="'.html_escape($value).'" />'."\n";
			}
		}



		return $form;
	}
}
// ------------------------------------------------------------------------

if ( ! function_exists('form_close'))
{
	/**
	 * Form Close Tag
	 *
	 * @param	string
	 * @return	string
	 */
	function form_close($extra = '')
	{
		return '</form>'.$extra;
	}
}


if ( ! function_exists('_attributes_to_string'))
{
	/**
	 * Attributes To String
	 *
	 * Helper function used by some of the form helpers
	 *
	 * @param	mixed
	 * @return	string
	 */
	function _attributes_to_string($attributes)
	{
		if (empty($attributes))
		{
			return '';
		}

		if (is_object($attributes))
		{
			$attributes = (array) $attributes;
		}

		if (is_array($attributes))
		{
			$atts = '';

			foreach ($attributes as $key => $val)
			{
				$atts .= ' '.$key.'="'.$val.'"';
			}

			return $atts;
		}

		if (is_string($attributes))
		{
			return ' '.$attributes;
		}

		return FALSE;
	}
}

if ( ! function_exists('html_escape'))
{
	/**
	 * Returns HTML escaped variable.
	 *
	 * @param	mixed	$var		The input string or array of strings to be escaped.
	 * @param	bool	$double_encode	$double_encode set to FALSE prevents escaping twice.
	 * @return	mixed			The escaped string or array of strings as a result.
	 */
	function html_escape($var, $double_encode = TRUE)
	{
		if (empty($var))
		{
			return $var;
		}

		if (is_array($var))
		{
			foreach (array_keys($var) as $key)
			{
				$var[$key] = html_escape($var[$key], $double_encode);
			}

			return $var;
		}

		return htmlspecialchars($var, ENT_QUOTES, "UTF-8", $double_encode);
	}
}

if ( ! function_exists('_stringify_attributes'))
{
	/**
	 * Stringify attributes for use in HTML tags.
	 *
	 * Helper function used to convert a string, array, or object
	 * of attributes to a string.
	 *
	 * @param	mixed	string, array, object
	 * @param	bool
	 * @return	string
	 */
	function _stringify_attributes($attributes, $js = FALSE)
	{
		$atts = NULL;

		if (empty($attributes))
		{
			return $atts;
		}

		if (is_string($attributes))
		{
			return ' '.$attributes;
		}

		$attributes = (array) $attributes;

		foreach ($attributes as $key => $val)
		{
			$atts .= ($js) ? $key.'='.$val.',' : ' '.$key.'="'.$val.'"';
		}

		return rtrim($atts, ',');
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('form_dropdown'))
{
	/**
	 * Drop-down Menu
	 *
	 * @param	mixed	$data
	 * @param	mixed	$options
	 * @param	mixed	$selected
	 * @param	mixed	$extra
	 * @return	string
	 */
	function form_dropdown($data = '', $options = array(), $selected = array(), $extra = '', $not_sort = '')
	{
		$defaults = array();

		if (is_array($data))
		{
			if (isset($data['selected']))
			{
				$selected = $data['selected'];
				unset($data['selected']); // select tags don't have a selected attribute
			}

			if (isset($data['options']))
			{
				$options = $data['options'];
				unset($data['options']); // select tags don't use an options attribute
			}
		}
		else
		{
			$defaults = array('name' => $data);
		}

		is_array($selected) OR $selected = array($selected);
		is_array($options) OR $options = array($options);

		// If no selected state was submitted we will attempt to set it automatically
		if (empty($selected))
		{
			if (is_array($data))
			{
				if (isset($data['name'], $_POST[$data['name']]))
				{
					$selected = array($_POST[$data['name']]);
				}
			}
			elseif (isset($_POST[$data]))
			{
				$selected = array($_POST[$data]);
			}
		}

		$extra = _attributes_to_string($extra);

		$multiple = (count($selected) > 1 && stripos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';

		$form = '<select '.rtrim(_parse_form_attributes($data, $defaults)).$extra.$multiple.">\n";
		if($not_sort != "not sort") {
			asort($options, SORT_NATURAL | SORT_FLAG_CASE);
		}

		$s=0;
		foreach ($options as $key => $val)
		{
			if($s==0) {
				if($key !== "") {
					//Tambahkan pilihan -Select-
					if(isset($data['name'])) {
						$options = [''=>'-Select '.ucwords(str_replace("_"," ",$data['name'])).'-']+$options;
					} else if (isset($defaults['name'])) {
						$options = [''=>'-Select '.ucwords(str_replace("_"," ",$defaults['name'])).'-']+$options;
					} else {
						$options = [''=>'-Select-']+$options;
					}

				}
			}
			$s++;
		}
		foreach ($options as $key => $val)
		{
			$key = (string) $key;

			if (is_array($val))
			{
				if (empty($val))
				{
					continue;
				}

				$form .= '<optgroup label="'.$key."\">\n";

				foreach ($val as $optgroup_key => $optgroup_val)
				{
					$sel = in_array($optgroup_key, $selected) ? ' selected="selected"' : '';
					$form .= '<option value="'.html_escape($optgroup_key).'"'.$sel.'>'
						.(string) $optgroup_val."</option>\n";
				}

				$form .= "</optgroup>\n";
			}
			else
			{
				$form .= '<option value="'.html_escape($key).'"'
					.(in_array($key, $selected) ? ' selected="selected"' : '').'>'
					.(string) $val."</option>\n";
			}
		}

		return $form."</select>\n";
	}
}
if ( ! function_exists('_parse_form_attributes'))
{
	/**
	 * Parse the form attributes
	 *
	 * Helper function used by some of the form helpers
	 *
	 * @param	array	$attributes	List of attributes
	 * @param	array	$default	Default values
	 * @return	string
	 */
	function _parse_form_attributes($attributes, $default)
	{
		if (is_array($attributes))
		{
			foreach ($default as $key => $val)
			{
				if (isset($attributes[$key]))
				{
					$default[$key] = $attributes[$key];
					unset($attributes[$key]);
				}
			}

			if (count($attributes) > 0)
			{
				$default = array_merge($default, $attributes);
			}
		}

		$att = '';

		foreach ($default as $key => $val)
		{
			if ($key === 'value')
			{
				$val = html_escape($val);
			}
			elseif ($key === 'name' && ! strlen($default['name']))
			{
				continue;
			}

			$att .= $key.'="'.$val.'" ';
		}

		return $att;
	}
}
if ( ! function_exists('is_image'))
{
    function is_image($filename){
		$supported_image = array(
			'gif',
			'jpg',
			'jpeg',
			'png'
		);


		$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION)); // Using strtolower to overcome case sensitive
		if (in_array($ext, $supported_image)) {
			//echo "it's image";
			return TRUE;
		} else {
			return FALSE;
			//echo 'not image';
		}
	}
}

if ( ! function_exists('handleUpload'))
{
	function handleUpload($file,$filename="",$pathname = "") {
		$tujuan_upload = 'uploads';
		$file->move($tujuan_upload,$filename);

    if($file->getClientOriginalExtension() == 'pdf') {
      try {
        sanitizePdf(public_path("uploads/".$filename));
      } catch(\Exception $e) {
        // $file->delete('public/'.$tujuan_upload);
        throw new Exception($e->getMessage());
      }
    }

	}
}
if ( ! function_exists('thumbImage'))
{
	function thumbImage($image,$path) {
		$a = $image;
		//$img = file_get_contents($baseppa.str_replace(" ","%20",$d->image));
		if ((strpos($a, '.jpg') !== false) || (strpos($a, '.jpeg') !== false)
			|| (strpos($a, '.JPG') !== false) || (strpos($a, '.JPEG') !== false)) {
			$im = imagecreatefromjpeg($image);
		}
		if ((strpos($a, '.png') !== false) || (strpos($a, '.PNG') !== false)) {
			$im = imagecreatefrompng($image);
		}
		//$im = imagecreatefromstring($img);
		if(isset($im)){
		$width = imagesx($im);
		$height = imagesy($im);



		// Get new dimensions
		// list($width_orig, $height_orig) = getimagesize($filename);
		$newwidth = '300';//MAX WIDTH
		$newheight = '250';//MAX HEIGHT
		$ratio_orig = $width/$height;
		if ($newwidth/$newheight > $ratio_orig) {
		   $newwidth = $newheight*$ratio_orig;
		} else {
		   $newheight = $newwidth/$ratio_orig;
		}


		$thumb = imagecreatetruecolor($newwidth, $newheight);

		imagesavealpha($thumb, true);
		$color = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
		imagefill($thumb, 0, 0, $color);

		imagecopyresampled($thumb, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);



		//CEK FOLDER EKSIS KALO BELUM ADA BIKIN DULU SUPAYA TIDAK ERROR
		$p = explode('/',$image);
		imagepng($thumb,$path.$image); //save image as jpg
		imagedestroy($thumb);
		imagedestroy($im);
		}
	}
}
if ( ! function_exists('izrand'))
{
	function izrand($length = 32, $numeric = false) {

		$random_string = "";
		while(strlen($random_string)<$length && $length > 0) {
			if($numeric === false) {
				$randnum = mt_rand(0,61);
				$random_string .= ($randnum < 10) ?
					chr($randnum+48) : ($randnum < 36 ?
						chr($randnum+55) : chr($randnum+61));
			} else {
				$randnum = mt_rand(0,9);
				$random_string .= chr($randnum+48);
			}
		}
		return $random_string;
	}
}
if (!function_exists('date_format_garing')) {
	//fungsi convert dari 2020-12-22 menjadi 22/12/2020
	function date_format_garing($date) {
		if(!empty($date)) {
			//$arr = explode("-",$date);
			//$date = $arr[2]."/".$arr[1]."/".$arr[0];
			$datetime = new DateTime($date);
			$date = $datetime->format('d/m/Y');
		} else {
			$date = "";
		}
		return $date;
	}
}
if (!function_exists('date_format_strip')) {
	//fungsi convert dari 22/12/2020 menjadi 2020-12-22
	function date_format_strip($date) {
		if(!empty($date)) {
			$arr = explode("/",$date);
			$date = $arr[2]."-".$arr[1]."-".$arr[0];
		} else {
			$date = "";
		}
		return $date;
	}
}
if (!function_exists('updateRelation')) {
	function updateRelation($id,$table_main, $list_join_table,$input2) {

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
}

if (!function_exists('get_agents')) {
	function get_agents($tree, $root = 0) {
        $return = array();
        # Traverse the tree and search for direct children of the root
        foreach($tree as $child => $parent) {
            # A direct child is found
            //echo $parent->id; die();
            if($parent->parent == $root) {
                # Remove item from tree (we don't need to traverse this again)
                unset($tree[$child]);
                # Append the child into result array and parse its children
                $return[] = array(
                    'id' => $parent->id,
                    'job_name' => $parent->job_name,
                    'children' => get_agents($tree, $parent->id)
                );
            }
        }
        return empty($return) ? null : $return;
    }
}
if (!function_exists('validateDate')) {
	function validateDate($date, $format = 'Y-m-d')
	{
		$d = DateTime::createFromFormat($format, $date);
		// The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
		return $d && $d->format($format) === $date;
	}
}
if (!function_exists('get_bawahan')) {
	function get_bawahan($job_title_id) {

		$list_bawahan = [];
		$list_child_job_title = DB::table('job_title')->where('parent',$job_title_id)->get();

		if(!empty($list_child_job_title)) {
			foreach($list_child_job_title as $job_title) {
				$list_contact_bawahan = DB::table('contact')->where('job_title', $job_title->id)->get();
				if(!empty($list_contact_bawahan)) {
					foreach($list_contact_bawahan as $bawahan) {
						array_push($list_bawahan,$bawahan);
					}
				}
				$list_bawahan = array_merge($list_bawahan,get_bawahan($job_title->id));
			}
		}
		return empty($list_bawahan) ? [] : $list_bawahan;
    }
}

if (!function_exists('check_main_menu_access')) {
	function check_main_menu_access($list_menu = null){
        //return true;
		$has_access = false;
		if(!empty($list_menu)) {
			foreach($list_menu as $menu) {
				//salah satu puny akses maka main menu dibuka
				if(accessv($menu,'list','return')) {
					$has_access = true;
				}
			}
		}
		return $has_access;
	}
}

if (!function_exists('notif_to_all_needed_contact_inventory')) {
	function notif_to_all_needed_contact_inventory($id,$ticket,$title,$content,$type_notif,$list_id_contact){
			//$list_contact = getContactCaseJourney($ticket);
			//$contact = DB::table('contact')->select('id','name','email')->where('id',Auth::user()->person)->first();
			//$list_contact[$contact->id] = $contact;
			if(!empty($list_id_contact)) {
				//$list_id_contact = explode(",",$ticket->notif);
				$list_contact = DB::table('contact')->select('id','name','email')->whereIn('id',$list_id_contact)->get();
			}
			//var_dump($list_contact);die;
			$message = $content;
			$type = $type_notif;
			$ref_id = $id;
			$i = 0;
			$first_email = "";
			$list_cc = [];
			foreach($list_contact as $notif_contact) {
				$i++;
				//sendNotifEmail($notif_contact->id, $title, $content,$type_notif,$id);
				$contact_id = $notif_contact->id;


				$contact = DB::table('contact')
					->where('contact.id',$contact_id)
					->leftJoin('users', 'users.person' , '=', 'contact.id')
					->leftJoin('lnkuserstoroles', 'lnkuserstoroles.users_id', '=', 'users.id')
					->first();

				$email = $contact->email;
				if($contact_id == $ticket->created_by_contact) {
					$first_email = $email;//requester yang jadi to, sisanya cc
				} else {
					$list_cc[] = $email;
				}
				// if($i == 1) {
				// 	$first_email = $email;
				// } else {
				// 	$list_cc[] = $email;
				// }

				if(empty($email)) {
					//return ['status'=>FALSE, 'message'=>'Email Not Found'];
				}

				$user_id = DB::table('users')->where('person',$contact_id)->value('id');


				DB::table('notification_message')->insertGetId(
					[
						'title' => $title,
						'user_id'=>$user_id,
						'contact_id'=>$contact_id,
						'message'=>$message,
						'type'=>$type,
						'ref_id'=>$ref_id,
						'created_at'=>date("Y-m-d H:i:s"),
						'updated_at'=>date("Y-m-d H:i:s"),
						'token' => generateRandomString(40),
					]
				);

			}

			if(empty($first_email)) {
				$first_email = array_shift($list_cc);
			}

			if($type == 'goods_issue') {
				$data['ticket'] = DB::table('goods_issues')->where('id',$ref_id)->first();
				$data['ticket']->message = $message;
				$data['statuses'] = DB::table('goods_issue_approvals')->where('goods_issue_id', $ref_id)->get();
				$data['message'] = $message;
				$data['title'] = $title;

				$url_customer = env('MAIL_CUSTOMER_REDIRECT_URL').'/goods_issue/'.$data['ticket']->token;
				$url_agent = env('MAIL_AGENT_REDIRECT_URL').'/goods_issue/'.$data['ticket']->token;
				$data['redirect_url'] = $contact->roles_id == 11 ? $url_agent : $url_customer;

			} else {
				$data['ticket'] = DB::table('goods_receives')->where('id',$ref_id)->first();
				$data['ticket']->message = $message;
				$data['statuses'] = DB::table('goods_receive_approvals')->where('goods_receive_id', $ref_id)->get();
				$data['message'] = $message;
				$data['title'] = $title;

				$url_customer = env('MAIL_CUSTOMER_REDIRECT_URL').'/goods_receive/'.$data['ticket']->token;
				$url_agent = env('MAIL_AGENT_REDIRECT_URL').'/goods_receive/'.$data['ticket']->token;
				$data['redirect_url'] = $contact->roles_id == 11 ? $url_agent : $url_customer;

			}

			try {
				if($first_email) {
					Illuminate\Support\Facades\Mail::to($first_email)->cc($list_cc)->send(new App\Mail\EmailSendGoodsIssues($title,$data));
				}
			} catch (Exception $e) {
				//echo $e->getMessage()."<br/>";
			}


	}


	if(!function_exists('removeSpecialCharacters')) {
		function removeSpecialCharacters($input) {
			$pattern = '/[^a-zA-Z0-9 .]/';
			$result = preg_replace($pattern, '', $input);

			return $result;
		}
	}

	if(!function_exists('validateEditorContent')) {
		function validateEditorContent($content, int $minLength = 10): bool
		{
			if(!$content) return false;
			 $plainText = html_entity_decode(strip_tags($content));

			 $plainText = str_replace("\xc2\xa0", ' ', $plainText);

			 if (preg_match('/^(?:\s|\xc2\xa0){10,}/u', $plainText)) {
				 return false;
			 }

			 // Remove all spaces (regular and non-breaking) to count only meaningful characters
			 $textWithoutSpaces = preg_replace('/[\s\xc2\xa0]+/u', '', $plainText);

			 // Validate the length of the meaningful text (excluding spaces)
			 return mb_strlen($textWithoutSpaces) >= $minLength;
		}
	}
}

if(!function_exists('sanitize')) {
    function sanitize($input)
    {
      $allowedTags = '<p><a><strong><em><ul><li><img>';
      $cleanHtml = strip_tags($input, $allowedTags);

      return $cleanHtml;
    }
}

if(!function_exists('sanitizePdf')) {
  function sanitizePdf($pathname)
  {
    $parser = new Parser();
    try {
      $pdf = $parser->parseFile($pathname);
      $pdfText = $pdf->getText();

      // Check for malicious patterns
      if (preg_match('/<script|on\w+=|confirm\(|eval\(/i', $pdfText)) {
        throw new Exception('The PDF contains malicious content.');
      }
    } catch(Exception $e) {
      throw new Exception('The PDF contains malicious content.');
    }
  }
}
