@extends('layouts.app')

@section('content')
<script>
function on_click_approve() {
	window.location = '{{URL('/').'/approve-request'}}';
}

localStorage.setItem('is_back_service', '0');
</script>

<?php

use Illuminate\Support\Facades\Auth;

//$agents = array();
$list_bawahan = array();
if (Auth::user()->person) {
	$contact = DB::table('contact')->where('id', Auth::user()->person)->first();

	if ($contact->job_title) {

		$list_bawahan = get_bawahan($contact->job_title);
		//$list_child_job_title = DB::table('job_title')->where('parent_id',$contact->job_title)->get();
		//for() {

		//}

		//while(!empty($list_child_job_title)) {
			//foreach($list_child_job_title as $job_title) {
				//$list_bawahan = DB::table('contact')->where('job_title', $job_title->id)->get();
				//foreach($list_bawahan as $bawahan) {
					//array_push($bawahan,$bawahan);
				//}
				//$list_child_job_title = DB::table('job_title')->where('parent_id',$job_title->id)->get();

			//}
		//}

		//$job_title = DB::table('job_title')->get();
		//$job_title2 = get_agents($job_title, $contact->job_title) ?? '';

		//if ($job_title2) {
			//foreach($job_title2 as $job2) {
				//$agents[] = DB::table('contact')->where('job_title', $job2['id'])->first();

				//if ($job2['children'] != null) {
					//foreach($job2['children'] as $child) {
						//array_push($agents, DB::table('contact')->where('job_title', $child['id'])->first());
					//}
				//}
			//}
		//}
	}
}
//dd($list_bawahan);
$first = date("Y-m-d", strtotime("first day of this month"));
$last = date("Y-m-d", strtotime("last day of this month"));
$array_pl = array_column($list_bawahan, 'id');
array_push($array_pl, Auth::user()->person);
?>
<style>
.subheader {
	display:none;
}
.content {
    padding: 0px 0 40px 0 !important;
}
</style>
<script src="{{URL('/')}}/assets/js/pages/widgets.js"></script>
					<!--begin::Content-->
					<div  style="padding-top:0px !important" class="box-content-home col-lg-12" id="kt_content">
						<!--begin::Entry-->

						<style>
