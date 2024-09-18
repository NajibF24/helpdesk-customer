
<?php
use App\Helpers\TicketStatusHelper;
use Illuminate\Support\Facades\Auth;

$name = DB::table('users')->where('id', $ticket->created_by)->value('name');
// dd($ticket);
$contact = DB::table('contact')->where('id',$ticket->requestor)->first();
// dd($contact);
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
<html xmlns="http://www.w3.org/1999/xhtml" style=""><head style="">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" style="">
<meta name="viewport" content="width=device-width" style="">

<title style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 14px;">{{ $title }}</title>

</head>

<body style="font-family: 'Maven Pro', sans-serif;background-color: #fff;font-size: 15.2px;font-weight: 400;line-height: 1.5;color: #212529;-webkit-text-size-adjust: 100%;-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">

<table style="background-color: #fff;" width="100%">
<tbody>
<tr>
<td>
	<!--HEADER TITLE-->
	<table width="500" style="background-color: #375471;padding-top:24px;padding-bottom:24px;padding-left:24px;padding-right:24px;" cellpadding="0" cellspacing="0" align="center">
		<tbody><tr><td>
			<h5 style="color:#fff;text-align: center;margin-top: 0;margin-bottom: 8px;font-weight: 600;line-height: 1.2;font-size: 17.6px">{{$title}}</h5>
		</td></tr></tbody>
	</table>
	<!--CONTENT-->
	<table width="500" cellpadding="0" cellspacing="0" align="center">
		<tbody><tr><td style="border-right:1px solid #c8c9ca;border-left:1px solid #c8c9ca;padding-top:13px;padding-bottom:13px;padding-left:13px;padding-right:13px;">
				<table  width="100%" cellpadding="0" cellspacing="0" align="center">
					<tbody>
						<tr>
							<td>
								<?=$ticket->message?>
							</td>
						</tr>
						<?php
                        // if ($ticket->finalclass == "service_request") {
						// 		$rtype =  "Service";
						// 	} elseif ($ticket->finalclass == "problem_request") {
						// 		$rtype =  "Problem";
						// 	} else {
						// 		$rtype =  "Incident";
						// 	}
                        $rtype =  "Goods Issues";
						?>
						<tr>
							<?php
							if (env('NABATI_PORTAL_URL') == "https://dev-globalservices.gunungsteel.com") {
								$url_ngs = "https://dev-globalservices.gunungsteel.com";
							} else {
								$url_ngs = "https://dev-globalservices.gunungsteel.com";
							}
							// if (env('NABATI_PORTAL_URL') == "http://localhost/hcism/public") {
							// 	$url_ngs = "http://localhost/hcism/public";
							// } else {
							// 	$url_ngs = "http://localhost/hcism/public";
							// }
							?>
							<td>
								<a href="{{@$redirect_url}}"><h6 style="font-size: 19px;font-weight: 600;color: #0d6efd;margin-top: 0;margin-bottom: 8px;line-height: 1.2;">Inventory Details</h6></a>
							</td>

						</tr>
						<tr>
							<td>
								<strong>{{$rtype}}</strong>
							</td>
						</tr>
						<tr>
							<td>
								<?php
									$service = DB::table('service')->where('id', $ticket->service_id)->first();
								?>{{ $service->name }}
							</td>
						</tr>
						<tr>
							<td>
								<strong>Subject Request </strong>
							</td>
						</tr>
						<tr>
							<td>
								{{ $ticket->subject }}
							</td>
						</tr>
						<tr>
							<td>
								<strong>Inventory Number </strong>
							</td>
						</tr>
						<tr>
							<td>
								{{ $ticket->code }}
							</td>
						</tr>
						<!--LINE-->
						<tr>
							<td style="padding-top:10px;padding-bottom:10px">
								<table width="100%" cellpadding="0" cellspacing="0" align="left">
									<tbody><tr><td style="border-bottom:1px solid #c8c9ca;">
									</td></tr></tbody>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<strong>Description </strong>
							</td>
						</tr>
						<tr>
							<td>
								Hi, team <br style=""><br style="">
									<?= $ticket->description ?>
									<br style=""><br style="">
									Regards,<br style="">
									{{ $name }}
							</td>
						</tr>
						<!--LINE-->
						<tr>
							<td style="padding-top:10px;padding-bottom:10px">
								<table width="100%" cellpadding="0" cellspacing="0" align="left">
									<tbody><tr><td style="border-bottom:1px solid #c8c9ca;">
									</td></tr></tbody>
								</table>
							</td>
						</tr>



						<?php
						//var_dump($ticket->form_builder_json);
						//echo "<pre>";
						$form_builder = json_decode($ticket->form_builder_json);
						//var_dump($form_builder);
						//echo "<br/><br/>";

						$data_json = json_decode($ticket->form_builder_data);
						//var_dump($data_json);
						//echo "</pre>";
						if(!empty($form_builder)) {
							foreach($form_builder ?? [] as $f) {
								//var_dump($f);
								if(str_contains($f->type, 'data_grid')) {
									//echo "MASUK";
									?>
									<tr>
										<td width="100%">
											<table  width="100%" cellpadding="0" cellspacing="0" align="center">
												<tbody>
													<tr>
														<td width="20" style="width:20px;">
															<div><!--[if mso]>
																  <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="http://" style="height:9px;v-text-anchor:middle;width:9px;" arcsize="556%" strokecolor="#1e3650" fill="t">
																	<v:fill type="tile" src="https://i.imgur.com/0xPEf.gif" color="#ffffff" />
																	<w:anchorlock/>
																	<center style="color:#ffffff;font-family:sans-serif;font-size:10px;font-weight:bold;">a</center>
																  </v:roundrect>
																<![endif]-->
																<a href="http://"
																style="background-color:#ffffff;;border:1px solid #1e3650;border-radius:50px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:10px;font-weight:bold;line-height:9px;text-align:center;text-decoration:none;width:9px;-webkit-text-size-adjust:none;mso-hide:all;">a</a>
															</div>
														</td>
														<td>
															<span><?=$f->label?></span>
														</td>
													</tr>
													<tr>
														<td align="right">
															<table >
																<thead>
																	<tr>
																		<?php
																		$headers = explode("#*#",$f->header);
																		?>
																		@foreach($headers ?? [] as $h)
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
																		foreach($headers ?? [] as $h) {

																			$target_key = "input_".$f->name."_".$k."_".$r;
																			?>
																			<td>
																				<?php
																				foreach($data_json ?? [] as $key=>$value) {
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

														</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>

									<?php
								}
								else {
									?>
									<tr>
										<td width="100%">
											<table  width="100%" cellpadding="0" cellspacing="0" align="center">
												<tbody>
													<tr>
														<td width="20" style="width:20px;">
															<div><!--[if mso]>
																  <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="http://" style="height:9px;v-text-anchor:middle;width:9px;" arcsize="556%" strokecolor="#1e3650" fill="t">
																	<v:fill type="tile" src="https://i.imgur.com/0xPEf.gif" color="#ffffff" />
																	<w:anchorlock/>
																	<center style="color:#ffffff;font-family:sans-serif;font-size:10px;font-weight:bold;">a</center>
																  </v:roundrect>
																<![endif]-->
																<a href="http://"
																style="background-color:#ffffff;;border:1px solid #1e3650;border-radius:50px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:10px;font-weight:bold;line-height:9px;text-align:center;text-decoration:none;width:9px;-webkit-text-size-adjust:none;mso-hide:all;">a</a>
															</div>
														</td>
														<td>
															<span><?=$f->label?></span>
														</td>
														<td align="right">
															<strong>

																<?php
																foreach($data_json ?? [] as $key=>$value) {
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
																				foreach($retval ?? [] as $val) {
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
															</strong>
														</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
									<?php
								}
							}
						}
						?>

						<!--LINE-->
						<tr>
							<td style="padding-top:10px;padding-bottom:10px">
								<table width="100%" cellpadding="0" cellspacing="0" align="left">
									<tbody><tr><td style="border-bottom:1px solid #c8c9ca;">
									</td></tr></tbody>
								</table>
							</td>
						</tr>

                    <?php
                    if (!function_exists('renderRow')) {
                    function renderRow($label,$value) {
						?>
						<tr>
							<td width="100%">
								<table  width="100%" cellpadding="0" cellspacing="0" align="center">
									<tbody>
										<tr>
											<td width="20" style="width:20px;">
												<div><!--[if mso]>
													  <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="http://" style="height:9px;v-text-anchor:middle;width:9px;" arcsize="556%" strokecolor="#1e3650" fill="t">
														<v:fill type="tile" src="https://i.imgur.com/0xPEf.gif" color="#ffffff" />
														<w:anchorlock/>
														<center style="color:#ffffff;font-family:sans-serif;font-size:10px;font-weight:bold;">a</center>
													  </v:roundrect>
													<![endif]-->
													<a href="http://"
													style="background-color:#ffffff;;border:1px solid #1e3650;border-radius:50px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:10px;font-weight:bold;line-height:9px;text-align:center;text-decoration:none;width:9px;-webkit-text-size-adjust:none;mso-hide:all;">a
													</a>
												</div>
											</td>
											<td>
												<span>{{$label}}</span>
											</td>
											<td align="right">
												<strong>{{$value}} </strong>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<?php
					}
					}
                    ?>

                    <?php
                    renderRow('Inventory Status',$ticket->status);
                    $value = "";
					if(!empty($ticket->next_approver_id)) {
						$contact = DB::table('contact')
										->where('id',$ticket->next_approver_id)
										->first();
						// echo $contact->name." ";
						// if($ticket->approval_state == "appoval_support") {
						// 	$value =  "(Approval Support)";
						// } else {
						// 	$value =  "(Approval User)";
						// }
					} else {
						// $value =  "-";
						$value = $contact->name;
					}
					renderRow('Next Approver',$value);
					// $team_name = DB::table('contact')->where('id', $ticket->team_id)->value('name') ?? '-';
					// $team_name = DB::table('lnkemployeetoteam')->where('employee_id', $ticket->agent_id)->leftJoin('contact', 'contact.id', 'lnkemployeetoteam.team_id')->value('name') ?? '-';
					// $agent_name = DB::table('contact')->where('id', $ticket->agent_id)->value('name') ?? '-' ;

					// renderRow('Assignment Tier / Agent ',$team_name." / ".$agent_name);
					// renderRow('Assign Time',empty($ticket->assign_time) ? "-" : date('M d, Y h:i A', strtotime($ticket->assign_time)));
					// renderRow('Due Date',empty($ticket->due_date) ? "-" : date('M d, Y h:i A', strtotime($ticket->due_date)));
					$service = DB::table('service')->where('id', $ticket->service_id)->first();
					renderRow($rtype,$service->name);

                    ?>
					</tbody>
				</table>
		</td></tr></tbody>
	</table>
	<!--FOOTER-->
	<table width="500" style="background-color: #375471"   cellpadding="0" cellspacing="0" align="center">
		<tbody><tr><td style="padding-top:24px;padding-bottom:24px;padding-left:24px;padding-right:24px;">
			<table cellpadding="0" cellspacing="0" align="left">
				<tbody><tr><td>
					<table width="100%" cellpadding="0" cellspacing="0" align="left">
						<tbody><tr><td>
								<span style="color:#fff;">PT. Garuda Yamato Steel</span>
						</td></tr></tbody>
					</table>
					<table width="100%" cellpadding="0" cellspacing="0" align="left">
						<tbody><tr><td>
								<small style="color:#fff;">Copyright {{ date('Y') }} All Right Reserved</small>
						</td></tr></tbody>
					</table>
				</td></tr></tbody>
			</table>

			<table  cellpadding="0" cellspacing="0" align="right">
				<tbody><tr><td>
				<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 width="190px" height="61.5px" viewBox="211 365 190 61.5" enable-background="new 211 365 190 61.5" xml:space="preserve">
<g>
	<path fill="#FEA621" d="M262.306,380.809c0.263,0.394,0.196,0.787,0.196,1.181c0,3.608-0.065,7.217,0,10.76
		c0,1.115-0.262,1.903-1.377,2.362c-0.328,0.131-24.275,13.843-24.275,13.843s0-7.281,0-10.89
		c0.721-0.394,10.628-6.102,13.581-7.874c1.574-0.853,3.149-1.64,4.527-2.69C256.663,386.779,262.699,382.908,262.306,380.809z"/>
	<path fill="#F6313D" d="M262.24,397.014c0.328,0.263,0.197,0.656,0.197,0.984c0,3.936,0,7.808,0,11.743
		c0,0.655-0.263,1.049-0.853,1.377c-8.004,4.593-15.942,9.185-23.947,13.777c-0.328,0.198-0.656,0.592-1.115,0.198
		c0.328-0.395,0.262-0.919,0.262-1.378c0-3.15,0-6.234,0-9.382c3.609-2.034,7.217-4.068,10.76-6.168
		C250.102,406.723,262.24,400.36,262.24,397.014z"/>
	<path fill="#DB6D0F" d="M236.85,398.064c0,3.608,0,7.281,0,10.89c-4.002-2.296-8.07-4.593-12.072-6.888
		c-0.525-0.264-0.984-0.656-1.575-0.788c-0.328-0.262-0.656-0.59-1.05-0.787c-1.837-1.05-3.674-2.1-5.511-3.148
		c-0.919-0.526-4.002-2.231-4.461-2.56c-0.459-0.262-0.722-0.59-0.722-1.115c0-3.346,0-6.692,0-10.104
		c0.393,0,0.656,0.197,0.918,0.328c2.166,1.246,4.331,2.493,6.496,3.74C219.595,388.025,235.866,397.472,236.85,398.064z"/>
	<path fill="#A00E09" d="M236.85,414.335c0,3.147,0,6.231,0,9.382c0,0.459,0.065,0.983-0.263,1.378
		c-7.283-4.2-14.565-8.398-21.848-12.531c-0.918-0.525-1.771-1.05-2.689-1.575c-0.394-0.197-0.59-0.524-0.59-0.984
		c0-3.346,0-6.758,0-10.104c0.459,0,0.787,0.196,1.181,0.394c2.099,1.247,4.199,2.429,6.298,3.674
		c1.312,0.789,2.69,1.51,4.002,2.297C227.599,408.954,232.258,411.644,236.85,414.335z"/>
	<path fill="#FEA621" d="M218.939,387.698c-2.165-1.247-4.33-2.493-6.495-3.74c-0.328-0.196-0.591-0.328-0.919-0.328
		c0-2.624,0.459-3.083,2.231-4.068c7.544-4.265,23.212-13.325,23.212-13.325l0.077,10.801c0,0-8.856,5.149-12.792,7.445
		C222.482,385.532,220.58,386.45,218.939,387.698z"/>
	<path fill="#DB6D0F" d="M236.902,377.135v-10.898c0,0,15.694,8.996,23.37,13.457c0.656,0.394,1.377,0.788,2.034,1.247
		c0.393,2.099-5.577,5.97-7.283,6.692c-0.394-0.197-0.787-0.394-1.181-0.656C248.2,383.63,236.902,377.135,236.902,377.135z"/>
	<path fill="#F6313D" d="M218.939,403.968c-2.099-1.245-4.199-2.493-6.298-3.674c-0.328-0.197-0.722-0.394-1.181-0.394
		c0-0.196,0-0.394,0-0.59c0-2.231,0.722-3.018,2.362-3.543c0.984,0.525,6.495,3.739,8.332,4.724c0.394,0.197,0.722,0.525,1.05,0.787
		c-0.328,0.196-0.656,0.459-1.05,0.656C221.104,402.656,219.924,403.115,218.939,403.968z"/>
	<path fill="#A00E09" d="M262.24,397.014c0,3.346-5.577,5.838-7.217,6.822c-1.443-0.853-4.592-2.65-4.592-2.65l9.713-5.531
		C260.144,395.655,261.584,396.621,262.24,397.014z"/>
	<path fill="#A00E09" d="M237.047,382.58c3.936,2.231,7.872,4.462,11.809,6.692c0.525,0.329,1.05,0.59,1.575,0.918
		c-2.952,1.772-9.513,5.511-9.513,5.511s-2.625-1.508-3.871-2.23C237.047,389.863,237.047,386.189,237.047,382.58z"/>
	<path fill="#F6313D" d="M237.047,393.471c-1.312,0.721-4.199,2.296-4.199,2.296l-9.448-5.446c0,0,8.661-4.986,12.925-7.479
		c0.262-0.131,0.459-0.328,0.787-0.263C237.047,386.189,237.047,389.797,237.047,393.471z"/>
	<g>
		<path fill="#FFFFFF" d="M272.738,378.578h5.117v20.994h-5.117V378.578z"/>
		<path fill="#FFFFFF" d="M282.775,378.578h5.183l5.839,9.775c0.853,1.378,1.771,3.609,1.771,3.609h0.066
			c0,0-0.263-2.23-0.263-3.609v-9.775h5.117v20.994h-5.117l-5.839-9.775c-0.852-1.377-1.771-3.608-1.771-3.608h-0.065
			c0,0,0.263,2.23,0.263,3.608v9.775h-5.117v-20.994H282.775z"/>
		<path fill="#FFFFFF" d="M309.478,382.974h-6.364v-4.396h17.844v4.396h-6.364v16.598h-5.116V382.974z"/>
		<path fill="#FFFFFF" d="M323.517,378.578h13.121v4.396h-8.003v3.805h6.364v4.396h-6.364v3.937h8.397v4.396h-13.58v-20.928H323.517
			z"/>
		<path fill="#FFFFFF" d="M350.351,378.184c5.38,0,8.069,2.756,8.069,2.756l-2.494,3.871c0,0-2.164-1.968-5.116-1.968
			c-4.396,0-6.167,2.821-6.167,5.905c0,4.002,2.755,6.364,6.036,6.364c2.493,0,4.265-1.509,4.265-1.509v-1.64h-2.953v-4.396h7.479
			v11.875h-4.265v-0.591c0-0.459,0-0.852,0-0.852h-0.065c0,0-2.033,1.836-5.511,1.836c-5.379,0-10.301-4.002-10.301-10.891
			C339.395,382.908,343.987,378.184,350.351,378.184z"/>
		<path fill="#FFFFFF" d="M363.34,378.578h7.284c2.164,0,3.148,0.131,4.066,0.524c2.362,0.918,3.871,3.019,3.871,6.102
			c0,2.231-1.05,4.658-3.149,5.708v0.066c0,0,0.263,0.393,0.787,1.246l4.068,7.348h-5.708l-3.673-7.085h-2.429v7.085h-5.118V378.578
			z M370.689,388.091c1.641,0,2.69-0.919,2.69-2.559c0-1.575-0.591-2.493-3.148-2.493h-1.706v5.117h2.164V388.091z"/>
		<path fill="#FFFFFF" d="M393.913,395.111h-6.561l-1.247,4.396h-5.247l7.15-20.994h5.38l7.151,20.994h-5.248L393.913,395.111z
			 M390.634,383.367c0,0-0.46,2.296-0.854,3.608l-1.181,4.067h4.133l-1.181-4.067C391.158,385.664,390.698,383.367,390.634,383.367
			L390.634,383.367z"/>
	</g>
	<g>
		<path fill="#FFFFFF" d="M273.591,406.593c0.065,0.131,0.13,0.263,0.262,0.328c0.131,0.131,0.329,0.197,0.525,0.262
			c0.263,0.066,0.525,0.197,0.919,0.263c0.722,0.132,1.246,0.394,1.64,0.655c0.328,0.264,0.525,0.723,0.525,1.182
			c0,0.263-0.066,0.526-0.197,0.788c-0.13,0.262-0.262,0.459-0.458,0.59s-0.46,0.263-0.722,0.394
			c-0.263,0.065-0.59,0.132-0.919,0.132c-0.524,0-0.983-0.066-1.443-0.263c-0.458-0.197-0.853-0.459-1.246-0.788l0.459-0.524
			c0.328,0.328,0.722,0.524,1.05,0.722c0.328,0.132,0.787,0.263,1.246,0.263s0.788-0.131,1.05-0.328
			c0.262-0.196,0.394-0.458,0.394-0.853c0-0.132,0-0.328-0.065-0.394c-0.066-0.132-0.131-0.263-0.263-0.328
			c-0.131-0.132-0.328-0.197-0.525-0.263c-0.196-0.065-0.524-0.131-0.853-0.263c-0.394-0.066-0.722-0.196-0.984-0.263
			c-0.263-0.065-0.524-0.262-0.721-0.394c-0.197-0.131-0.328-0.328-0.394-0.524c-0.065-0.197-0.131-0.459-0.131-0.722
			s0.065-0.524,0.131-0.722c0.131-0.197,0.263-0.393,0.459-0.591c0.197-0.131,0.394-0.263,0.656-0.394
			c0.263-0.065,0.525-0.13,0.854-0.13c0.459,0,0.918,0.064,1.246,0.195c0.394,0.133,0.722,0.328,1.05,0.591l-0.459,0.592
			c-0.329-0.264-0.591-0.461-0.919-0.526c-0.328-0.13-0.656-0.195-0.984-0.195c-0.197,0-0.394,0-0.59,0.065
			c-0.197,0.064-0.329,0.13-0.46,0.195c-0.13,0.066-0.197,0.197-0.262,0.328c-0.065,0.133-0.131,0.263-0.131,0.394
			C273.524,406.33,273.591,406.462,273.591,406.593z"/>
		<path fill="#FFFFFF" d="M288.877,409.086c-0.131,0.394-0.394,0.787-0.656,1.115c-0.328,0.328-0.656,0.59-1.05,0.722
			c-0.393,0.196-0.853,0.263-1.377,0.263s-0.984-0.066-1.377-0.263c-0.394-0.197-0.788-0.459-1.05-0.722
			c-0.328-0.328-0.525-0.656-0.656-1.115c-0.132-0.394-0.263-0.854-0.263-1.312c0-0.46,0.065-0.919,0.263-1.313
			c0.131-0.395,0.393-0.788,0.656-1.116c0.327-0.328,0.656-0.589,1.05-0.722c0.394-0.195,0.853-0.263,1.377-0.263
			s0.984,0.067,1.377,0.263c0.394,0.197,0.788,0.461,1.05,0.722c0.329,0.328,0.525,0.656,0.656,1.116
			c0.131,0.394,0.263,0.853,0.263,1.313C289.139,408.232,289.073,408.692,288.877,409.086z M288.156,406.723
			c-0.131-0.328-0.329-0.591-0.525-0.853c-0.263-0.263-0.525-0.459-0.853-0.591c-0.328-0.13-0.656-0.195-1.05-0.195
			c-0.393,0-0.721,0.065-1.049,0.195c-0.328,0.132-0.59,0.328-0.787,0.591c-0.198,0.262-0.394,0.524-0.525,0.853
			c-0.131,0.328-0.198,0.656-0.198,1.051c0,0.393,0.066,0.721,0.198,1.049c0.131,0.329,0.327,0.591,0.525,0.853
			c0.262,0.263,0.525,0.459,0.853,0.591c0.328,0.131,0.656,0.197,1.05,0.197c0.394,0,0.721-0.066,1.05-0.197
			c0.328-0.132,0.59-0.328,0.788-0.591c0.196-0.262,0.394-0.523,0.525-0.853c0.13-0.328,0.197-0.656,0.197-1.049
			C288.352,407.445,288.286,407.052,288.156,406.723z"/>
		<path fill="#FFFFFF" d="M294.387,404.493h0.722v5.904h3.74v0.656h-4.462V404.493z"/>
		<path fill="#FFFFFF" d="M309.149,408.299c0,0.459-0.064,0.918-0.196,1.246c-0.132,0.394-0.328,0.656-0.59,0.919
			c-0.263,0.262-0.526,0.459-0.854,0.524c-0.327,0.131-0.721,0.197-1.114,0.197s-0.787-0.066-1.115-0.197s-0.656-0.328-0.853-0.524
			c-0.262-0.263-0.459-0.525-0.591-0.919c-0.131-0.328-0.196-0.787-0.196-1.246v-3.806h0.722v3.806c0,0.722,0.197,1.246,0.524,1.64
			c0.329,0.394,0.853,0.591,1.509,0.591c0.591,0,1.114-0.197,1.443-0.525c0.327-0.394,0.525-0.918,0.525-1.64v-3.806h0.722v3.74
			H309.149z"/>
		<path fill="#FFFFFF" d="M315.25,406.593c0.066,0.131,0.132,0.263,0.263,0.328c0.131,0.131,0.329,0.197,0.525,0.262
			c0.262,0.066,0.524,0.197,0.918,0.263c0.722,0.132,1.246,0.394,1.64,0.655c0.328,0.264,0.526,0.723,0.526,1.182
			c0,0.263-0.065,0.526-0.198,0.788c-0.13,0.262-0.262,0.459-0.458,0.59c-0.198,0.131-0.46,0.263-0.722,0.394
			c-0.263,0.065-0.591,0.132-0.919,0.132c-0.525,0-0.983-0.066-1.443-0.263c-0.46-0.197-0.854-0.459-1.246-0.788l0.458-0.524
			c0.328,0.328,0.723,0.524,1.05,0.722c0.328,0.132,0.788,0.263,1.247,0.263c0.46,0,0.787-0.131,1.05-0.328
			c0.262-0.196,0.394-0.458,0.394-0.853c0-0.132,0-0.328-0.066-0.394c-0.065-0.132-0.13-0.263-0.262-0.328
			c-0.132-0.132-0.328-0.197-0.524-0.263c-0.197-0.065-0.525-0.131-0.854-0.263c-0.394-0.066-0.722-0.196-0.984-0.263
			c-0.262-0.13-0.523-0.262-0.722-0.394c-0.195-0.131-0.328-0.328-0.394-0.524c-0.065-0.197-0.13-0.459-0.13-0.722
			s0.065-0.524,0.13-0.722c0.132-0.197,0.264-0.393,0.46-0.591c0.197-0.131,0.394-0.263,0.655-0.394
			c0.263-0.065,0.525-0.13,0.854-0.13c0.458,0,0.918,0.064,1.246,0.195c0.394,0.133,0.722,0.328,1.05,0.591l-0.46,0.592
			c-0.328-0.264-0.59-0.461-0.918-0.526c-0.328-0.13-0.656-0.195-0.984-0.195c-0.197,0-0.394,0-0.59,0.065
			c-0.198,0.064-0.329,0.13-0.46,0.195c-0.132,0.066-0.196,0.197-0.262,0.328c-0.066,0.133-0.132,0.263-0.132,0.394
			C315.186,406.33,315.186,406.462,315.25,406.593z"/>
		<path fill="#FFFFFF" d="M325.158,404.493v6.626h-0.723v-6.626H325.158z"/>
		<path fill="#FFFFFF" d="M340.445,409.282L340.445,409.282l-2.429-3.542v5.313h-0.722v-6.561h0.722l2.429,3.607l2.428-3.607h0.722
			v6.626h-0.722v-5.313L340.445,409.282z"/>
		<path fill="#FFFFFF" d="M355.337,411.119h-0.787l-0.788-1.771h-3.608l-0.787,1.771h-0.787l3.019-6.626h0.721L355.337,411.119z
			 M351.99,405.345l-1.509,3.347h3.019L351.99,405.345z"/>
		<path fill="#FFFFFF" d="M365.178,404.493h0.722v6.626h-0.59l-4.266-5.379v5.379h-0.722v-6.626h0.722l4.134,5.315V404.493
			L365.178,404.493z"/>
		<path fill="#FFFFFF" d="M376.987,409.086c-0.196,0.394-0.393,0.787-0.721,1.049c-0.328,0.329-0.656,0.525-1.115,0.723
			c-0.46,0.196-0.919,0.262-1.443,0.262h-2.231v-6.626h2.297c0.524,0,0.983,0.065,1.443,0.264c0.459,0.195,0.787,0.394,1.115,0.721
			c0.329,0.329,0.524,0.655,0.722,1.05c0.196,0.394,0.263,0.853,0.263,1.312C377.249,408.232,377.184,408.692,376.987,409.086z
			 M376.331,406.789c-0.132-0.327-0.328-0.59-0.525-0.853c-0.262-0.263-0.524-0.394-0.852-0.524
			c-0.328-0.132-0.723-0.197-1.116-0.197h-1.574v5.249h1.574c0.394,0,0.788-0.066,1.116-0.197c0.327-0.132,0.655-0.328,0.852-0.524
			c0.197-0.197,0.394-0.525,0.525-0.854c0.131-0.328,0.196-0.656,0.196-1.05S376.462,407.118,376.331,406.789z"/>
		<path fill="#FFFFFF" d="M383.351,404.493v6.626h-0.721v-6.626H383.351z"/>
		<path fill="#FFFFFF" d="M393.52,411.119l-1.901-2.559h-1.903v2.559h-0.723v-6.626h2.821c0.395,0,0.722,0.065,0.984,0.131
			c0.264,0.065,0.526,0.197,0.722,0.394c0.197,0.197,0.394,0.395,0.461,0.591c0.13,0.263,0.195,0.524,0.195,0.787
			s-0.065,0.525-0.131,0.723c-0.064,0.196-0.197,0.393-0.394,0.59c-0.132,0.131-0.327,0.263-0.59,0.393
			c-0.198,0.132-0.459,0.198-0.723,0.198l2.035,2.689h-0.854V411.119z M392.995,405.543c-0.327-0.263-0.721-0.328-1.247-0.328
			h-2.033v2.689h2.033c0.263,0,0.46,0,0.656-0.065c0.198-0.064,0.394-0.131,0.526-0.262c0.131-0.132,0.261-0.263,0.327-0.459
			c0.066-0.197,0.131-0.395,0.131-0.591C393.454,406.067,393.324,405.74,392.995,405.543z"/>
		<path fill="#FFFFFF" d="M400.278,404.493v6.626h-0.722v-6.626H400.278z"/>
	</g>
</g>
</svg>
				</td></tr></tbody>
			</table>
		</td></tr></tbody>
	</table>



</td>
</tr>
</tbody>
</table>



</body></html>
