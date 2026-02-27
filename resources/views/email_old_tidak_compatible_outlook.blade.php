
<?php
use App\Helpers\TicketStatusHelper;
use Illuminate\Support\Facades\Auth;
 
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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.=
w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="box-sizing: border-box;"><head style="box-sizing: border-box;">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" style="box-sizing: border-box;">
<meta name="viewport" content="width=device-width" style="box-sizing: border-box;">

<title style="padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;box-sizing: border-box;font-size: 14px;">{{ $title }}</title>

</head>

<body style="box-sizing: border-box;font-family: 'Maven Pro', sans-serif;background-color: #fff;font-size: 0.95rem;font-weight: 400;line-height: 1.5;color: #212529;-webkit-text-size-adjust: 100%;-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">

<div class="container mt-5 mb-5" style="box-sizing: border-box;
    margin-bottom: 3rem !important;
    margin-top: 3rem !important;
    max-width: 720px;
    max-width: 540px;
    width: 100%;
    padding-right: 1rem;
    padding-left: 1rem;
    margin-right: auto;
    margin-left: auto;
">
    <div class="row d-flex justify-content-center" style="box-sizing: border-box;
    display: block !important;
    display: block;
    flex: 1 0 100%;
    margin-top: 0;
    margin-right: -0.75rem);
    margin-left: -0.75rem);
    ">
        <div class="col-md-8" style="box-sizing: border-box;
        flex: 0 0 auto;
    width: 66.666667%;
    flex-shrink: 0;
    width: 100%;
    max-width: 100%;
    padding-right: 0.75rem;
    padding-left: 0.75rem;
    margin-top: 0;
">
            <div class="card" style="box-sizing: border-box;background-color: #fff;border: none;position: relative;
    display: block;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    
    border: 1px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.25rem;">
                <div class="lower bg-primary p-4 py-3 text-white d-flex justify-content-between" style="box-sizing: border-box;background-color: #0d6efd !important;
	color: #fff !important;
    padding-top: 1rem !important;
    padding-bottom: 1rem !important;
    padding: 1.5rem !important;
    display: block !important;">
                    
                    <h5 style="text-align: center;width: 100%;box-sizing: border-box;margin-top: 0;margin-bottom: 0.5rem;font-weight: 600;line-height: 1.2;font-size: 1.1rem;">{{$title}}</h5>
                </div>
                <div class="upper p-4" style="box-sizing: border-box;padding: 13px;">
					<div style="box-sizing: border-box;">
						<?=$ticket->message?>
					</div>
					<?php if ($ticket->finalclass == "service_request") {
							$rtype =  "Service";
						} elseif ($ticket->finalclass == "problem") {
							$rtype =  "Problem";
						} else {
							$rtype =  "Incident";
						}
					?>   					
					<div style="box-sizing: border-box;">
					   
					</div>
                    <hr style="box-sizing: border-box;margin: 6px 0;color: inherit;background-color: #c8c9ca;border: 0;height: 1px;">
                    <h6 style="font-size: 19px;font-weight: 600;color: #0d6efd;box-sizing: border-box;margin-top: 0;margin-bottom: 0.5rem;line-height: 1.2;">Ticket Details</h6>
                    <div class="d-flex justify-content-between" style="box-sizing: border-box;">
                        <div class="d-flex flex-row align-items-center" style="box-sizing: border-box;">
                            <div class="add" style="box-sizing: border-box;"> <span class="font-weight-bold d-block" style="box-sizing: border-box;font-weight: 600 !important;display:block;">{{$rtype}}</span> 
                            <span class=" d-block" style="box-sizing: border-box;">
                            <?php
								$service = DB::table('service')->where('id', $ticket->service_id)->first();
							?>{{ $service->name }}
                            </span> <small style="box-sizing: border-box;"></small> </div> 
                        </div>
                    </div>
                    <div class="d-flex justify-content-between" style="box-sizing: border-box;">
                        <div class="d-flex flex-row align-items-center" style="box-sizing: border-box;">
                            <div class="add" style="box-sizing: border-box;"> <span class="font-weight-bold d-block" style="box-sizing: border-box;font-weight: 600 !important;display:block;">Subject Request </span> 
                            <span class=" d-block" style="box-sizing: border-box;">{{ $ticket->title }}</span> <small style="box-sizing: border-box;"></small> </div> 
<!--
                            <img src="" width="60" class="d-none rounded-circle">
-->
                        </div>
                    </div>
                    <div class="d-flex justify-content-between" style="box-sizing: border-box;">
                        <div class="d-flex flex-row align-items-center" style="box-sizing: border-box;">
                            <div class="add" style="box-sizing: border-box;"> <span class="font-weight-bold d-block" style="box-sizing: border-box;font-weight: 600 !important;display:block;">Ticket Number </span> 
                            <span class=" d-block" style="box-sizing: border-box;">{{ $ticket->ref }}</span> <small style="box-sizing: border-box;"></small> </div> 
<!--
                            <img src="" width="60" class="d-none rounded-circle">