.wave.wave-success {
    background-color: rgb(27 197 189 / 0.2) !important;
}
.wave.wave-warning {
    background-color: rgba(255, 168, 0, 0.2) !important;
}
.wave.wave-primary {
    background-color: rgba(246, 78, 96, 0.2) !important;
}
						</style>

						<!--begin::Section-->
						<div class="container-fluid py-8">
							<div class="row">

								<div class="col-lg-4">
									<!--begin::Callout-->

									<div  style="cursor: pointer;" class="card card-custom wave wave-animate-slow wave-warning mb-8 mb-lg-0" >
										<div class="card-body" onclick="window.location = '{{URL('/').'/request-service/service-catalog/2'}}?division={{@$division->id}}';">
											<div class="d-flex align-items-center p-5">
												<!--begin::Icon-->
												<div class="mr-6">
													<span class="svg-icon svg-icon-warning svg-icon-5x">
														<!--begin::Svg Icon | path:assets/media/svg/icons/Design/Sketch.svg-->
														<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
															<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																<rect x="0" y="0" width="24" height="24" />
																<polygon fill="#000000" opacity="0.3" points="5 3 19 3 23 8 1 8" />
																<polygon fill="#000000" points="23 8 12 20 1 8" />
															</g>
														</svg>
														<!--end::Svg Icon-->
													</span>
												</div>
												<!--end::Icon-->
												<!--begin::Content-->
												<div class="d-flex flex-column">
													<span href=#" class="text-dark text-hover-primary font-weight-bold font-size-h4 mb-3">Request Service</span>
													<div class="text-dark-75" style="height:38px">Raise a Request for a new device or service</div>
												</div>
												<!--end::Content-->
											</div>
										</div>
									</div>

									<!--end::Callout-->
								</div>
								<div class="col-lg-4">
									<!--begin::Callout-->

									<div  style="cursor: pointer;" class="card card-custom wave wave-animate-slow wave-danger mb-8 mb-lg-0">
										<div class="card-body" onclick="window.location = '{{URL('/').'/request-incident/incident-catalog/2'}}?division={{@$division->id}}';">
											<div class="d-flex align-items-center p-5">
												<!--begin::Icon-->
												<div class="mr-6">
													<span class="svg-icon svg-icon-danger svg-icon-5x">
														<!--begin::Svg Icon | path:assets/media/svg/icons/General/Thunder-move.svg-->
														<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
															<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																<rect x="0" y="0" width="24" height="24" />
																<path d="M16.3740377,19.9389434 L22.2226499,11.1660251 C22.4524142,10.8213786 22.3592838,10.3557266 22.0146373,10.1259623 C21.8914367,10.0438285 21.7466809,10 21.5986122,10 L17,10 L17,4.47708173 C17,4.06286817 16.6642136,3.72708173 16.25,3.72708173 C15.9992351,3.72708173 15.7650616,3.85240758 15.6259623,4.06105658 L9.7773501,12.8339749 C9.54758575,13.1786214 9.64071616,13.6442734 9.98536267,13.8740377 C10.1085633,13.9561715 10.2533191,14 10.4013878,14 L15,14 L15,19.5229183 C15,19.9371318 15.3357864,20.2729183 15.75,20.2729183 C16.0007649,20.2729183 16.2349384,20.1475924 16.3740377,19.9389434 Z" fill="#000000" />
																<path d="M4.5,5 L9.5,5 C10.3284271,5 11,5.67157288 11,6.5 C11,7.32842712 10.3284271,8 9.5,8 L4.5,8 C3.67157288,8 3,7.32842712 3,6.5 C3,5.67157288 3.67157288,5 4.5,5 Z M4.5,17 L9.5,17 C10.3284271,17 11,17.6715729 11,18.5 C11,19.3284271 10.3284271,20 9.5,20 L4.5,20 C3.67157288,20 3,19.3284271 3,18.5 C3,17.6715729 3.67157288,17 4.5,17 Z M2.5,11 L6.5,11 C7.32842712,11 8,11.6715729 8,12.5 C8,13.3284271 7.32842712,14 6.5,14 L2.5,14 C1.67157288,14 1,13.3284271 1,12.5 C1,11.6715729 1.67157288,11 2.5,11 Z" fill="#000000" opacity="0.3" />
															</g>
														</svg>
														<!--end::Svg Icon-->

													</span>
												</div>
												<!--end::Icon-->
												<!--begin::Content-->
												<div class="d-flex flex-column">
													<span href="#" class="text-dark text-hover-primary font-weight-bold font-size-h4 mb-3">Request Incident</span>
													<div class="text-dark-75"  style="height:38px">Having Trouble ? Contact The Support Team</div>
												</div>
												<!--end::Content-->
											</div>
										</div>
									</div>

									<!--end::Callout-->
								</div>

								<div class="col-lg-4">
									<!--begin::Callout-->

									<div  style="cursor: pointer;" class="card card-custom wave wave-animate-slow wave-success mb-8 mb-lg-0">
										<div class="card-body" onclick="window.location = '{{URL('/').'/report'}}';">
											<div class="d-flex align-items-center p-5">
												<!--begin::Icon-->
												<div class="mr-6">
													<span class="svg-icon svg-icon-success svg-icon-5x">
														<!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\Shopping\Chart-pie.svg-->
														<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 122.88 102.52" xml:space="preserve"><style type="text/css">.st0{fill-rule:evenodd;clip-rule:evenodd;}</style><g><path class="st0" d="M5.42,0h112.04c2.98,0,5.42,2.44,5.42,5.42V97.1c0,2.98-2.44,5.42-5.42,5.42H5.42c-2.98,0-5.42-2.44-5.42-5.42 V5.42C0,2.44,2.44,0,5.42,0L5.42,0z M8.48,23.58H38.1c0.82,0,1.48,0.67,1.48,1.48v9.76c0,0.81-0.67,1.48-1.48,1.48H8.48 c-0.81,0-1.48-0.67-1.48-1.48v-9.76C6.99,24.25,7.66,23.58,8.48,23.58L8.48,23.58z M84.78,82.35h29.63c0.82,0,1.48,0.67,1.48,1.48 v9.76c0,0.81-0.67,1.48-1.48,1.48H84.78c-0.81,0-1.48-0.67-1.48-1.48v-9.76C83.29,83.02,83.96,82.35,84.78,82.35L84.78,82.35z M46.8,82.35h29.28c0.82,0,1.48,0.67,1.48,1.48v9.76c0,0.81-0.67,1.48-1.48,1.48H46.8c-0.81,0-1.48-0.67-1.48-1.48v-9.76 C45.31,83.02,45.98,82.35,46.8,82.35L46.8,82.35z M8.48,82.35H38.1c0.82,0,1.48,0.67,1.48,1.48v9.76c0,0.81-0.67,1.48-1.48,1.48 H8.48c-0.81,0-1.48-0.67-1.48-1.48v-9.76C6.99,83.02,7.66,82.35,8.48,82.35L8.48,82.35z M84.78,62.76h29.63 c0.82,0,1.48,0.67,1.48,1.48V74c0,0.81-0.67,1.48-1.48,1.48H84.78c-0.81,0-1.48-0.67-1.48-1.48v-9.76 C83.29,63.43,83.96,62.76,84.78,62.76L84.78,62.76z M46.8,62.76h29.28c0.82,0,1.48,0.67,1.48,1.48V74c0,0.81-0.67,1.48-1.48,1.48 H46.8c-0.81,0-1.48-0.67-1.48-1.48v-9.76C45.31,63.43,45.98,62.76,46.8,62.76L46.8,62.76z M8.48,62.76H38.1 c0.82,0,1.48,0.67,1.48,1.48V74c0,0.81-0.67,1.48-1.48,1.48H8.48c-0.81,0-1.48-0.67-1.48-1.48v-9.76 C6.99,63.43,7.66,62.76,8.48,62.76L8.48,62.76z M84.78,43.17h29.63c0.82,0,1.48,0.67,1.48,1.48v9.76c0,0.81-0.67,1.48-1.48,1.48 H84.78c-0.81,0-1.48-0.67-1.48-1.48v-9.76C83.29,43.84,83.96,43.17,84.78,43.17L84.78,43.17z M46.8,43.17h29.28 c0.82,0,1.48,0.67,1.48,1.48v9.76c0,0.81-0.67,1.48-1.48,1.48H46.8c-0.81,0-1.48-0.67-1.48-1.48v-9.76 C45.31,43.84,45.98,43.17,46.8,43.17L46.8,43.17z M8.48,43.17H38.1c0.82,0,1.48,0.67,1.48,1.48v9.76c0,0.81-0.67,1.48-1.48,1.48 H8.48c-0.81,0-1.48-0.67-1.48-1.48v-9.76C6.99,43.84,7.66,43.17,8.48,43.17L8.48,43.17z M84.78,23.58h29.63 c0.82,0,1.48,0.67,1.48,1.48v9.76c0,0.81-0.67,1.48-1.48,1.48H84.78c-0.81,0-1.48-0.67-1.48-1.48v-9.76 C83.29,24.25,83.96,23.58,84.78,23.58L84.78,23.58z M46.8,23.58h29.28c0.82,0,1.48,0.67,1.48,1.48v9.76c0,0.81-0.67,1.48-1.48,1.48 H46.8c-0.81,0-1.48-0.67-1.48-1.48v-9.76C45.31,24.25,45.98,23.58,46.8,23.58L46.8,23.58z" fill="#000000" opacity="0.3"/></g></svg>
													</span>
												</div>
												<!--end::Icon-->
												<!--begin::Content-->
												<div class="d-flex flex-column">
													<span href="#" class="text-dark text-hover-primary font-weight-bold font-size-h4 mb-3">Report</span>
													<div class="text-dark-75" style="height:38px">Showing Your Ticket Request Based on Status and Time Period</div>
												</div>
												<!--end::Content-->
											</div>
										</div>
									</div>

									<!--end::Callout-->
								</div>

							</div>
							<div class="row mt-10">

								<div class="col-lg-4">
									<!--begin::Callout-->
									<?php $tickets = DB::table('ticket')->where('next_approval_id', Auth::user()->person)->count();?>
									<div style="cursor: pointer;background-color: rgb(139 195 74 / 0.2) !important;" class="card card-custom {{count($list_bawahan) > 0 ? 'wave wave-animate-slow wave-primary' : ''}} mb-8 mb-lg-0" style="">
										<div class="card-body onclick-approve" onclick="on_click_approve()">
											<div class="d-flex align-items-center p-5">
												<!--begin::Icon-->
												<div class="mr-6">
													<span class="svg-icon svg-icon-primary svg-icon-5x">
														<!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\Communication\Clipboard-check.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
															<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																<rect x="0" y="0" width="24" height="24"/>
																<path style="fill: #9bce5b !important;" d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="#000000" opacity="0.3"/>
																<path style="fill: #a4d06b !important;" d="M10.875,15.75 C10.6354167,15.75 10.3958333,15.6541667 10.2041667,15.4625 L8.2875,13.5458333 C7.90416667,13.1625 7.90416667,12.5875 8.2875,12.2041667 C8.67083333,11.8208333 9.29375,11.8208333 9.62916667,12.2041667 L10.875,13.45 L14.0375,10.2875 C14.4208333,9.90416667 14.9958333,9.90416667 15.3791667,10.2875 C15.7625,10.6708333 15.7625,11.2458333 15.3791667,11.6291667 L11.5458333,15.4625 C11.3541667,15.6541667 11.1145833,15.75 10.875,15.75 Z" fill="#000000"/>
																<path style="fill: #bbd897 !important;" d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#000000"/>
															</g>
														</svg><!--end::Svg Icon-->
													</span>
												</div>
												<!--end::Icon-->
												<!--begin::Content-->
												<?php
												$count_approves = DB::table('ticket')
												->whereIn('ticket.status',['Submit for Approval'])
												->where('ticket.next_approval_id', Auth::user()->person)
												->count();

												$count_approves += DB::table('goods_issues')
												->where('next_approver_id', Auth::user()->person)
												->count();

												$count_approves += DB::table('goods_receives')
												->where('next_approver_id', Auth::user()->person)
												->count();

												?>
												<div class="d-flex flex-column">
													<span class="text-dark text-hover-primary font-weight-bold font-size-h4 mb-3">Approve Request <span class="text-danger">{{ $count_approves > 0 ? '(' . $count_approves . ')' : '' }}</span></span>
													<div class="text-dark-75" style="height:38px">View all requests awaiting my approval</div>
												</div>
												<!--end::Content-->
											</div>
										</div>

									</div>

									<!--end::Callout-->
								</div>
								<div class="col-lg-4">
									<!--begin::Callout-->

									<div  style="cursor: pointer;background-color: rgb(103 58 183 / 0.2) !important;" class="card card-custom wave wave-animate-slow wave-success mb-8 mb-lg-0" style="">
										<div class="card-body" onclick="window.location = '{{URL('/').'/ticket-monitoring'}}';">
											<div class="d-flex align-items-center pl-5 pr-0 pt-5 pb-5">
												<!--begin::Icon-->
												<div class="mr-6">
													<span class="svg-icon svg-icon-success svg-icon-5x">
														<!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\Communication\Urgent-mail.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
															<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																<rect x="0" y="0" width="24" height="24"/>
																<path style="fill: #6b4a9e !important;" d="M12.7037037,14 L15.6666667,10 L13.4444444,10 L13.4444444,6 L9,12 L11.2222222,12 L11.2222222,14 L6,14 C5.44771525,14 5,13.5522847 5,13 L5,3 C5,2.44771525 5.44771525,2 6,2 L18,2 C18.5522847,2 19,2.44771525 19,3 L19,13 C19,13.5522847 18.5522847,14 18,14 L12.7037037,14 Z" fill="#000000" opacity="0.3"/>
																<path style="fill: #8e78b1 !important;" d="M9.80428954,10.9142091 L9,12 L11.2222222,12 L11.2222222,16 L15.6666667,10 L15.4615385,10 L20.2072547,6.57253826 C20.4311176,6.4108595 20.7436609,6.46126971 20.9053396,6.68513259 C20.9668779,6.77033951 21,6.87277228 21,6.97787787 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,6.97787787 C3,6.70173549 3.22385763,6.47787787 3.5,6.47787787 C3.60510559,6.47787787 3.70753836,6.51099993 3.79274528,6.57253826 L9.80428954,10.9142091 Z" fill="#000000"/>
															</g>
														</svg><!--end::Svg Icon-->
													</span>
												</div>
												<!--end::Icon-->
												<!--begin::Content-->
												<div class="d-flex flex-column">
													<span href="#" class="text-dark text-hover-primary font-weight-bold font-size-h4 mb-3" >Tracking Your Ticket</span>
													<div class="text-dark-75" style="height:38px">Monitoring Your Submit <br/>Ticket</div>
												</div>
												<!--end::Content-->
											</div>
										</div>
									</div>

									<!--end::Callout-->
								</div>
								<div class="col-lg-4">
									<!--begin::Callout-->

									<div  style="cursor: pointer;background-color: rgb(63 81 181 / 0.2) !important;" class="card card-custom wave wave-animate-slow wave-warning mb-8 mb-lg-0" >
										<div class="card-body"  onclick="window.location = '{{URL('/').'/faq'}}';">

											<div class="d-flex align-items-center p-5">
												<!--begin::Icon-->
												<div class="mr-6">
													<span class="svg-icon svg-icon-warning svg-icon-5x">
														<!--begin::Svg Icon | path:assets/media/svg/icons/Home/Mirror.svg-->
														<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
															<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																<rect x="0" y="0" width="24" height="24" />
																<path style="fill: #8f94ce !important;" d="M13,17.0484323 L13,18 L14,18 C15.1045695,18 16,18.8954305 16,20 L8,20 C8,18.8954305 8.8954305,18 10,18 L11,18 L11,17.0482312 C6.89844817,16.5925472 3.58685702,13.3691811 3.07555009,9.22038742 C3.00799634,8.67224972 3.3975866,8.17313318 3.94572429,8.10557943 C4.49386199,8.03802567 4.99297853,8.42761593 5.06053229,8.97575363 C5.4896663,12.4577884 8.46049164,15.1035129 12.0008191,15.1035129 C15.577644,15.1035129 18.5681939,12.4043008 18.9524872,8.87772126 C19.0123158,8.32868667 19.505897,7.93210686 20.0549316,7.99193546 C20.6039661,8.05176407 21.000546,8.54534521 20.9407173,9.09437981 C20.4824216,13.3000638 17.1471597,16.5885839 13,17.0484323 Z" fill="#000000" fill-rule="nonzero" />
																<path style="fill: #6c76e2 !important;" d="M12,14 C8.6862915,14 6,11.3137085 6,8 C6,4.6862915 8.6862915,2 12,2 C15.3137085,2 18,4.6862915 18,8 C18,11.3137085 15.3137085,14 12,14 Z M8.81595773,7.80077353 C8.79067542,7.43921955 8.47708263,7.16661749 8.11552864,7.19189981 C7.75397465,7.21718213 7.4813726,7.53077492 7.50665492,7.89232891 C7.62279197,9.55316612 8.39667037,10.8635466 9.79502238,11.7671393 C10.099435,11.9638458 10.5056723,11.8765328 10.7023788,11.5721203 C10.8990854,11.2677077 10.8117724,10.8614704 10.5073598,10.6647638 C9.4559885,9.98538454 8.90327706,9.04949813 8.81595773,7.80077353 Z" fill="#000000" opacity="0.3" />
															</g>
														</svg>
														<!--end::Svg Icon-->
													</span>
												</div>
												<!--end::Icon-->
												<!--begin::Content-->
												<div class="d-flex flex-column">
													<span href="#" class="text-dark text-hover-primary font-weight-bold font-size-h4 mb-3">FAQ &amp; Tutorials</span>
													<div class="text-dark-75" style="height:38px">Lookup FAQ to fix issues on your own</div>
												</div>
												<!--end::Content-->
											</div>
										</div>

									</div>

									<!--end::Callout-->
								</div>

							</div>


							<div class="row mt-10 d-none">
								<div class="col-xl-6" >
									<!--begin::Engage Widget 12-->
									<div style="height:180px" class="card card-custom card-stretch card-stretch-half gutter-b overflow-hidden">
										<div class="card-body p-0 d-flex rounded bg-light-info">
											<div class="py-16 px-12">
												<h3 class="font-size-h4">
													<a href="#" class="text-dark font-weight-bolder">Report an Issue</a>
												</h3>
												<div class="font-size-h5 text-success" style="width:300px">Having Trouble ? Contact The Support Team</div>
											</div>
											<div class="d-none d-md-flex flex-row-fluid bgi-no-repeat bgi-position-y-center bgi-position-x-left bgi-size-contain" style="transform: scale(1.1) rotate(0deg);background-image: url(assets/media/svg/humans/custom-4.svg);
												background-position-x: right;
												opacity: 0.9;
												background-position-y: 40px;
												"></div>
										</div>
									</div>
									<!--end::Engage Widget 12-->
								</div>
								<div class="col-xl-6" style="height:180px">

									<!--begin::Engage Widget 12-->
									<div style="height:180px" class="card card-custom card-stretch card-stretch-half gutter-b overflow-hidden">
										<div class="card-body p-0 d-flex rounded bg-light-info">
											<div class="py-16 px-12">
												<h3 class="font-size-h4">
													<a href="#" class="text-dark font-weight-bolder">Request a Service</a>
												</h3>
												<div class="font-size-h5 text-danger"  style="width:300px">Raise a Request for a new device, software or service</div>
											</div>
											<div class="d-none d-md-flex flex-row-fluid bgi-no-repeat bgi-position-y-center bgi-position-x-left bgi-size-contain" style="transform: scale(1.1) rotate(0deg); background-image: url('assets/media/svg/patterns/rhone.svg');background-position-x: right;"></div>
										</div>
									</div>
									<!--end::Engage Widget 12-->
								</div>
							</div>

							<div class="row mt-6 d-none">
								<div class="col-xl-4">
									<!--begin: Stats Widget 19-->
									<div class="card card-custom bg-light-success card-stretch gutter-b">
										<!--begin::Body-->
										<div class="card-body my-3">
											<a href="#" class="card-title font-weight-bolder text-success text-hover-state-dark font-size-h6 mb-4 d-block">SAP UI Progress</a>
											<div class="font-weight-bold text-muted font-size-sm">
											<span class="text-dark-75 font-size-h2 font-weight-bolder mr-2">67%</span>Average</div>
											<div class="progress progress-xs mt-7 bg-success-o-60">
												<div class="progress-bar bg-success" role="progressbar" style="width: 67%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
											</div>
										</div>
										<!--end:: Body-->
									</div>
									<!--end: Stats:Widget 19-->
								</div>
								<div class="col-xl-4">
									<!--begin::Stats Widget 20-->
									<div class="card card-custom bg-light-warning card-stretch gutter-b">
										<!--begin::Body-->
										<div class="card-body my-4">
											<a href="#" class="card-title font-weight-bolder text-warning font-size-h6 mb-4 text-hover-state-dark d-block">Airplus Budget</a>
											<div class="font-weight-bold text-muted font-size-sm">
											<span class="text-dark-75 font-weight-bolder font-size-h2 mr-2">87K%</span>23k to goal</div>
											<div class="progress progress-xs mt-7 bg-warning-o-60">
												<div class="progress-bar bg-warning" role="progressbar" style="width: 87%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
											</div>
										</div>
										<!--end::Body-->
									</div>
									<!--end::Stats Widget 20-->
								</div>
								<div class="col-xl-4">
									<!--begin::Stats Widget 21-->
									<div class="card card-custom bg-light-info card-stretch gutter-b">
										<!--begin::ody-->
										<div class="card-body my-4">
											<a href="#" class="card-title font-weight-bolder text-info font-size-h6 mb-4 text-hover-state-dark d-block">Customer</a>
											<div class="font-weight-bold text-muted font-size-sm">
											<span class="text-dark-75 font-weight-bolder font-size-h2 mr-2">52,450</span>48k to goal</div>
											<div class="progress progress-xs mt-7 bg-info-o-60">
												<div class="progress-bar bg-info" role="progressbar" style="width: 52%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
											</div>
										</div>
										<!--end::Body-->
									</div>
									<!--end::Stats Widget 21-->
								</div>
							</div>

						</div>
						<!--end::Section-->


						<!-- START : Statistic -->

