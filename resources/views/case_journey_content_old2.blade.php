<?php
use App\Helpers\TicketStatusHelper;
use Illuminate\Support\Facades\Auth;
 ?>
<style>
.case-journey i {
	font-size: 1.1rem;
}
</style>
<?php 
$has_rejected = false;
?>
            <!--begin::List Widget 9-->
            <div class="card card-custom gutter-b case-journey">
                <!--begin::Header-->
                <div class="card-header align-items-center border-0 mt-4">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="font-weight-bolder text-dark">Case's Journey</span>
                    </h3>
                </div>
                <!--end::Header-->
                <?php //START : KODE SAMA HELPDESK DAN PORTAL ?>
                <!--begin::Body-->
                <div class="card-body pt-4">
                    <!--begin::Timeline-->
                    <div class="timeline timeline-6 mt-3">
						<!--begin::Item-->
						<div class="timeline-item align-items-start">
							<!--begin::Label-->
							<div class="timeline-label font-weight-bolder text-dark-75 font-size-lg"></div>
							<!--end::Label-->
							<!--begin::Badge-->
							<div class="timeline-badge">
								<i class="fa fa-genderless text-success icon-xl"></i>
							</div>
							<!--end::Badge-->
							<!--begin::Desc-->
							<div class="timeline-content font-weight-bolder font-size-lg text-dark-75 pl-3">
								New
							<i class="text-light-50">
								
							</i>
							<br><span>{{ date('M d, Y H:i', strtotime($ticket->created_at)) }}</span>
							</div>
							<!--end::Desc-->
						</div>
						<!--end::Item-->
						
						<?php 
						if($ticket->finalclass != "incident_management") {
							//START : STEP APPROVAL
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
											$atasan = ['id'=>$contact->id,'name'=>$contact->name];
											?>
												<!--begin::Item-->
												<div class="timeline-item align-items-start">
													<!--begin::Label-->
													<div class="timeline-label font-weight-bolder text-dark-75 font-size-lg"></div>
													<!--end::Label-->
													<!--begin::Badge-->
													@if(!empty($ticket_approval->status))
														<?php 
														if ($ticket_approval->status == 'rejected') {
															$has_rejected = true;
														}
														?>
														<div class="timeline-badge">
															<i class="fa fa-genderless text-{{ TicketStatusHelper::status_round_color($ticket_approval->status) }} icon-xl"></i>
														</div>
													@else 
														<div class="timeline-badge">
															<i class="fa fa-genderless text-danger icon-xl"></i>
														</div>
													@endif
													<!--end::Badge-->
													<!--begin::Desc-->
													<div class="timeline-content font-weight-bolder font-size-lg text-dark-75 pl-3">
														@if(!empty($ticket_approval->status))
															{{ucfirst($ticket_approval->status)}}
														@else
															{{"Wait for Approval"}}
														@endif
													<i class="text-light-50" <?= popoverJobTitle($atasan['id']) ?> >
														{{"by " . $atasan['name']}}
													</i>
													
													@if(!empty($ticket_approval))
														<br><span>{{ date('M d, Y H:i', strtotime($ticket_approval->created_at)) }}</span>
													@else
														<br><span></span>
													@endif
													</div>
													<!--end::Desc-->
												</div>
												<!--end::Item-->
											
											<?php
											

											
											
											
										}
									}
								}
							} 
							else {
								$request_management = DB::table('request_management')->where('id', $ticket->request_management)->first();
								//var_dump($request_management);
								//APPROVAL USER
								if(empty($request_management->approval_user_custom)) {
									if(false && empty($request_management->max_user_superordinate)) {
										//dua duanya kosong
										//diskip

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
											foreach($semua_atasan as $atasan) {	
												if($atasan['position_id'] == $request_management->max_user_superordinate) {
													$max_user_ditemukan = true;
												}
											}
											if($max_user_ditemukan) {
												//user di bawah maxusersuperordinate
												foreach($semua_atasan as $atasan) {
													$ticket_approval = DB::table('ticket_approval')
																		->where('ticket_id',$ticket->id)
																		->where('approval_id',$atasan['contact_id'])
																		->first();
														//$approval_name = DB::table('users')->where('id', $status->created_by)->first();
													if(!$has_rejected) {
														?>
														<!--begin::Item-->
														<div class="timeline-item align-items-start">
															<!--begin::Label-->
															<div class="timeline-label font-weight-bolder text-dark-75 font-size-lg"></div>
															<!--end::Label-->
															<!--begin::Badge-->
															@if(!empty($ticket_approval->status))
																<?php 
																if ($ticket_approval->status == 'rejected') {
																	$has_rejected = true;
																}
																?>
																<div class="timeline-badge">
																	<i class="fa fa-genderless text-{{ TicketStatusHelper::status_round_color($ticket_approval->status) }} icon-xl"></i>
																</div>
															@else 
																<div class="timeline-badge">
																	<i class="fa fa-genderless text-danger icon-xl"></i>
																</div>
															@endif
															<!--end::Badge-->
															<!--begin::Desc-->
															<div class="timeline-content font-weight-bolder font-size-lg text-dark-75 pl-3">
																@if(!empty($ticket_approval->status))
																	{{ucfirst($ticket_approval->status)}}
																@else
																	{{"Wait for Approval"}}
																@endif
															<i class="text-light-50"  <?= popoverJobTitle($atasan['contact_id']) ?> >
																{{"by " . $atasan['name']}}
															</i>
															
															@if(!empty($ticket_approval))
																<br><span>{{ date('M d, Y H:i', strtotime($ticket_approval->created_at)) }}</span>
															@else
																<br><span></span>
															@endif
															</div>
															<!--end::Desc-->
														</div>
														<!--end::Item-->
														<?php
													}
													if($atasan['position_id'] == $request_management->max_user_superordinate) {
														//UDAH MAKSIMUM
														break;
													}
												}
											} 
											else {
												//user di atas atau sama dgn maxusersuperordinate
												//atau kondisinya tidak ditemukan maxusersuperordinate
												foreach($semua_atasan as $atasan) {
													$ticket_approval = DB::table('ticket_approval')
																		->where('ticket_id',$ticket->id)
																		->where('approval_id',$atasan['contact_id'])
																		->first();
													if(!$has_rejected) {
														?>
														<!--begin::Item-->
														<div class="timeline-item align-items-start">
															<!--begin::Label-->
															<div class="timeline-label font-weight-bolder text-dark-75 font-size-lg"></div>
															<!--end::Label-->
															<!--begin::Badge-->
															@if(!empty($ticket_approval->status))
																<?php 
																if ($ticket_approval->status == 'rejected') {
																	$has_rejected = true;
																}
																?>
																<div class="timeline-badge">
																	<i class="fa fa-genderless text-{{ TicketStatusHelper::status_round_color($ticket_approval->status) }} icon-xl"></i>
																</div>
															@else 
																<div class="timeline-badge">
																	<i class="fa fa-genderless text-danger icon-xl"></i>
																</div>
															@endif
															<!--end::Badge-->
															<!--begin::Desc-->
															<div class="timeline-content font-weight-bolder font-size-lg text-dark-75 pl-3">
																@if(!empty($ticket_approval->status))
																	{{ucfirst($ticket_approval->status)}}
																@else
																	{{"Wait for Approval"}}
																@endif
															<i class="text-light-50"  <?= popoverJobTitle($atasan['contact_id']) ?> >
																{{"by " . $atasan['name']}}
															</i>
															
															@if(!empty($ticket_approval))
																<br><span>{{ date('M d, Y H:i', strtotime($ticket_approval->created_at)) }}</span>
															@else
																<br><span></span>
															@endif
															</div>
															<!--end::Desc-->
														</div>
														<!--end::Item-->
														<?php
													}
													//UDAH DAPAT 1 ATASAN LANGSUNG BREAK
													break;
												}
											}

										}

									}
								} 
								else {
									//approval_user_custom
									
									$list_approval_user_custom = explode(",",$request_management->approval_user_custom);
									
									for($i=0;$i<count($list_approval_user_custom);$i++) {
										$contact = DB::table('contact')
											->where('contact.status', '=', 'Active')
											->whereNull('contact.deleted_at')
											->where('contact.job_title',$list_approval_user_custom[$i])->first();
										if($contact) {
											$atasan = ['id'=>$contact->id,'name'=>$contact->name];
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
										} else {
											$ticket_approval = null;
										}
										if(!empty($atasan) && !$has_rejected) {
										?>
											<!--begin::Item-->
											<div class="timeline-item align-items-start">
												<!--begin::Label-->
												<div class="timeline-label font-weight-bolder text-dark-75 font-size-lg"></div>
												<!--end::Label-->
												<!--begin::Badge-->
												@if(!empty($ticket_approval->status))
													<?php 
													if ($ticket_approval->status == 'rejected') {
														$has_rejected = true;
													}
													?>
													<div class="timeline-badge">
														<i class="fa fa-genderless text-{{ TicketStatusHelper::status_round_color($ticket_approval->status) }} icon-xl"></i>
													</div>
												@else 
													<div class="timeline-badge">
														<i class="fa fa-genderless text-danger icon-xl"></i>
													</div>
												@endif
												<!--end::Badge-->
												<!--begin::Desc-->
												<div class="timeline-content font-weight-bolder font-size-lg text-dark-75 pl-3">
													@if(!empty($ticket_approval->status))
														{{ucfirst($ticket_approval->status)}}
													@else
														{{"Wait for Approval"}}
													@endif
												<i class="text-light-50">
													{{"by " . $atasan['name']}}
												</i>
												
												@if(!empty($ticket_approval))
													<br><span>{{ date('M d, Y H:i', strtotime($ticket_approval->created_at)) }}</span>
												@else
													<br><span></span>
												@endif
												</div>
												<!--end::Desc-->
											</div>
											<!--end::Item-->
										
										<?php
										}
									}

									if(false) {
									//$list_approval_user_custom = explode(",",$request_management->approval_user_custom);
									//$semua_atasan = cekSemuaAtasan($ticket->requester);

									//foreach($list_approval_user_custom as $auc_position_id) {
										////var_dump($auc_position_id);
										//if(!empty($semua_atasan)) {
										//foreach($semua_atasan as $atasan) {
												////var_dump($atasan);
												//if($atasan['position_id'] == $auc_position_id) {


														//$ticket_approval = DB::table('ticket_approval')
																			//->where('ticket_id',$ticket->id)
																			//->where('approval_id',$atasan['contact_id'])
																			//->first();
															//$approval_name = DB::table('users')->where('id', $status->created_by)->first();
														?>
														<!--begin::Item-->
														<div class="timeline-item align-items-start">
															<!--begin::Label-->
															<div class="timeline-label font-weight-bolder text-dark-75 font-size-lg"></div>
															<!--end::Label-->
															<!--begin::Badge-->
															@if(!empty($ticket_approval->status))
																<div class="timeline-badge">
																	<i class="fa fa-genderless text-{{ TicketStatusHelper::status_round_color($ticket_approval->status) }} icon-xl"></i>
																</div>
															@else 
																<div class="timeline-badge">
																	<i class="fa fa-genderless text-danger icon-xl"></i>
																</div>
															@endif
															<!--end::Badge-->
															<!--begin::Desc-->
															<div class="timeline-content font-weight-bolder font-size-lg text-dark-75 pl-3">
																@if(!empty($ticket_approval->status))
																	{{ucfirst($ticket_approval->status)}}
																@else
																	{{"Wait for Approval"}}
																@endif
															<i class="text-light-50">
																{{"by " . $atasan['name']}}
															</i>
															
															@if(!empty($ticket_approval))
																<br><span>{{ date('M d, Y H:i', strtotime($ticket_approval->created_at)) }}</span>
															@else
																<br><span></span>
															@endif
															</div>
															<!--end::Desc-->
														</div>
														<!--end::Item-->
														<?php
													
												//}
										//}
										//}
									//}
									}
									//////////////////////
								}
								//APPROVAL SUPPORT
								
								if(empty($request_management->approval_support_custom)) {
									if(false && empty($request_management->max_support_approval)) {
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
											
											//$list_employee_team = DB::table('lnkemployeetoteam')->where('team_id',$team_id)->get();
											//$job_title = null;
											//$selected_contact_employee;
											//foreach($list_employee_team as $et) {
												//$employee = DB::table('contact')->where('id',$et->employee_id)->first();
												//if(!empty($employee->job_title)) {
													////$job_title_id = $employee->job_title;
													//$selected_contact_employee = $employee;
												//}
											//}
											$ret_val = getEmployeeJabatanTerbawah($team_id);
											$job_title_id = $ret_val[0];
											$employee = $ret_val[1];
											
											$selected_contact_employee = $employee;
										}
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
												foreach($semua_atasan as $atasan) {
													//var_dump($atasan);
													//echo "SSS";
													$ticket_approval = DB::table('ticket_approval')
																		->where('ticket_id',$ticket->id)
																		->where('approval_id',$atasan['contact_id'])
																		->first();
													if(!$has_rejected) {
														?>
														
														<!--begin::Item-->
														<div class="timeline-item align-items-start">
															<!--begin::Label-->
															<div class="timeline-label font-weight-bolder text-dark-75 font-size-lg"></div>
															<!--end::Label-->
															<!--begin::Badge-->
															@if(!empty($ticket_approval->status))
																<?php 
																if ($ticket_approval->status == 'rejected') {
																	$has_rejected = true;
																}
																?>
																<div class="timeline-badge">
																	<i class="fa fa-genderless text-{{ TicketStatusHelper::status_round_color($ticket_approval->status) }} icon-xl"></i>
																</div>
															@else 
																<div class="timeline-badge">
																	<i class="fa fa-genderless text-danger icon-xl"></i>
																</div>
															@endif
															<!--end::Badge-->
															<!--begin::Desc-->
															<div class="timeline-content font-weight-bolder font-size-lg text-dark-75 pl-3">
																@if(!empty($ticket_approval->status))
																	{{ucfirst($ticket_approval->status)}}
																@else
																	{{"Wait for Approval"}}
																@endif
															<i class="text-light-50" <?= popoverJobTitle($atasan['contact_id']) ?> >
																{{"by " . $atasan['name']}} 
															</i>
															
															@if(!empty($ticket_approval))
																<br><span>{{ date('M d, Y H:i', strtotime($ticket_approval->created_at)) }}</span>
															@else
																<br><span></span>
															@endif
															</div>
															<!--end::Desc-->
														</div>
														<!--end::Item-->
														
														<?php
													}
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

													if(!$has_rejected) {
														?>
														
														<!--begin::Item-->
														<div class="timeline-item align-items-start">
															<!--begin::Label-->
															<div class="timeline-label font-weight-bolder text-dark-75 font-size-lg"></div>
															<!--end::Label-->
															<!--begin::Badge-->
															@if(!empty($ticket_approval->status))
																<?php 
																if ($ticket_approval->status == 'rejected') {
																	$has_rejected = true;
																}
																?>
																<div class="timeline-badge">
																	<i class="fa fa-genderless text-{{ TicketStatusHelper::status_round_color($ticket_approval->status) }} icon-xl"></i>
																</div>
															@else 
																<div class="timeline-badge">
																	<i class="fa fa-genderless text-danger icon-xl"></i>
																</div>
															@endif
															<!--end::Badge-->
															<!--begin::Desc-->
															<div class="timeline-content font-weight-bolder font-size-lg text-dark-75 pl-3">
																@if(!empty($ticket_approval->status))
																	{{ucfirst($ticket_approval->status)}}
																@else
																	{{"Wait for Approval"}}
																@endif
															<i class="text-light-50" <?= popoverJobTitle($atasan['contact_id']) ?> >
																{{"by " . $atasan['name']}} 
															</i>
															
															@if(!empty($ticket_approval))
																<br><span>{{ date('M d, Y H:i', strtotime($ticket_approval->created_at)) }}</span>
															@else
																<br><span></span>
															@endif
															</div>
															<!--end::Desc-->
														</div>
														<!--end::Item-->
														
														<?php
													}
													//UDAH DAPAT 1 ATASAN LANGSUNG BREAK
													break;
												}
											}
										//$contact_yang_login = DB::table('contact')->where('id',Auth::user()->person)->first();
										
										//echo "stepA";
										//if(empty($job_title_id)) {
											////echo "stepB";
											////tidak bisa diproses, employee tidak tercatat job titlenya
											////langsung ke assignment
											//$next_is_assignment = 1;
										//} 

									}
										}
								} 
								else {
									//approval_support_custom
									$list_approval_support_custom = explode(",",$request_management->approval_support_custom);
									
									for($i=0;$i<count($list_approval_support_custom);$i++) {
										$contact = DB::table('contact')
														->where('contact.status', '=', 'Active')
														->whereNull('contact.deleted_at')
														->where('contact.job_title',$list_approval_support_custom[$i])->first();
										if($contact) {
											$atasan = ['id'=>$contact->id,'name'=>$contact->name];
										} else {
											$need_check_jobtitle = $list_approval_support_custom[$i];
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
										} else {
											$ticket_approval = null;
										}
										if(!empty($atasan) && !$has_rejected) {
										?>
											<!--begin::Item-->
											<div class="timeline-item align-items-start">
												<!--begin::Label-->
												<div class="timeline-label font-weight-bolder text-dark-75 font-size-lg"></div>
												<!--end::Label-->
												<!--begin::Badge-->
												@if(!empty($ticket_approval->status))
													<?php 
													if ($ticket_approval->status == 'rejected') {
														$has_rejected = true;
													}
													?>
													<div class="timeline-badge">
														<i class="fa fa-genderless text-{{ TicketStatusHelper::status_round_color($ticket_approval->status) }} icon-xl"></i>
													</div>
												@else 
													<div class="timeline-badge">
														<i class="fa fa-genderless text-danger icon-xl"></i>
													</div>
												@endif
												<!--end::Badge-->
												<!--begin::Desc-->
												<div class="timeline-content font-weight-bolder font-size-lg text-dark-75 pl-3">
													@if(!empty($ticket_approval->status))
														{{ucfirst($ticket_approval->status)}}
													@else
														{{"Wait for Approval"}}
													@endif
												<i class="text-light-50" <?= popoverJobTitle($atasan['id']) ?> >
													{{"by " . $atasan['name']}}
												</i>
												
												@if(!empty($ticket_approval))
													<br><span>{{ date('M d, Y H:i', strtotime($ticket_approval->created_at)) }}</span>
												@else
													<br><span></span>
												@endif
												</div>
												<!--end::Desc-->
											</div>
											<!--end::Item-->
										
										<?php
										}
									}
								}
							}
							//END : STEP APPROVAL
						}
						if(!$has_rejected) {
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
										?>
										<!--begin::Item-->
										<div class="timeline-item align-items-start">
											<!--begin::Label-->
											<div class="timeline-label font-weight-bolder text-dark-75 font-size-lg"></div>
											<!--end::Label-->
											<!--begin::Badge-->
												<div class="timeline-badge">
													<i class="fa fa-genderless text-warning icon-xl"></i>
												</div>
											<!--end::Badge-->
											<!--begin::Desc-->
											<div class="timeline-content font-weight-bolder font-size-lg text-dark-75 pl-3">
												{{"Pending Assignment"}}
											<i class="text-light-50"  >
												{{"to ".$contact->name }}
											</i>
											<br><span></span>
											</div>
											<!--end::Desc-->
										</div>
										<!--end::Item-->							
										<?php
									}
								}
							}
						}
						else {
							foreach($ticket_assignment_log as $log) {
									$team = DB::table('contact')->where('id',$log->team_id)->first();
									$agent = DB::table('contact')->where('id',$log->agent_id)->first();
									
									?>
									<!--begin::Item-->
									<div class="timeline-item align-items-start">
										<!--begin::Label-->
										<div class="timeline-label font-weight-bolder text-dark-75 font-size-lg"></div>
										<!--end::Label-->
										<!--begin::Badge-->
											<div class="timeline-badge">
												<i class="fa fa-genderless text-warning icon-xl"></i>
											</div>
										<!--end::Badge-->
										<!--begin::Desc-->
										<div class="timeline-content font-weight-bolder font-size-lg text-dark-75 pl-3">
											{{$log->status}}
										<i class="text-light-50"  <?= popoverJobTitle($agent->id ?? -3) ?>>
											<?php 
											if($log->status == "Escalated"
												|| $log->status == "Resolved"
												|| $log->status == "Change Agent"
											) {
												echo " by ";
											}
											if($log->status == "Assignment") {
												echo " to ";
											}
											if($log->agent_id == -1 || $log->agent_id == -3) {
												echo "System";
											} else {
												echo $agent->name ?? "";
											}
											if(!empty($team->name)) {
												echo " (".$team->name.")";
											}
											?>
										</i>
										<br><span>{{ date('M d, Y H:i', strtotime($log->created_at)) }}</span>
										</div>
										<!--end::Desc-->
									</div>
									<!--end::Item-->							
									<?php								
							}
						}
						?>
						
                        @if (false && count($statuses) > 0) 
                            @foreach ($statuses as $status)
                            <!--begin::Item-->
                            <div class="timeline-item align-items-start">
                                <!--begin::Label-->
                                <div class="timeline-label font-weight-bolder text-dark-75 font-size-lg"></div>
                                <!--end::Label-->
                                <!--begin::Badge-->
                                <div class="timeline-badge">
                                    <i class="fa fa-genderless text-{{ TicketStatusHelper::status_round_color($status->status) }} icon-xl"></i>
                                </div>
                                <!--end::Badge-->
                                <!--begin::Desc-->
                                <div class="timeline-content font-weight-bolder font-size-lg text-dark-75 pl-3">{{ TicketStatusHelper::status_name($status->status) }} 
                                <i class="text-light-50">
                                    <?php
                                        $approval_name = DB::table('users')->where('id', $status->created_by)->first();
                                    ?>

                                    {{ $status->status != "new" ? "by " . $approval_name->name : "" }}
                                </i>
                                <br><span>{{ date('M d, Y H:i', strtotime($status->created_at)) }}</span></div>
                                <!--end::Desc-->
                            </div>
                            <!--end::Item-->
                            @endforeach
                        @endif
                        <?php } //end hasrejected?>
                    </div>
                    <!--end::Timeline-->
                </div>
                <!--end: Card Body-->
            </div>
            <!--end: List Widget 9-->

