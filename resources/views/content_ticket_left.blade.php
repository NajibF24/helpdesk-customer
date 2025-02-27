            <?php

            //$ticket->assign_time = "2021-05-21 17:30:00";
            //echo $ticket->assign_time;
            //echo checkDueDate($ticket->id,$ticket->assign_time);
            //echo checkEscalationDate($ticket->id,$ticket->assign_time,5,"Hours");

            ?>

            <!--begin::List Widget 8-->
			<div class="card card-custom gutter-b">
				<div class="card-body">
					<!--begin::Top-->
					<div class="d-flex">
						<!--begin::Pic-->
						<div class="flex-shrink-0 mr-7">
							<div class="symbol symbol-50 symbol-lg-90 symbol-light-primary">
								<span class="font-size-h3 symbol-label font-weight-boldest">{{acronym($name ?? "")}}</span>
							</div>
						</div>
						<!--end::Pic-->
						<!--begin: Info-->
						<div class="flex-grow-1">
							<!--begin::Title-->
							<div class="d-flex align-items-center justify-content-between flex-wrap mt-2">
								<!--begin::User-->
								<div class="mr-3">
									<!--begin::Name-->
									<h2>{{ $ticket->title }}</h2>
									<span class="d-flex align-items-center text-dark text-hover-primary font-size-h5 font-weight-bold mr-3">


										{{ $name }}


									<i class="flaticon2-correct text-success icon-md ml-2"></i></span>
									<!--end::Name-->
									@if ($ticket->status == "Closed" && $ticket->rating)
									<span class="d-flex align-items-center text-dark text-hover-primary font-size-h5 font-weight-bold mr-3">
										@for ($i= 1; $i <= $ticket->rating; $i++)
										<span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\General\Star.svg-->
											<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
												<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
													<polygon points="0 0 24 0 24 24 0 24"/>
													<path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
												</g>
											</svg><!--end::Svg Icon-->
										</span>
										@endfor
									</span>
									@endif
									<!--begin::Contacts-->
									<div class="d-flex flex-wrap my-2">

										<span class="text-muted text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2">
										<span class="flaticon2-chronometer icon-md icon-gray-500 mr-1"></span>
										{{date('d M Y H:i', strtotime($ticket->created_at))}}</span>
										<span class="text-muted text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2">
										<span class="svg-icon svg-icon-md svg-icon-gray-500 mr-1">
											<!--begin::Svg Icon | path:assets/media/svg/icons/General/Lock.svg-->
											<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
												<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
													<mask fill="white">
														<use xlink:href="#path-1"></use>
													</mask>
													<g></g>
													<path d="M7,10 L7,8 C7,5.23857625 9.23857625,3 12,3 C14.7614237,3 17,5.23857625 17,8 L17,10 L18,10 C19.1045695,10 20,10.8954305 20,12 L20,18 C20,19.1045695 19.1045695,20 18,20 L6,20 C4.8954305,20 4,19.1045695 4,18 L4,12 C4,10.8954305 4.8954305,10 6,10 L7,10 Z M12,5 C10.3431458,5 9,6.34314575 9,8 L9,10 L15,10 L15,8 C15,6.34314575 13.6568542,5 12,5 Z" fill="#000000"></path>
												</g>
											</svg>
											<!--end::Svg Icon-->
										</span>{{$job_name}}</span>
										<span class="text-muted text-hover-primary font-weight-bold">
										<span class="svg-icon svg-icon-md svg-icon-gray-500 mr-1">
											<!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Mail-notification.svg-->
											<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
												<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
													<rect x="0" y="0" width="24" height="24"></rect>
													<path d="M21,12.0829584 C20.6747915,12.0283988 20.3407122,12 20,12 C16.6862915,12 14,14.6862915 14,18 C14,18.3407122 14.0283988,18.6747915 14.0829584,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,8 C3,6.8954305 3.8954305,6 5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,12.0829584 Z M18.1444251,7.83964668 L12,11.1481833 L5.85557487,7.83964668 C5.4908718,7.6432681 5.03602525,7.77972206 4.83964668,8.14442513 C4.6432681,8.5091282 4.77972206,8.96397475 5.14442513,9.16035332 L11.6444251,12.6603533 C11.8664074,12.7798822 12.1335926,12.7798822 12.3555749,12.6603533 L18.8555749,9.16035332 C19.2202779,8.96397475 19.3567319,8.5091282 19.1603533,8.14442513 C18.9639747,7.77972206 18.5091282,7.6432681 18.1444251,7.83964668 Z" fill="#000000"></path>
													<circle fill="#000000" opacity="0.3" cx="19.5" cy="17.5" r="2.5"></circle>
												</g>
											</svg>
											<!--end::Svg Icon-->
										</span>{{$contact->email ?? ""}}</span>
									</div>
									<!--end::Contacts-->
								</div>
								<!--begin::User-->
								<!--begin::Actions-->
								<div class="my-lg-0 my-1" hidden>
									<a href="#" class="btn btn-sm btn-white-line3"><span class="far fa-star"></span></a>
									<a href="#" class="btn btn-sm btn-white-line3 ml-1 mr-2"><span class="far fa-trash-alt"></span></a>
								</div>
								<!--end::Actions-->
							</div>
							<!--end::Title-->
							<!--begin::Content-->
							<div class="d-none align-items-center flex-wrap justify-content-between">
								<!--begin::Description-->
								<div class="flex-grow-1  text-dark-75 py-2 py-lg-2 mr-5">
									Hi, team <br/><br/>
								<?= $ticket->description ?>
									<br/><br/>
									Regards,<br/>
									{{ $name }}
								</div>
								<!--end::Description-->






							</div>
							<!--end::Content-->


						</div>
						<!--end::Info-->
					</div>
					<div>
						<div class="row mt-4">
							<br/>
							<!--begin::Item-->
							<div class="mb-2  col-md-4">
								<!--begin::Section-->
								<div class="d-flex align-items-center">
									<!--begin::Text-->
									<div class="d-flex flex-column flex-grow-1">
										<span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">Description</span>
									</div>
									<!--end::Text-->
								</div>

								<!--begin::Description-->
								<div class="flex-grow-1  text-dark-75 py-2 py-lg-2 mr-5">
									<div class="d-none1">Hi, team <br/></div>
								<?= $ticket->description ?>
									<div class="d-none1">

									Regards,<br/>
									{{ $name }}
									</div>
								</div>
								<!--end::Description-->
								<!--end::Desc-->
							</div>
							<!--end::Item-->
							<?php
							//var_dump($ticket->form_builder_json);
							//echo "<pre>";
							$form_builder = json_decode($ticket->form_builder_json);
							//var_dump($form_builder);
							//echo "<br/><br/>";

							$data_json = json_decode($ticket->data_json);
							//var_dump($data_json);
							//echo "</pre>";
							if(!empty($form_builder)) {
								foreach($form_builder as $f) {
									//var_dump($f);
									if(str_contains($f->type, 'data_grid')) {
										if(!empty($f->header)) {
										//echo "MASUK";
										?>
										<!--begin::Item-->
										<div class="mb-2 col-md-12">
											<!--begin::Section-->
											<div class="d-flex align-items-center">
												<!--begin::Text-->
												<div class="d-flex flex-column flex-grow-1">
													<span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1"><?=$f->label?></span>
												</div>
												<!--end::Text-->
											</div>
											<!--end::Section-->
											<!--begin::Desc-->
											<table class="table table-bordered table-striped">
												<thead>
													<tr>
														<?php
														$headers = explode("#*#",$f->header);
														?>
														@foreach($headers as $h)
														<th>
															{{$h}}
														</th>
														@endforeach
													</tr>
												</thead>
												<tbody>
													<?php
													for($r=0;$r<$f->rows;$r++) {
													?>
													<tr>
														<?php $k = 0;
														foreach($headers as $h) {

															$target_key = "input_".$f->name."_".$k."_".$r;
															?>
															<td>
																<?php
																foreach($data_json as $key=>$value) {
																	if($key == $target_key) {
																		//control selain checkbox
																		echo $value;
																	} else {
																		if(str_contains($key, $target_key)) {
																			//control checkbox, lebih dari 1 value
																			echo "<div><i class='fas fa-check' style='font-size: 11px;margin-right: 5px;color: #868686;'></i>".$value."</div>";
																		}
																	}
																}
																?>
															</td>
														<?php
														$k++;
														} ?>

													</tr>
													<?php
													} ?>
												</tbody>
											</table>
											<?php

											?>
											<!--end::Desc-->
										</div>
										<!--end::Item-->
										<?php
										}
									} else {
										?>
										<!--begin::Item-->
										<div class="mb-2  col-md-4">
											<!--begin::Section-->
											<div class="d-flex align-items-center">
												<!--begin::Text-->
												<div class="d-flex flex-column flex-grow-1">
													<span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1"><?=$f->label?></span>
												</div>
												<!--end::Text-->
											</div>
											<!--end::Section-->
											<!--begin::Desc-->
											<?php
											foreach($data_json as $key=>$value) {
												//echo $key;
												$a = explode("_",$key); //misal : location-1617930142092_location_Address
												if(!empty($f->name)) {
													if($a[0] == $f->name) {
														//echo "SAMA";
														//var_dump($value);
														//echo $f->type;

														$retval =  getObjectValue($f->type,$value);
														if(is_array($retval)) {
															echo "<ul>";
															foreach($retval as $val) {
																if(!is_array($val)) {
																	echo "<li>$val</li>";
																}
															}
															echo "</ul>";
														} else {
															if(is_string($retval)) {
																echo $retval;
															}
														}

													}
												}
											}
											?>
											<p class="text-dark-50 m-0 pt-5 font-weight-normal">{{ "" }}</p>
											<!--end::Desc-->
										</div>
										<!--end::Item-->
										<?php
									}
								}
							}
							?>
						</div>
						@if(!empty($ticket->files))
							<!--begin::Item-->
							<div class="mb-2">
								<!--begin::Section-->
								<div class="d-flex align-items-center">
									<!--begin::Text-->
									<div class="d-flex flex-column flex-grow-1">
										<span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">Attachments</span>
									</div>
									<!--end::Text-->
								</div>
								<!--end::Section-->
								<!--begin::Desc-->

								<div class="d-flex flex-column mb-5 align-items-start">
									<div>
									<?php $files = explode(",",$ticket->files_url);
									?>
									@foreach($files as $f)
										@if(is_image($f))
										@else
											<?php
											$a = explode("/",$f);
											$t = substr($a[count($a)-1],6);
											?>
											<a class="file-attach" target="_blank" href="{{$f}}">{{$t}}</a>
											<br/>
										@endif
									@endforeach
									@foreach($files as $f)
										@if(is_image($f))
											<a target="_blank" href="{{$f}}">
											<div class="symbol symbol-50 symbol-lg-50 symbol-light-primary ml-1 p-1" style="border:1px solid #d4d4d4;">
												<img src="{{$f}}">
											</div>
											</a>
										@else
										@endif
									@endforeach
									</div>
								</div>
								<!--end::Desc-->
							</div>
							<!--end::Item-->
						@endif
						<!--end::Info-->
					</div>
					<!--end::Top-->
					<!--begin::Separator-->
					<div class="separator separator-solid mt-7"></div>
					<!--end::Separator-->

					&nbsp;<button class="btn btn-sm btn-white-line2 mt-7" onclick="window.location.assign('{{URL('/')}}/ticket-monitoring')"><i class="flaticon2-left-arrow icon-sm text-dark-75"></i> Back</button>

					@if($ticket->status == "Resolved" && $ticket->created_by == Auth::user()->id)
						&nbsp;<button class="btn btn-sm btn-white-line2 rating-closed mt-7"  data-action="close-ticket"><i class="flaticon2-check-mark  icon-sm text-dark-75"></i> Close Ticket</button>
					@endif

					@if($ticket->status == "Resolved" && $ticket->created_by == Auth::user()->id)
						<button class="btn btn-sm btn-white-line2 reopen mt-7"  data-action="reopen-ticket"><i class="flaticon2-check-mark  icon-sm text-dark-75"></i> Reopen Ticket</button>
					@endif

					@if($ticket->agent_id == Auth::user()->person)
						@if(in_array($ticket->status, ["Open"]))
							<button class="btn btn-sm btn-white-line2 on_progress mt-7"  data-action="on_progress"><i class="flaticon2-check-mark  icon-sm text-dark-75"></i> Start Case</button>
						@endif
						@if(in_array($ticket->status, ["Open","On Progress","Re-Open"]))
							&nbsp;<button class="btn btn-sm btn-white-line2 escalate mt-7" data-action="escalate"><i class="flaticon2-zig-zag-line-sign  icon-sm text-dark-75"></i> Escalate</button>
						@endif
						@if(in_array($ticket->status, ["On Progress","Re-Open"]))
							<button class="btn btn-sm btn-white-line2 solved mt-7"  data-action="escalate"><i class="flaticon2-check-mark  icon-sm text-dark-75"></i> Solved</button>
						@endif
						&nbsp;
					@endif

					@if ($ticket->next_approval_id == Auth::user()->person && $ticket->status != 'Withdrawn')
						&nbsp;
						<button class="btn btn-sm btn-white-line2  mt-7 reasonModalButton reason-modal" data-id="{{ $ticket->id }}" data-title="{{ $ticket->title }}" data-status-ticket="Approve">Approve</button>
						&nbsp;
						<button class="btn btn-sm btn-white-line2  mt-7 reasonModalButton reason-modal" data-status-ticket="Reject">Reject</button>
						&nbsp;
						@if ($ticket->finalclass != "problem_request")
						<button class="btn btn-sm btn-white-line2  mt-7 askMoreInfoButton">Ask More Info</button>
						@endif
					@endif
					<!--end::Form-->

					&nbsp;
					<button  class="mb-0 btn btn-sm btn-white-line2  mt-7 reply-comment"  data-toggle="modal" data-target="#exampleModal" style=""><i class="flaticon2-reply-1 icon-sm text-dark-75"></i> Reply</button>

					<button  class="mb-0 btn btn-sm btn-white-line2  mt-7 activities"  ><i class="flaticon2-time icon-sm text-dark-75"></i> Activities</button>

					<!-- Delete Ticket -->
					@if ($ticket->created_by == Auth::user()->id && $ticket->status == "Submit for Approval" && DB::table('ticket_approval')->where('ticket_id', $ticket->id)->count() == 0)
					&nbsp;
					<button class="mb-0 btn btn-sm btn-white-line2 mt-7 reasonModalButton reason-modal" data-id="{{ $ticket->id }}" data-title="{{ $ticket->title }}" data-status-ticket="Delete"><i class="flaticon2-trash icon-sm text-dark-75"></i> Withdrawn</button>
					@endif

					<!--begin::Example-->
					<div class="content-activities example example-basic mt-5" style="display:none">
						<div class="example-preview">
							<!--begin::Timeline-->
							<div class="timeline timeline-6 mt-3">
								<?php
								$ticket_log = DB::table('ticket_log')->where('ticket_id',$ticket->id)->get();
								$color3 = ['primary','danger','warning','info','default'];
								$i = 0;
								?>
								@foreach($ticket_log as $t)
								<?php
								$i++;
								?>
								<!--begin::Item-->
								<div class="timeline-item align-items-start">
									<!--begin::Label-->
									<div class="timeline-label font-weight-bolder text-dark-75 font-size-lg"><?=date('d M Y H:i', strtotime($t->created_at))?></div>
									<!--end::Label-->
									<!--begin::Badge-->
									<div class="timeline-badge">
										<i class="fa fa-genderless text-{{$color3[$i%5]}} icon-xl"></i>
									</div>
									<!--end::Badge-->
									<!--begin::Text-->
									<div class="font-weight-mormal font-size-lg timeline-content text-muted pl-3">{!!sanitize($t->message)!!}</div>
									<!--end::Text-->
								</div>
								<!--end::Item-->
								@endforeach
							</div>
							<!--end::Timeline-->
						</div>
					</div>
					<!--end::Example-->



					<div class="card mt-5" style="border-radius:4px;">
						<div class="card-body p-4">
							<div class="row">
								<div class="col-md-4">
								  <b class="text-info">Request Type</b><br/>
								  <?php if ($ticket->finalclass == "service_request") {
										echo "Service";
									} elseif ($ticket->finalclass == "problem_request") {
										echo "Problem";
									} else {
										echo "Incident";
									}
									?>
								</div>
								<div class="col-md-4">
								  <b class="text-info">Impact</b><br/>
								  {{$ticket->impact}}
								</div>
								<div class="col-md-4">
								  <b class="text-info">Urgency</b><br/>
								  {{$ticket->urgency}}
								</div>
							</div>
						</div>
					</div>

					@if ($ticket->status == "Closed" && $ticket->rating)
					<div class="card mt-5" style="border-radius:4px;">
						<div class="card-body p-4">
							<div class="row">
								<div class="col-md-12">
								  <b class="text-info">Rating Message</b><br/>
								  <p>{{$ticket->comment ?? '-'}}</p>
								</div>
							</div>
						</div>
					</div>
					@endif

				</div>
			</div>

            <!--end::List Widget 8-->
<script>
$( document ).ready(function() {
	$(".activities").click(function(e) {
		if ($('.content-activities').css('display') == 'none') {
			$('.content-activities').slideDown();
			$(this).addClass('btn-press');
		} else {
			$('.content-activities').slideUp();
			$(this).removeClass('btn-press');
		}
	});
});
</script>
<style>
.btn-press {
	background: linear-gradient(180deg, #e0e0e0 0%, #e8e8e8 100%) !important;
}
.content-activities .timeline.timeline-6 .timeline-item .timeline-label {
    width: 150px;
    text-align: right;
    padding-right: 15px;
}
.content-activities .timeline.timeline-6:before {
    left: 151px;
}

</style>