<style>
.container-padding {

}
</style>
<script>
	$( ".box-content-home .container" ).each(function( index ) {
	  console.log( index + ": " + $( this ).text() );
	  $(this).addClass("container-fluid pl-11 pr-11");
	  $(this).addClass("container-padding");
	  $(this).removeClass("container");
	});
$( document ).ready(function() {
	$( ".box-content-home .container" ).each(function( index ) {
	  console.log( index + ": " + $( this ).text() );
	  $(this).addClass("container-fluid pl-11 pr-11");
	  $(this).addClass("container-padding");
	  $(this).removeClass("container");
	});
	//setTimeout(function() {
	// var cw = $('#card-body-1').width();
	// $('.card-body-row2').css({
	// 	'height': (cw+40) + 'px'
	// });
    // Your code here
//}, 5000);

});
</script>
<hr>
<div class="container-fluid">

<script>
//makechart('container-chart2',total_ticket,data_json1);
//makechart('container-chart3',total_ticket,data_json1);
// Create the chart
function makechart(id_element,total_ticket,data_json) {
	var element_type;
	console.log(data_json);

	switch(id_element) {
		case 'container-chart':
			element_type = 'incident_management';
			break;
		case 'container-chart2':
			element_type = 'service_request';
			break;
		default:
			element_type = 'problem_request';
			break;
	}

    Highcharts.setOptions({
        //colors: ['#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4']
        colors: ['#76CDE1', '#FEAC3A', '#00774A', '#FD5852', '#FFA800', '#D9598C'],
        series: {
            shadow: true
        }
    });
	Highcharts.chart(id_element, {
	  chart: {
		type: 'pie',
		// height:'80%',
	  },
		title: {
			text: '<h2 style="font-weight:600;color:#1F6DD1;font-size:19px">'+total_ticket+'</h2><br/><h5 style="font-size:15px">Tickets</h5>',
			align: 'center',
			verticalAlign: 'middle',
			y: 15,
			x:0,
		},
	  //title: {
		//text: 'Browser market shares. January, 2018'
	  //},
	  subtitle: {
		//text: 'Click the slices to view versions. Source: <a href="http://statcounter.com" target="_blank">statcounter.com</a>'
	  },

	  accessibility: {
		announceNewData: {
		  enabled: true
		},
		point: {
		  valueSuffix: ''
		}
	  },
    plotOptions: {
			series: {
                dataLabels: {
                    enabled: true,
                    format: '{point.name}: {point.y}'
                }
			},
			pie: {
				borderWidth: 0,
				fillColor:"#ffede5",
                // size:'100%',
                // height: '100%',
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
			}
		},
	  tooltip: {
		headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
		pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> of total<br/>'
	  },

	  series: [{
	//name: 'Browser share',
		innerSize: '50%',
		name: "Total Ticket",
		colorByPoint: true,
		data: data_json,
		point:{
			events:{
				click: function (event) {
					var name = this.name
					var select_requester = $('.multiple-agent :selected').toArray().map(item => item.value).join('|');
					var params = "&start_date="+$('#kt_start_date').val()+"&end_date="+$('#kt_end_date').val()+"&requester="+select_requester+"";

					var url = "{{URL('/')}}/ticket-monitoring?status_ticket="+name+"&ticket_type="+element_type+params;
					window.open(url, '_blank').focus();
					//window.location.href = ;
				}
			}
		}
		//[
		//{
			//name: "Medium",
			//y: 62.74,
			//drilldown: "Chrome"
		  //},
		  //{
			//name: "Firefox",
			//y: 10.57,
			//drilldown: "Firefox"
		  //},
		//]
	  }],
	});
}
</script>