-->
                        </div>
                    </div>
                    <hr style="box-sizing: border-box;margin: 6px 0;color: inherit;background-color: #c8c9ca;border: 0;height: 1px;">
                    <div class="d-flex justify-content-between" style="box-sizing: border-box;">
                        <div class="d-flex flex-row align-items-center" style="box-sizing: border-box;">
                            <div class="add" style="box-sizing: border-box;"> <span class="font-weight-bold d-block" style="box-sizing: border-box;font-weight: 600 !important;">Description</span> 
                            <span class=" d-block" style="box-sizing: border-box;"></span> <small style="box-sizing: border-box;"></small> </div> 
                        </div>
                    </div>
                    Hi, team <br style="box-sizing: border-box;"><br style="box-sizing: border-box;">
						<?= $ticket->description ?>		
						<br style="box-sizing: border-box;"><br style="box-sizing: border-box;">
						Regards,<br style="box-sizing: border-box;">
						{{ $name }}
                    <hr style="box-sizing: border-box;margin: 6px 0;color: inherit;background-color: #c8c9ca;border: 0;height: 1px;">
                    
                    
                    
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
										//echo "MASUK";
										?>
										<div class="delivery mb-2">
											<div class="">
												<div class="d-flex flex-row align-items-center"> <i class="fa fa-check-circle-o"></i> <span class="ml-0"><?=$f->label?></span> </div> 
												<span class="">
												
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
												</span>
											</div>
										</div>
										<?php 
									
									} 
									else {
										?>
										<div class="delivery mb-2" style="height:30px;">
											<div class="d-flex justify-content-between"											
											style="box-sizing: border-box;
													display: block !important;
													float: left;
													width:100%;">
												<div class="d-flex flex-row align-items-center"  style="box-sizing: border-box;
																						flex-direction: row !important;
																						display: block !important;width: 50%;float: left;
																					"> <i class="fa fa-check-circle-o"></i> <span class="ml-0"><?=$f->label?></span> </div> 
												<span class="font-weight-bold"  style="text-align: right;box-sizing: border-box;
																						text-align: right;
																						font-weight: 600 !important;
																						float: right;"												
												>
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
																		if(is_string($val)) {
																			echo "<li>$val</li>";
																		}
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
												
												</span>
											</div>
										</div>
			
										<?php 
									}
								}
							}
							?>
							
									@if(false && !empty($ticket->files))
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
					<hr style="box-sizing: border-box;margin: 6px 0;color: inherit;background-color: #c8c9ca;border: 0;height: 1px;">
                    <?php 
                    if (!function_exists('renderRow')) {
                    function renderRow($label,$value) {
						?>
						<div class="delivery mb-2" style="height:30px;box-sizing: border-box;">
							<div class="d-flex justify-content-between" 
							style="box-sizing: border-box;
								display: block !important;
								float: left;width:100%">
								<div class="d-flex flex-row align-items-center" style="box-sizing: border-box;
																						flex-direction: row !important;
																						display: block !important;width: 50%;float: left;
								"> <i class="fa fa-check-circle-o" style="box-sizing: border-box;color: blue;display: inline-block;border: 1.5px solid #000000;border-radius: 5px;height: 8px;width: 8px;margin-right: 5px;"></i> 
								<span class="ml-0" style="box-sizing: border-box;">{{$label}}</span> </div>
								<span class="font-weight-bold" style="text-align: right;box-sizing: border-box;
																		text-align: right;
																		font-weight: 600 !important;
																		float: right;">{{$value}}</span>
							</div>
						</div>						
						<?php
					}
					}
                    ?>
                    <?php 
                    renderRow('Ticket Status',$ticket->status);
                    $value = "";
					if(!empty($ticket->next_approval_id)) {
						$contact = DB::table('contact')
										->where('id',$ticket->next_approval_id)
										->first();
						echo $contact->name." ";
						if($ticket->approval_state == "appoval_support") {
							$value =  "(Approval Support)";
						} else {
							$value =  "(Approval User)";
						}
					} else {
						$value =  "-";
					}
					renderRow('Next Approver',$value);
					$team_name = DB::table('contact')->where('id', $ticket->team_id)->value('name') ?? '-';
					$agent_name = DB::table('contact')->where('id', $ticket->agent_id)->value('name') ?? '-' ;
					
					renderRow('Assignment Tier / Agent ',$team_name." / ".$agent_name);
					renderRow('Assign Time',empty($ticket->assign_time) ? "-" : date('M d, Y h:i A', strtotime($ticket->assign_time)));
					renderRow('Due Date',empty($ticket->due_date) ? "-" : date('M d, Y h:i A', strtotime($ticket->due_date)));
					$service = DB::table('service')->where('id', $ticket->service_id)->first();
					renderRow($rtype,$service->name);
                    
                    ?>
<!--
                    <div class="delivery">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex flex-row align-items-center"> <i class="fa fa-check-circle-o"></i> <span class="ml-2">Estimated days to find a buyer</span> </div> <span class="font-weight-bold">0 Days</span>
                        </div>
                    </div>
-->

                </div>
                <div class="lower bg-primary p-4 py-5 text-white d-flex justify-content-between" style="box-sizing: border-box;
	background-color: #0d6efd !important;
    color: #fff !important;
    padding-top: 3rem !important;
    padding-bottom: 3rem !important;
    padding: 1.5rem !important;
    display: block !important;
    min-height: 100px;
    width: 100%;
                ">
                    <div style="box-sizing: border-box;flex-direction: column !important;display: block !important;width: 50%;float: left;"> 
						<span style="box-sizing: border-box;">PT. Kaldu Sari Nabati </span> 
						<small style="box-sizing: border-box;">Copyright 2021 All Right Reserved</small> 
					</div>
                    
                    <img alt="Logo" src="https://ngs.nabatigroup.com/assets/images/logo_nabati.png" 
							style="margin-left: 0px; min-height: 45px;max-height: 45px;float: right;display: block;">
                </div>
            </div>
        </div>
    </div>
</div>






</body></html>
