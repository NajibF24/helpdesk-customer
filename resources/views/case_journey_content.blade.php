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
// $has_rejected = false;
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
								by {{$contact->name }}
							</i>
							<br><span>{{ date('M d, Y H:i', strtotime($ticket->created_at)) }}</span>
							</div>
							<!--end::Desc-->
						</div>
						<!--end::Item-->

						<?php
						if($ticket->finalclass != "incident_management") {
							//START : STEP APPROVAL
							//cara praktis dapetin output html approval
							getContactCaseJourney($ticket,"","","need_html_output_case_journey");
							//END : STEP APPROVAL
						}
						if(!in_array($ticket->status, ['Rejected', 'Withdrawn'])) {
						//ASSIGNMENT STEP
						$ticket_assignment_log = DB::table('ticket_assignment_log')->where('ticket_id',$ticket->id)->oldest('id')->get();
						//var_dump();
						if($ticket_assignment_log->count() <= 0) {
							//echo "KOSONG";
							$request_management = DB::table('request_management')->where('id',$ticket->request_management)->first();
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
									// $team = DB::table('contact')->where('id',$log->team_id)->first();
                                    $team = DB::table('lnkemployeetoteam')->where('employee_id', $log->agent_id)->leftJoin('contact', 'contact.id', 'lnkemployeetoteam.team_id')->first();
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
												echo e($agent->name) ?? "";
											}
											if(!empty($team->name)) {
												echo " (".e($team->name).")";
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
		$name_created = DB::table('users')->where('id', $ticket->created_by)->value('name');
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
		<div class="mt-3">
			<b>Created By / Submit By</b><br/>
			{{$name_created ?? ""}}
		</div>
		<a href="#" class="mt-5" style="display:block">
			Recent tickets
		</a>
	</div>
</div>

<?php //END : KODE SAMA HELPDESK DAN PORTAL ?>