<style>
.dash-number {
	font-size: 1.7rem;
    font-weight: 600;
}
.dash-title {
    font-size: 1.1rem;
    font-weight: 600;
}
.dash-box .card-body {
	padding:1.3rem 1.25rem;
}
</style>
<script>
$(document).ready(function(){

	var total_incident_chart = 0;
	var total_service_chart = 0;
	$(".apply").on('click', function() {
		KTApp.block('#kt_blockui_content', {
			overlayColor: '#000000',
			state: 'primary',
			message: 'Processing...'
        });

		var select_requester = $('.multiple-agent :selected').toArray().map(item => item.value).join('|');


		setTimeout(function() {
			console.log("{{URL('/')}}/dashboard_reload?start_date="+$('#kt_start_date').val()+"&end_date="+$('#kt_end_date').val()+"&requester="+select_requester+"");
			$.ajax({
				type: 'GET',
				url: "{{URL('/')}}/dashboard_reload?start_date="+$('#kt_start_date').val()+"&end_date="+$('#kt_end_date').val()+"&requester="+select_requester+"",
				success: function(data) {
					$(".dashboard-count").html(data);
				},
				error: function() {
					console.log('error');
				}
			});

			//// incident
			$.ajax({
				type: 'GET',
				url: "{{URL('/')}}/dashboard_chart_reload?type=incident_management&start_date="+$('#kt_start_date').val()+"&end_date="+$('#kt_end_date').val()+"&requester="+select_requester+"",
				success: function(result) {
					makechart('container-chart',result.total,result.data);
					total_incident_chart = result.total;
					$(".dash-number-large").html(total_incident_chart+total_service_chart);
				},
				error: function() {
					console.log('error');
				}
			});

			//// service
			$.ajax({
				type: 'GET',
				url: "{{URL('/')}}/dashboard_chart_reload?type=service_request&start_date="+$('#kt_start_date').val()+"&end_date="+$('#kt_end_date').val()+"&requester="+select_requester+"",
				success: function(result) {
					makechart('container-chart2',result.total,result.data);
					total_service_chart = result.total;
					$(".dash-number-large").html(total_incident_chart+total_service_chart);
				},
				error: function() {
					console.log('error');
				}
			});

			KTApp.unblock('#kt_blockui_content');
		}, 500)
	})

});
</script>

