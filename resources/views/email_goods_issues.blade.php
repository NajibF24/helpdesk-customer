
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
					<img src="{{ asset('grp_bg_logo.png') }}" alt="" width="100" height="auto">
				</td></tr></tbody>
			</table>
		</td></tr></tbody>
	</table>



</td>
</tr>
</tbody>
</table>



</body></html>
