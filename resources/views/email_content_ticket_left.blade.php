            <?php 
            //$ticket->assign_time = "2021-05-21 17:30:00";
            //echo $ticket->assign_time;
            //echo checkDueDate($ticket->id,$ticket->assign_time);
            ?>
            
            <!--begin::List Widget 8-->
			<div class="card card-custom gutter-b">
				<div class="card-body">
					<!--begin::Top-->
					<div class="d-flex">
						<!--begin::Pic-->
						<div class="flex-shrink-0 mr-7">
							<div class="symbol symbol-50 symbol-lg-120 symbol-light-primary">
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
									<a href="#" class="d-flex align-items-center text-dark text-hover-primary font-size-h5 font-weight-bold mr-3">


										{{ $name }}
										
										
									<i class="flaticon2-correct text-success icon-md ml-2"></i></a>
									<!--end::Name-->
								</div>
								<!--begin::User-->
							</div>
							<!--end::Title-->
							<!--begin::Content-->
							<div class="d-flex align-items-center flex-wrap justify-content-between">
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
					
					
							<br/>
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
									?>
									<!--begin::Item-->
									<div class="mb-2">
										<!--begin::Section-->
										<div class="d-flex align-items-center">
											<!--begin::Text-->
											<div class="d-flex flex-column flex-grow-1">
												<span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">{{$f->label}}</span>
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
							?>
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
						</div>
						<!--end::Info-->
					</div>
					<!--end::Top-->
					
					<div class="card mt-5" style="border-radius:4px;">
						<div class="card-body p-4">
							<div class="row">
								<div class="col-md-4">
								  <b class="text-info">Request Type</b><br/>
								  <?php if ($ticket->finalclass == "service_request") {
										echo "Service";
									} elseif ($ticket->finalclass == "problem") {
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

				</div>
			</div>

            <!--end::List Widget 8-->
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