<style>
.select2-selection__rendered {
	height: auto;
	display: block !important;
}
.select2-selection--multiple {
	height: auto !important;
}
</style>
<div class="form-group row">
	<div class="col-lg-4 col-md-4 col-sm-4">
		<select class="form-control select2 multiple-agent" id="kt_select2_3" name="agent[]" multiple="multiple">
			<option value="{{Auth::user()->person ?? 0}}">My Self</option>
			<option value="{{ implode('|', array_column($list_bawahan, 'id')) }}">All Subordinates</option>
			@if (Auth::user()->person && count($list_bawahan) > 0)
			<optgroup label="My Subordinates">
				@foreach($list_bawahan as $agent)
				<option value="{{$agent->id ?? '-'}}">{{$agent->name ?? '-'}}</option>
				@endforeach
			</optgroup>
			@endif
		</select>
	</div>
	<!--begin::Daterange-->
	<!-- <button id="kt_daterangepicker_6" class="btn btn-sm btn-secondary font-weight-bold mr-2" >
		<span class="text-muted font-size-base font-weight-bold mr-2" id="kt_title_6">Today</span>
		<span class="text-success font-size-base font-weight-bolder" id="kt_range_6">{{ date('d M Y') }}</span>
	</button>

	<input type="hidden" id="kt_start_date" value=""/>
	<input type="hidden" id="kt_end_date" value="{{ date('Y-m-d') }}"/>
	<button class="btn btn-success apply">Apply</button> -->

	<button class="btn btn-sm btn-secondary font-weight-bold mr-2" id="kt_dashboard_daterangepicker" title="Select dashboard daterange" data-placement="left">
		<!-- <span class="text-muted font-size-base font-weight-bold mr-2" id="kt_dashboard_daterangepicker_title">Today</span>
		<span class="text-success font-size-base font-weight-bolder" id="kt_dashboard_daterangepicker_date">{{ date('M d') }}</span> -->
		<span class="text-muted font-size-base font-weight-bold mr-2" id="kt_title_6">Today</span>
		<span class="text-success font-size-base font-weight-bolder" id="kt_range_6">{{ date('d M Y') }}</span>
	</button>
	<!-- <input type="hidden" class="kt_dashboard_daterangepicker_date_start"/>
	<input type="hidden" class="kt_dashboard_daterangepicker_date_end"/> -->
	<input type="hidden" id="kt_start_date" value=""/>
	<input type="hidden" id="kt_end_date" value="{{ date('Y-m-d') }}"/>
	<button class="btn btn-success apply">Apply</button>


	<!--end::Daterange-->

	<script>
	// Class definition
	var KTBootstrapDaterangepicker = function () {

	 // Private functions
	 var demos = function () {
	  // predefined ranges
	  var start = moment();
	  var end = moment();

	  $('#kt_daterangepicker_6').daterangepicker({
	   buttonClasses: ' btn',
	   applyClass: 'btn-primary',
	   cancelClass: 'btn-secondary',
	   startDate: start,
	   endDate: end,
	   ranges: {
	   'Today': [moment(), moment()],
	   'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
	   'Last 7 Days': [moment().subtract(6, 'days'), moment()],
	   'Last 30 Days': [moment().subtract(29, 'days'), moment()],
	   'This Month': [moment().startOf('month'), moment().endOf('month')],
	   'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
	   }
	  }, function(start, end, label) {
		if(label == "Custom Range") {
			$('#kt_title_6').html( "Range");
			$('#kt_range_6').html( start.format('DD MMM YYYY') + ' - ' + end.format('DD MMM YYYY'));
			$('#kt_start_date').val( start.format('YYYY-MM-DD'));
			$('#kt_end_date').val( end.format('YYYY-MM-DD'));
		} else if(label == "Today" || label == "Yesterday") {
			$('#kt_title_6').html( label);
			$('#kt_range_6').html( end.format('DD MMM YYYY'));
			$('#kt_start_date').val("");
			$('#kt_end_date').val( end.format('YYYY-MM-DD'));
		} else {
			$('#kt_title_6').html( label);
			$('#kt_range_6').html( start.format('DD MMM YYYY') + ' - ' + end.format('DD MMM YYYY'));
			$('#kt_start_date').val( start.format('YYYY-MM-DD'));
			$('#kt_end_date').val( end.format('YYYY-MM-DD'));
			//$('#kt_daterangepicker_6').html( label);
		}
	  });
	 }

	 return {
	  // public functions
	  init: function() {
	   demos();
	  }
	 };
	}();

	jQuery(document).ready(function() {
	 KTBootstrapDaterangepicker.init();
	});
	</script>

</div>

<div id="kt_blockui_content"></div>

<div class="row dashboard-count">
	<div class="dash-box col pl-1 pr-1">

		<div class="card ml-2">
		  <div class="card-body">
		  <h3 class="dash-title mb-5">Overdue</h3>
		  <?php
			$count = DB::table('ticket')//
						->whereRaw(' (created_by_contact = ? OR requester = ?) ',[Auth::user()->person,Auth::user()->person])
						->whereIn('ticket.status',['Open','Re-Open','On Progress'])
						->whereRaw('due_date < NOW()')
						->count();

		  ?>
			<a class="dash-number text-danger" href="{{URL('/')}}/ticket-monitoring?state=overdue">{{$count}}</a><?php //old filter &status_ticket=Open, On Progress&ticket_type=overdue   &requester={{implode('|',$array_pl)}}?>
		  </div>
		</div>
	</div>
	<div class="dash-box col pl-1 pr-1">
		<div class="card ">
		  <div class="card-body">
		  <h3 class="dash-title mb-5">Due Today</h3>
		  <?php
			$count = DB::table('ticket')
            ->whereRaw('(created_by_contact = ? OR requester = ?)', [Auth::user()->person, Auth::user()->person])
            ->whereIn('ticket.status', ['Open', 'Re-Open', 'On Progress'])
            ->whereRaw('(due_date >= NOW() AND CURRENT_DATE = DATE(due_date))')
            ->count();
		  ?>
			<a class="dash-number text-info" href="{{URL('/')}}/ticket-monitoring?state=due_today">{{$count}}</a><?php //old filter &status_ticket=Open,On Progress&ticket_type=due_today  &requester={{implode('|',$array_pl)}}?>
		  </div>
		</div>
	</div>
	<div class="dash-box col pl-1 pr-1">
		<div class="card ">
		  <div class="card-body">
		  <h3 class="dash-title mb-5">Open Tickets</h3>
		  <?php
			$count_open = DB::table('ticket')
						->whereIn('ticket.status',['Open'])
						->whereRaw(' (created_by_contact = ? OR requester = ?) ',[Auth::user()->person,Auth::user()->person])
						->count();
						//->whereIn('ticket.requester', $array_pl)
						//->whereBetween('created_at', [$first, $last])
		  ?>
			<a class="dash-number text-info" href="{{URL('/')}}/ticket-monitoring?status_ticket=Open">{{$count_open}}</a> <?php //old filter &requester={{implode('|',$array_pl)}} ?>
		  </div>
		</div>
	</div>
	<div class="dash-box col pl-1 pr-1">
		<div class="card ">
		  <div class="card-body">
		  <h3 class="dash-title mb-5">On Progress</h3>
		  <?php
			$count_onprogress = DB::table('ticket')
						->whereRaw(' (created_by_contact = ? OR requester = ?) ',[Auth::user()->person,Auth::user()->person])
						->whereIn('ticket.status',['On Progress'])
						->count();
						//->whereIn('ticket.requester', $array_pl)
						//->whereBetween('created_at', [$first, $last])
		  ?>
			{{-- <a class="dash-number text-info" href="{{URL('/')}}/ticket-monitoring?status_ticket=Waiting for User">{{$count}}</a> --}}
			<a class="dash-number text-info" href="{{URL('/')}}/ticket-monitoring?status_ticket=On Progress">{{$count_onprogress}}</a> <?php //old filter &requester={{implode('|',$array_pl)}} ?>
		  </div>
		</div>
	</div>
    <div class="dash-box col pl-1 pr-1">
		<div class="card ">
		  <div class="card-body">
		  <h3 class="dash-title mb-5">Resolved</h3>
		  <?php
			$count_resolved = DB::table('ticket')
						->whereRaw(' (created_by_contact = ? OR requester = ?) ',[Auth::user()->person,Auth::user()->person])
						->whereIn('ticket.status',['Resolved'])
						->count();
						//->whereIn('ticket.requester', $array_pl)
						//->whereBetween('created_at', [$first, $last])
		  ?>
			<a href="{{URL('/')}}/ticket-monitoring?status_ticket=Resolved" class="dash-number text-info">{{$count_resolved}}</a> <?php //old filter &requester={{implode('|',$array_pl)}} ?>
		  </div>
		</div>
	</div>
    <div class="dash-box col pl-1 pr-1">
		<div class="card">
		  <div class="card-body">
		  <h3 class="dash-title mb-5">Closed</h3>
		  <?php
			$count_closed = DB::table('ticket')
						->whereIn('ticket.status',['Closed'])
						->whereRaw(' (created_by_contact = ? OR requester = ?) ',[Auth::user()->person,Auth::user()->person])
						->count();
						//->whereIn('ticket.requester', $array_pl)
						//->whereBetween('created_at', [$first, $last])
		  ?>
			<a class="dash-number text-info" href="{{URL('/')}}/ticket-monitoring?status_ticket=Closed">{{$count_closed}}</a> <?php //&requester={{implode('|',$array_pl)}} ?>
		  </div>
		</div>
	</div>
    <div class="dash-box col pl-1 pr-1">
		<div class="card">
		  <div class="card-body">
		  <h3 class="dash-title mb-5">All Tickets</h3>
			<a class="dash-number text-info" href="{{URL('/')}}/ticket-monitoring?status_ticket=all">{{$count_open + $count_onprogress + $count_resolved + $count_closed}}</a> <?php //&requester={{implode('|',$array_pl)}} ?>
		  </div>
		</div>
	</div>