<!--begin::List Widget 9-->
<div class="card  gutter-b">
	<!--begin::Header-->
	<div class="card-header align-items-center border-0 mt-4 pb-0">
		<h3 class="card-title align-items-start flex-column mb-0">
			<span class="font-weight-bolder text-dark">Requester Info</span>
		</h3>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body pt-4">
		<?php
		$name = DB::table('users')->where('id', $ticket->created_by)->value('name');

		$contact = DB::table('contact')->where('id',$ticket->requester)->first();
		if(!empty($contact->name)) {
			$name = $contact->name;
		}
		$job_name = "";
		$email = "";
		if(!empty($contact->job_title)) {
			$job_name = DB::table('job_title')->where('id',$contact->job_title)->value('job_name');
		}
		?>
		<img class="" src="{{URL('/')}}/template1/src/media/users/default.jpg" style="height:50px;border-radius:10px;"/>
		<div style="margin-left:5px;display: inline-block;vertical-align: middle;">
			<a href="#">{{$name}}</a><br/>
			{{$job_name}}
		</div>
		<div class="mt-3">
			<b>Email</b><br/>
			{{$contact->email ?? ""}}
		</div>
		<a href="#" class="mt-5" style="display:block">
			Recent tickets
		</a>
	</div>
</div>

<?php //END : KODE SAMA HELPDESK DAN PORTAL ?>