</div>
<div class="row home-content-chart">

					<style>
					.highcharts-figure, .highcharts-data-table table {
						min-width: 320px;
						max-width: 660px;
						margin: 1em auto;
					}

					.highcharts-data-table table {
						font-family: Verdana, sans-serif;
						border-collapse: collapse;
						border: 1px solid #EBEBEB;
						margin: 10px auto;
						text-align: center;
						width: 100%;
						max-width: 500px;
					}
					.highcharts-data-table caption {
						padding: 1em 0;
						font-size: 1.2em;
						color: #555;
					}
					.highcharts-data-table th {
						font-weight: 600;
						padding: 0.5em;
					}
					.highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
						padding: 0.5em;
					}
					.highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
						background: #f8f8f8;
					}
					.highcharts-data-table tr:hover {
						background: #f1f7ff;
					}
					.highcharts-credits {
						display:none;
					}
					</style>

					<script src="https://code.highcharts.com/highcharts.js"></script>
					<script src="https://code.highcharts.com/modules/data.js"></script>
					<script src="https://code.highcharts.com/modules/drilldown.js"></script>
					<!--
					<script src="https://code.highcharts.com/modules/exporting.js"></script>
					-->
					<script src="https://code.highcharts.com/modules/export-data.js"></script>
					<script src="https://code.highcharts.com/modules/accessibility.js"></script>



					<div id="dash-col-1" class="dash-row-2 dash-box col pl-3 pr-1 pt-2">
						<!--begin::Card-->
						<div class="card card-custom gutter-b">
							<div class="card-body card-body-row2"  id="card-body-1">
									<h3 class="dash-title mb-5">Incident Ticket</h3>
									<!--start::Chart-->
									<figure class="highcharts-figure">
										<div id="container-chart"></div>
									</figure>
									<!--end::Chart-->
							</div>
						</div>
						<!--end::Card-->
					</div>
					<div class="dash-row-2 dash-box col pl-3 pr-1 pt-2">
						<!--begin::Card-->
						<div class="card card-custom gutter-b">
							<div class="card-body card-body-row2"  id="card-body-2">
									<h3 class="dash-title mb-5">Service Ticket</h3>
									<!--start::Chart-->
									<figure class="highcharts-figure">
										<div id="container-chart2"></div>
									</figure>
									<!--end::Chart-->
							</div>
						</div>
						<!--end::Card-->
					</div>


					<?php
					//INCIDENT
					$map_color = [
						'Open' => '#76CDE1',
						'On Progress' => '#FEAC3A',
						'Resolved' => '#FD5852',
						'Closed' => '#00774A'
					];
					$list_priority = DB::table('sla_priority')->get();
					$list_status = ['Open','On Progress','Resolved','Closed'];
					$list_status_with_reopen = ['Open','Re-Open','On Progress','Resolved','Closed'];
					$arr_priority =[];
					$total = DB::table('ticket')->whereIn('ticket.status',$list_status_with_reopen)//['Submit for Approval','Rejected','Waiting for User','Open',])
												->where('finalclass','incident_management')
												->whereRaw(' (created_by_contact = ? OR requester = ?) ',[Auth::user()->person,Auth::user()->person])
                                                ->count();

												//->whereBetween('created_at', [$first, $last])
												// ->where('created_by', Auth::user()->id)
												//->whereIn('ticket.requester', $array_pl)
					$total1  = $total;

					// Problem
					$total_problem = DB::table('ticket')->whereIn('ticket.status',$list_status_with_reopen)//['Submit for Approval','Rejected','Waiting for User','Open',])
												->where('finalclass','problem_request')
												->whereRaw(' (created_by_contact = ? OR requester = ? OR agent_id = ?) ',[Auth::user()->person,Auth::user()->person,Auth::user()->person])
                                                ->count();

												//->whereBetween('created_at', [$first, $last])
												// ->where('created_by', Auth::user()->id)
												//->whereIn('ticket.requester', $array_pl)
					$total3  = $total_problem;
					foreach($list_status as $p) {
					//foreach($list_priority as $p) {
						$count = DB::table('ticket')//->where('status',$p)
													->where('status', 'ilike', '%'.$p.'%') //dgn pakai like Re-Open masuk ke Open jadi lebih praktis tidak usah pakai if
													->where('finalclass','incident_management')
													->whereRaw(' (created_by_contact = ? OR requester = ?) ',[Auth::user()->person,Auth::user()->person])
													->count();
													//->whereIn('ticket.status',['Submit for Approval','Rejected','Waiting for User','Open',])
													//->where('priority',$p->priority)
													// ->where('created_by', Auth::user()->id)
													//->whereIn('ticket.requester', $array_pl)
													//->whereBetween('created_at', [$first, $last])
						if($count) {
							//$arr_priority[] = ['name'=>$p->priority,'y'=>$count
													////,'drilldown'=>$p->priority
												//];
							$arr_priority[] = ['name'=>$p,'y'=>$count, 'color' => $map_color[$p] ];
						}
					}
					//var_dump($arr_priority);
					//echo json_encode($arr_priority);
					?>
					<script>
					var total_ticket = {{$total}};
					var data_json1 = <?=json_encode($arr_priority)?>;
					makechart('container-chart',total_ticket,data_json1);
					</script>

					<?php
					//SERVICE
					$list_priority = DB::table('sla_priority')->get();
					$list_status = ['Open','On Progress','Resolved','Closed'];
					$arr_priority =[];
					$total = DB::table('ticket')->whereIn('ticket.status',$list_status_with_reopen)
												->whereRaw(' (created_by_contact = ? OR requester = ?) ',[Auth::user()->person,Auth::user()->person])
												->where('finalclass','service_request')->count();

												//['Submit for Approval','Rejected','Waiting for User','Open',])
												// ->where('created_by', Auth::user()->id)
												//->whereIn('ticket.requester', $array_pl)
					$total2 = $total;
					foreach($list_status as $p) {
					//foreach($list_priority as $p) {
						$count = DB::table('ticket')
													//->where('status',$p)
													->where('status', 'ilike', '%'.$p.'%') //dgn pakai like Re-Open masuk ke Open jadi lebih praktis tidak usah pakai if
													->where('finalclass','service_request')
													->whereRaw(' (created_by_contact = ? OR requester = ?) ',[Auth::user()->person,Auth::user()->person])
													->count();

													//->whereIn('ticket.status',['Submit for Approval','Rejected','Waiting for User','Open',])
													//->where('priority',$p->priority)
													// ->where('created_by', Auth::user()->id)
													//->whereIn('ticket.requester', $array_pl)
						if($count) {
							//$arr_priority[] = ['name'=>$p->priority,'y'=>$count
													////,'drilldown'=>$p->priority
												//];
							$arr_priority[] = ['name'=>$p,'y'=>$count, 'color' => $map_color[$p]];
						}
					}
					//var_dump($arr_priority);
					//echo json_encode($arr_priority);
					?>
					<script>
					total_ticket = {{$total}};
					data_json1 = <?=json_encode($arr_priority)?>;
					makechart('container-chart2',total_ticket,data_json1);
					</script>

</div>




</div><!-- close container-fluid -->

						<!-- END : Statistic -->
@if(true)
<div class="container-fluid">
<div class="row">


<div class="col-lg-6 pl-2 pt-0">
	<!--begin::Advance Table Widget 4-->
	<div class="card  card-stretch gutter-b">
		<!--begin::Header-->
		<div class="card-header border-0 py-5">
			<h3 class="card-title align-items-start flex-column mb-0 mt-2">
				<span class="card-label font-weight-bolder text-dark">List Of Approval</span>
				<span class="text-muted mt-3 font-weight-bold font-size-sm" style="display:block">This is the list than need to Approve by You (5)</span>
			</h3>
			<div class="card-toolbar">

			</div>
		</div>
		<!--end::Header-->
		<!--begin::Body-->
		<div class="card-body pt-0 pb-3">
			<div class="tab-content">
				<!--begin::Table-->
				<div class="table-responsive">
					<table class="table table-head-custom table-head-bg table-borderless table-vertical-center">
						<thead>
							<tr class="text-left text-uppercase">
								<th style="min-width: 150px" class="pl-7">
									<span class="text-dark-75">requester / date</span>
								</th>
								<th style="min-width: 100px">service /ticket number</th>
								<th style="min-width: 100px">status</th>
								<th style="min-width: 100px">category</th>
								<th style="min-width: 80px">Action</th>
							</tr>
						</thead>
						<tbody>

							<?php
							$color = ['info','success','warning','primary','danger'];
							$list_ticket = DB::table('ticket')
											->select('ticket.*','service.name AS service_name')
											->join('service', 'service.id', '=', 'ticket.service_id')
											->whereIn('ticket.status',['Submit for Approval'])
											->where('ticket.next_approval_id', Auth::user()->person)
											->orderBy('ticket.created_at','desc')
											->limit(5)
											->get();
							$n = 0;
							?>
							@foreach($list_ticket as $ticket)
									<?php
										$name = DB::table('users')->where('id', $ticket->created_by)->value('name');
									?>
							<tr>

								<td class="pl-0 py-8">
									<div class="d-flex align-items-center">
										<?php
										$acronym = substr($name,0,1);
										//$words = explode(" ", $name);
										//$acronym = "";

										//foreach ($words as $w) {
										  //$acronym .= $w[0];
										//}
										?>
										<div class="mr-4 symbol symbol-40 symbol-light-{{$color[(($n++)%5)]}} flex-shrink-0">
											<span class="symbol-label font-size-h4 font-weight-bold">{{$acronym}}</span>
										</div>
										<div>
											<a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">{{ $name }}</a>
											<span class="text-muted font-weight-bold d-block">{{ date('d M Y', strtotime($ticket->created_at)) }}</span>
										</div>
									</div>
								</td>
								<td>
									<span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{$ticket->service_name}}</span>
									<span class="text-muted font-weight-bold">{{$ticket->ref}}</span>
								</td>
								<td>
									<span class="label label-lg label-light-danger label-inline pt-1 pb-1" style="height: auto;text-align: center;">{{$ticket->status}}</span>
								</td>
								<td>
									<?php
										$category = DB::table('service_category')->where('id', $ticket->servicesubcategory_id)->first();
									?>
									<span class="text-dark-75 font-weight-bolder d-block font-size-lg">
										@if($ticket->finalclass == 'incident_management')
											{{'Incident Request'}}
										@elseif($ticket->finalclass == 'service_request')
											{{'Service Request'}}
										@elseif($ticket->finalclass == 'problem')
											{{'Problem Request'}}
										@endif
										</span>
									<span class="text-muted font-weight-bold">
										{{ isset($category->name) ? $category->name : '-' }}
									</span>
								</td>

								<td class="pr-0 text-right">
									<a href="{{URL('/')}}/approve-request/{{$ticket->token}}" class="btn btn-light-success font-weight-bolder font-size-sm">Detail</a>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<!--end::Table-->
			</div>
		</div>
		<!--end::Body-->
	</div>
	<!--end::Advance Table Widget 4-->

	<!--begin::Advance Table Widget 4-->
	<div class="card  card-stretch gutter-b">
		<!--begin::Header-->
		<div class="card-header border-0 py-5">
			<h3 class="card-title align-items-start flex-column mb-0 mt-2">
				<span class="card-label font-weight-bolder text-dark">List of Latest Ticket</span>
				<span class="text-muted mt-3 font-weight-bold font-size-sm" style="display:block">This is latest ticket that is part of you</span>
			</h3>
			<div class="card-toolbar">

			</div>
		</div>
		<!--end::Header-->
		<!--begin::Body-->
		<div class="card-body pt-0 pb-3">
			<div class="tab-content">
				<!--begin::Table-->
				<div class="table-responsive">
					<table class="table table-head-custom table-head-bg table-borderless table-vertical-center">
						<thead>
							<tr class="text-left text-uppercase">
								<th style="min-width: 150px" class="pl-7">
									<span class="text-dark-75">requester / date</span>
								</th>
								<th style="min-width: 100px">service /ticket number</th>
								<th style="min-width: 100px">status</th>
								<th style="min-width: 100px">category</th>
								<th style="min-width: 80px">Action</th>
							</tr>
						</thead>
						<tbody>

							<?php
							$color = ['info','success','warning','primary','danger'];
							$list_ticket = DB::table('ticket')
											->select('ticket.*','service.name AS service_name')
											->leftjoin('service', 'service.id', '=', 'ticket.service_id')
											//->whereIn('ticket.status',['Submit for Approval','Waiting for User'])
											//->where('next_approval_id', Auth::user()->person)
											->where('ticket.created_by', Auth::user()->id)
											->orderBy('ticket.created_at','desc')
											->limit(5)
											->get();
							$n = 0;
							?>
							@foreach($list_ticket as $ticket)
									<?php
										$name = DB::table('users')->where('id', $ticket->created_by)->value('name');
									?>
							<tr>

								<td class="pl-0 py-8">
									<div class="d-flex align-items-center">
										<?php
										$acronym = substr($name,0,1);
										//$words = explode(" ", $name);
										//$acronym = "";

										//foreach ($words as $w) {
										  //$acronym .= $w[0];
										//}
										?>
										<div class="mr-4 symbol symbol-40 symbol-light-{{$color[(($n++)%5)]}} flex-shrink-0">
											<span class="symbol-label font-size-h4 font-weight-bold">{{$acronym}}</span>
										</div>
										<div>
											<a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">{{ $name }}</a>
											<span class="text-muted font-weight-bold d-block">{{ date('d M Y', strtotime($ticket->created_at)) }}</span>
										</div>
									</div>
								</td>
								<td>
									<span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{$ticket->service_name}}</span>
									<span class="text-muted font-weight-bold">{{$ticket->ref}}</span>
								</td>
								<td>
									<?php
									if($ticket->status == "Resolved") {
										$color2 = "success";
									} else if($ticket->status == "Open") {
										$color2 = "danger";
									} else if($ticket->status == "Resolved") {
										$color2 = "success";
									} else  {
										$color2 = "warning";
									}
									?>

									<span class="label label-lg label-light-{{$color2}} label-inline pt-1 pb-1" style="height: auto;text-align: center;">{{$ticket->status}}</span>
								</td>
								<td>
									<?php
										$category = DB::table('service_category')->where('id', $ticket->servicesubcategory_id)->first();
									?>
									<span class="text-dark-75 font-weight-bolder d-block font-size-lg">
										@if($ticket->finalclass == 'incident_management')
											{{'Incident Request'}}
										@elseif($ticket->finalclass == 'service_request')
											{{'Service Request'}}
										@elseif($ticket->finalclass == 'problem')
											{{'Problem Request'}}
										@endif
										</span>
									<span class="text-muted font-weight-bold">
										{{ isset($category->name) ? $category->name : '-' }}
									</span>
								</td>

								<td class="pr-0 text-right">
									<a href="{{URL('/')}}/ticket-monitoring/{{$ticket->token}}" class="btn btn-light-success font-weight-bolder font-size-sm">Detail</a>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<!--end::Table-->
			</div>
		</div>
		<!--end::Body-->
	</div>
	<!--end::Advance Table Widget 4-->


</div>



@if(accessv('dashboard_material_report', 'list', 'return'))
<div class="col-lg-6 pl-6 pt-0 pr-0">
	<!--begin::Advance Table Widget 4-->
	<div class="card  card-stretch gutter-b">
		<!--begin::Header-->
		<div class="card-header border-0 py-5">
			<h3 class="card-title align-items-start flex-column mb-0 mt-2">
				<span class="card-label font-weight-bolder text-dark">Activity Stream</span>

			</h3>
			<div class="card-toolbar">

			</div>
		</div>
		<!--end::Header-->
		<!--begin::Body-->
		<div class="card-body pt-0 pb-3">
					<!--begin::Example-->
					<div class="content-activities example example-basic mt-5">
						<div class="example-preview">
							<!--begin::Timeline-->

								<?php
								$limit = 15;
								$count = DB::table('ticket_log')
													->where('created_by',Auth::user()->id)
													->orderBy('created_at','desc')
													->count();

								$ticket_log = DB::table('ticket_log')
													->where('created_by',Auth::user()->id)
													->orderBy('created_at','desc')
													->limit($limit)
													->get();
								$color3 = ['primary','danger','warning','info','default'];
								$i = 0;
								?>
								@if($ticket_log->count() == 0)
									<span><i class="text-dark-75">There's currently no activity stream. </i></span>
								@else

										<div class="timeline timeline-6 mt-3">
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
												<div class="font-weight-mormal font-size-lg timeline-content text-muted pl-3"><?=str_replace("Ticket", "Ticket ".ticketNumber($t->ticket_id)." ", $t->message)?></div>
												<!--end::Text-->
											</div>
											<!--end::Item-->
											@endforeach
										</div>
										<!--end::Timeline-->
									@if($count > $limit)
										<a href="{{URL('/')}}/activity_stream" style="display:block;margin:20px auto 0px auto;text-align:center">Show More</a>
									@endif
								@endif

						</div>
					</div>
					<!--end::Example-->

		</div>
		<!--end::Body-->
	</div>

	<div class="card  card-stretch gutter-b">
		<!--begin::Header-->
		<div class="card-header border-0 py-5">
			<div class="d-flex justify-content-between">
				<h3 class="card-title align-items-start flex-column mb-0 mt-2">
					<span class="card-label font-weight-bolder text-dark">List of outstanding Material</span>
					<span class="text-muted mt-3 font-weight-bold font-size-sm" style="display:block">These are your outstanding material</span>
				</h3>
				<div class="col-4">
					<select name="material-filter" class="form-control" id="material-filter">
						<option value="all">All Status</option>
						<option value="due_today">Due Today</option>
						<option value="overdue">Overdue</option>
					</select>
				</div>
			</div>
			<div class="card-toolbar">

			</div>
		</div>
		<!--end::Header-->
		<!--begin::Body-->
		<div class="card-body pt-0 pb-3">
			<div class="tab-content">
				<!--begin::Table-->
				<div class="table-responsive" style="">
					@include('flash::message')

					<table class="table table-striped table-responsive table-bordered" id="main-table">
						<thead>
							<tr>
								<th>No</th>
								<th>Serial Number</th>
								<th>Material Code</th>
								<th>Date Start</th>
								<th>Date End</th>
								<th>Status</th>
							</tr>
						</thead>
					</table>
				</div>
				<!--end::Table-->
			</div>
		</div>
		<!--end::Body-->
	</div>
</div>

<script>
		
		
		$(document).ready(function() {
			function initDatatable() {
				const datatable = $('#main-table').DataTable({
					processing: true,
					serverSide: true,
					bDestroy: true,
					ajax: "{{ route('home.material_list_report') }}?filter_status="+$('#material-filter').val(),
					columns: [
						{
							searchable: false,
							orderable: false,
							targets: 0,
							defaultContent: ''
						},
						{ data: 'material.serial_number', name: 'material.serial_number', defaultContent: '' },
						{ data: 'material.material_code.code', name: 'material.material_code.code', defaultContent: '' },
						{ data: 'date_start', name: 'start_date' },
						{ data: 'date_end', name: 'end_date' },
						{ data: 'status_badge', name: 'end_date' },
					]
				})

				datatable.on( 'draw.dt', function () {
					var PageInfo = $('.dataTable').DataTable().page.info();
					datatable.column(0, { page: 'current' }).nodes().each( function (cell, i) {
						cell.innerHTML = i + 1 + PageInfo.start;
					} );
				} );
			}

			

			$('#material-filter').change(function() {
				initDatatable()
			})

			initDatatable()
		})


	</script>
<style>
@endif
.content-activities .timeline.timeline-6 .timeline-item .timeline-label {
    width: 150px;
    text-align: right;
    padding-right: 15px;
}
.content-activities .timeline.timeline-6:before {
    left: 151px;
}
.content-activities .text-muted {
    color: #57575a !important;
}
</style>

</div>	<!--Close Row-->
</div>
@endif


						<!--end::Entry-->
					</div>
					<!--end::Content-->

@endsection
