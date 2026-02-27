@extends('layouts.app')

@section('content')
<style>
.subheader {
	display:none;
}
.content {
    padding: 0px 0 40px 0 !important;
}
</style>
					<!--begin::Content-->
					<div  style="padding-top:0px !important" class="content pt-0" id="kt_content">
						<!--begin::Entry-->
						<!--begin::Hero-->
						<?php //home image tidak ditampilkan ?>
						@if(false)
						<div class="d-flex flex-row-fluid bgi-size-cover bgi-position-center" style="background-image: url('assets/media/bg/bg-9.jpg')">
							<div class="container">
								<!--begin::Topbar-->
								<div class="d-flex justify-content-between align-items-center border-bottom border-white py-7">
									<h3 class="h4 text-dark mb-0">NSMS Portal</h3>
									<div class="d-flexaaa d-none">
										<a href="#" class="font-size-h6 font-weight-bold">Community</a>
										<a href="#" class="font-size-h6 font-weight-bold ml-8">Visit Blog</a>
									</div>
								</div>
								<!--end::Topbar-->
								<div class="d-flex align-items-stretch text-center flex-column py-40">
									<!--begin::Heading-->
									<h1 class="text-dark font-weight-bolder mb-12">How can we help?</h1>
									<!--end::Heading-->
									<!--begin::Form-->
									<form class="d-flex position-relative w-75 px-lg-40 m-auto">
										<div class="input-group">
											<!--begin::Icon-->
											<div class="input-group-prepend">
												<span class="input-group-text bg-white border-0 py-7 px-8">
													<span class="svg-icon svg-icon-xl">
														<!--begin::Svg Icon | path:assets/media/svg/icons/General/Search.svg-->
														<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
															<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																<rect x="0" y="0" width="24" height="24" />
																<path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
																<path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero" />
															</g>
														</svg>
														<!--end::Svg Icon-->
													</span>
												</span>
											</div>
											<!--end::Icon-->
											<!--begin::Input-->
											<input type="text" class="form-control h-auto border-0 py-7 px-1 font-size-h6" placeholder="Ask a question" />
											<!--end::Input-->
										</div>
									</form>
									<!--end::Form-->
								</div>
							</div>
						</div>
						<!--end::Hero-->
						@endif
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
										<div class="card-body" onclick="window.location = '{{URL('/').'/request-service/service-catalog/2'}}';">
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
													<a href="#" class="text-dark text-hover-primary font-weight-bold font-size-h4 mb-3">Request Service</a>
													<div class="text-dark-75">Raise a Request for a new device or service</div>
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
										<div class="card-body" onclick="window.location = '{{URL('/').'/request-incident/incident-catalog/2'}}';">
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
													<a href="#" class="text-dark text-hover-primary font-weight-bold font-size-h4 mb-3">Request Incident</a>
													<div class="text-dark-75">Having Trouble ? Contact The Support Team</div>
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
										<div class="card-body">
											<div class="d-flex align-items-center p-5">
												<!--begin::Icon-->
												<div class="mr-6">
													<span class="svg-icon svg-icon-success svg-icon-5x">
														<!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\Shopping\Chart-pie.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
															<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																<rect x="0" y="0" width="24" height="24"/>
																<path d="M4.00246329,12.2004927 L13,14 L13,4.06189375 C16.9463116,4.55399184 20,7.92038235 20,12 C20,16.418278 16.418278,20 12,20 C7.64874861,20 4.10886412,16.5261253 4.00246329,12.2004927 Z" fill="#000000" opacity="0.3"/>
																<path d="M3.0603968,10.0120794 C3.54712466,6.05992157 6.91622084,3 11,3 L11,11.6 L3.0603968,10.0120794 Z" fill="#000000"/>
															</g>
														</svg><!--end::Svg Icon-->
													</span>
												</div>
												<!--end::Icon-->
												<!--begin::Content-->
												<div class="d-flex flex-column">
													<a href="#" class="text-dark text-hover-primary font-weight-bold font-size-h4 mb-3">Report</a>
													<div class="text-dark-75">Visualize Data Report in <br/>Graphical</div>
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
									
									<div  style="cursor: pointer;background-color: rgb(139 195 74 / 0.2) !important;" class="card card-custom wave wave-animate-slow wave-primary mb-8 mb-lg-0" style="">
										<div class="card-body" onclick="window.location = '{{URL('/').'/approve-request'}}';">
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
												<div class="d-flex flex-column">
													<a href="#" class="text-dark text-hover-primary font-weight-bold font-size-h4 mb-3">Approve Request</a>
													<div class="text-dark-75">View all requests awaiting my approval</div>
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
													<a href="#" class="text-dark text-hover-primary font-weight-bold font-size-h4 mb-3" >Tracking Your Ticket</a>
													<div class="text-dark-75">Monitoring Your Submit <br/>Ticket</div>
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
													<a href="#" class="text-dark text-hover-primary font-weight-bold font-size-h4 mb-3">FAQ &amp; Tutorials</a>
													<div class="text-dark-75">Lookup FAQ to fix issues on your own</div>
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
	$( ".container" ).each(function( index ) {
	  console.log( index + ": " + $( this ).text() );
	  $(this).addClass("container-fluid pl-11 pr-11");
	  $(this).addClass("container-padding");
	  $(this).removeClass("container");
	});
$( document ).ready(function() {
	$( ".container" ).each(function( index ) {
	  console.log( index + ": " + $( this ).text() );
	  $(this).addClass("container-fluid pl-11 pr-11");
	  $(this).addClass("container-padding");
	  $(this).removeClass("container");
	});
});
</script>
<div class="container-fluid">
<script>
//makechart('container-chart2',total_ticket,data_json1);
//makechart('container-chart3',total_ticket,data_json1);
// Create the chart
function makechart(id_element,total_ticket,data_json) {
	Highcharts.chart(id_element, {
	  chart: {
		type: 'pie',
		height:'80%',
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
		}
	  },

	  tooltip: {
		headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
		pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
	  },

	  series: [{
	//name: 'Browser share',
			innerSize: '50%',
		name: "Browsers",
		colorByPoint: true,
		data: data_json
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
<div class="row">
	<div class="dash-box col-md-2 pl-5 pr-1">
		<div class="card ml-2">
		  <div class="card-body">
		  <h3 class="dash-title mb-5">Overdue</h3>
		  <?php 
			$count = DB::table('ticket')
						->whereIn('ticket.status',['Submit for Approval','Waiting for User'])
						->whereRaw('created_at < DATE_SUB(CURDATE(), INTERVAL 7 DAY)')
						->count();
		  ?>
			<span class="dash-number">{{$count}}</span>
		  </div>
		</div>
	</div>
	<div class="dash-box col-md-2 pl-1 pr-1">
		<div class="card ">
		  <div class="card-body">
		  <h3 class="dash-title mb-5">Due Today</h3>
		  <?php 
			$count = DB::table('ticket')
						->whereIn('ticket.status',['Submit for Approval','Waiting for User'])
						->whereRaw('created_at BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND DATE_SUB(CURDATE(), INTERVAL 7 DAY)')
						->count();
		  ?>
			<span class="dash-number">{{$count}}</span>
		  </div>
		</div>
	</div>
	<div class="dash-box col-md-2 pl-1 pr-1">
		<div class="card ">
		  <div class="card-body">
		  <h3 class="dash-title mb-5">Open Tickets</h3>
		  <?php 
			$count = DB::table('ticket')
						->whereIn('ticket.status',['Open'])
						->count();
		  ?>
			<span class="dash-number">{{$count}}</span>
		  </div>
		</div>
	</div>
	<div class="dash-box col-md-2 pl-1 pr-1">
		<div class="card ">
		  <div class="card-body">
		  <h3 class="dash-title mb-5">Tickets On Hold</h3>
		  <?php 
			$count = DB::table('ticket')
						->whereIn('ticket.status',['Waiting for User'])
						->count();
		  ?>
			<span class="dash-number">{{$count}}</span>
		  </div>
		</div>
	</div>

	<div class="dash-box col-md-2 pl-1 pr-1">
		<div class="card ">
		  <div class="card-body">
		  <h3 class="dash-title mb-5">Unassign Tickets</h3>
		  <?php 
			$count = DB::table('ticket')
						->where('agent_id',0)
						->count();
		  ?>
			<span class="dash-number">{{$count}}</span>
		  </div>
		</div>
	</div>
	<div class="dash-box col-md-2 pl-1 pr-1">
		<div class="card ">
		  <div class="card-body">
		  <h3 class="dash-title mb-5">All Tickets</h3>
		  <?php 
			$count = DB::table('ticket')
						->count();
		  ?>
			<span class="dash-number">{{$count}}</span>
		  </div>
		</div>
	</div>


</div>
<div class="row">

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
					<div class="dash-box col-md-4 pl-7 pr-1 pt-2">
						<!--begin::Card-->
						<div class="card card-custom gutter-b">
							<div class="card-body" >
									<h3 class="dash-title mb-5">Incident - Unresolved Ticket</h3>
									<!--start::Chart-->
									<figure class="highcharts-figure">
										<div id="container-chart"></div>
									</figure>
									<!--end::Chart-->
							</div>
						</div>
						<!--end::Card-->
					</div>
					<div class="dash-box col-md-4 pl-3 pr-1 pt-2">
						<!--begin::Card-->
						<div class="card card-custom gutter-b">
							<div class="card-body" >
									<h3 class="dash-title mb-5">Service - Unresolved Ticket</h3>
									<!--start::Chart-->
									<figure class="highcharts-figure">
										<div id="container-chart2"></div>
									</figure>
									<!--end::Chart-->
							</div>
						</div>
						<!--end::Card-->
					</div>
					<div class="dash-box col-md-4 pl-3 pr-1 pt-2">
						<!--begin::Card-->
						<div class="card card-custom gutter-b">
							<div class="card-body" >
									<h3 class="dash-title mb-5">Problem - Unresolved Ticket</h3>
									<!--start::Chart-->
									<figure class="highcharts-figure">
										<div id="container-chart3"></div>
									</figure>
									<!--end::Chart-->
							</div>
						</div>
						<!--end::Card-->
					</div>


						
					<?php 
					//INCIDENT
					$list_priority = DB::table('sla_priority')->get();
					$list_status = ['new','Submit for Approval','Rejected','Waiting for User','Open','On Progress'
										//,'Resolve','Close','Draft'
									];
					$arr_priority =[];
					$total = DB::table('ticket')->whereIn('ticket.status',$list_status)//['Submit for Approval','Rejected','Waiting for User','Open',])
												->where('finalclass','incident_management')->count();

					foreach($list_status as $p) {
					//foreach($list_priority as $p) {
						$count = DB::table('ticket')
													//->whereIn('ticket.status',['Submit for Approval','Rejected','Waiting for User','Open',])
													//->where('priority',$p->priority)
													->where('status',$p)
													->where('finalclass','incident_management')
													->count();
						if($count) {
							//$arr_priority[] = ['name'=>$p->priority,'y'=>$count
													////,'drilldown'=>$p->priority
												//];
							$arr_priority[] = ['name'=>$p,'y'=>$count
													//,'drilldown'=>$p->priority
												];
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
					$list_status = ['new','Submit for Approval','Rejected','Waiting for User','Open','On Progress'
										//,'Resolve','Close','Draft'
									];
					$arr_priority =[];
					$total = DB::table('ticket')->whereIn('ticket.status',$list_status)//['Submit for Approval','Rejected','Waiting for User','Open',])
												->where('finalclass','service_request')->count();
					
					foreach($list_status as $p) {
					//foreach($list_priority as $p) {
						$count = DB::table('ticket')
													//->whereIn('ticket.status',['Submit for Approval','Rejected','Waiting for User','Open',])
													//->where('priority',$p->priority)
													->where('status',$p)
													->where('finalclass','service_request')
													->count();
						if($count) {
							//$arr_priority[] = ['name'=>$p->priority,'y'=>$count
													////,'drilldown'=>$p->priority
												//];
							$arr_priority[] = ['name'=>$p,'y'=>$count
													//,'drilldown'=>$p->priority
												];
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

					<?php 
					//PROBLEM
					$list_priority = DB::table('sla_priority')->get();
					$list_status = ['new','Submit for Approval','Rejected','Waiting for User','Open','On Progress'
										//,'Resolve','Close','Draft'
									];
					$arr_priority =[];
					$total = DB::table('ticket')->whereIn('ticket.status',$list_status)//['Submit for Approval','Rejected','Waiting for User','Open',])
												->where('finalclass','problem')->count();
					
					foreach($list_status as $p) {
					//foreach($list_priority as $p) {
						$count = DB::table('ticket')
													//->whereIn('ticket.status',['Submit for Approval','Rejected','Waiting for User','Open',])
													//->where('priority',$p->priority)
													->where('status',$p)
													->where('finalclass','problem')
													->count();
						if($count) {
							//$arr_priority[] = ['name'=>$p->priority,'y'=>$count
													////,'drilldown'=>$p->priority
												//];
							$arr_priority[] = ['name'=>$p,'y'=>$count
													//,'drilldown'=>$p->priority
												];
						}
					}
					//var_dump($arr_priority);
					//echo json_encode($arr_priority);
					?>
					<script>
					total_ticket = {{$total}};
					data_json1 = <?=json_encode($arr_priority)?>;
					makechart('container-chart3',total_ticket,data_json1);
					</script>


</div>

</div><!-- close container-fluid -->
						
						<!-- END : Statistic -->
						
						
						
						@if(false)
						<!--begin::Section-->
						<div class="container mb-8">
							<div class="card">
								<div class="card-body">
									<div class="p-6">
										<h2 class="text-dark mb-8">Information About NSMS Portal</h2>
										<div class="row">
											<div class="col-lg-3">
												<!--begin::Navigation-->
												<ul class="navi navi-link-rounded navi-accent navi-hover flex-column mb-8 mb-lg-0" role="tablist">
													<!--begin::Nav Item-->
													<li class="navi-item mb-2">
														<a class="navi-link" data-toggle="tab" href="#">
															<span class="navi-text text-dark-50 font-size-h5 font-weight-bold">Introduction</span>
														</a>
													</li>
													<!--end::Nav Item-->
													<!--begin::Nav Item-->
													<li class="navi-item mb-2">
														<a class="navi-link active" data-toggle="tab" href="#">
															<span class="navi-text text-dark font-size-h5 font-weight-bold">Request Service</span>
														</a>
													</li>
													<!--end::Nav Item-->
													<!--begin::Nav Item-->
													<li class="navi-item mb-2">
														<a class="navi-link" data-toggle="tab" href="#">
															<span class="navi-text text-dark-50 font-size-h5 font-weight-bold">Request Incident</span>
														</a>
													</li>
													<!--end::Nav Item-->
													<!--begin::Nav Item-->
													<li class="navi-item mb-2">
														<a class="navi-link" data-toggle="tab" href="#">
															<span class="navi-text text-dark-50 font-size-h5 font-weight-bold">NSMS Workflow</span>
														</a>
													</li>
													<!--end::Nav Item-->
													<!--begin::Nav Item-->
													<li class="navi-item mb-2">
														<a class="navi-link" data-toggle="tab" href="#">
															<span class="navi-text text-dark-50 font-size-h5 font-weight-bold">More...</span>
														</a>
													</li>
													<!--end::Nav Item-->
												</ul>
												<!--end::Navigation-->
											</div>
											<div class="col-lg-7">
												<!--begin::Accordion-->
												<div class="accordion accordion-light accordion-light-borderless accordion-svg-toggle" id="accordionExample7">
													<!--begin::Item-->
													<div class="card">
														<!--begin::Header-->
														<div class="card-header" id="headingOne7">
															<div class="card-title" data-toggle="collapse" data-target="#collapseOne7" aria-expanded="true" role="button">
																<span class="svg-icon svg-icon-primary">
																	<!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Angle-double-right.svg-->
																	<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																		<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																			<polygon points="0 0 24 0 24 24 0 24" />
																			<path d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z" fill="#000000" fill-rule="nonzero" />
																			<path d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999)" />
																		</g>
																	</svg>
																	<!--end::Svg Icon-->
																</span>
																<div class="card-label text-dark pl-4">Product Inventory</div>
															</div>
														</div>
														<!--end::Header-->
														<!--begin::Body-->
														<div id="collapseOne7" class="collapse show" aria-labelledby="headingOne7" data-parent="#accordionExample7">
															<div class="card-body text-dark-50 font-size-lg pl-12">Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo.</div>
														</div>
														<!--end::Body-->
													</div>
													<!--end::Item-->
													<!--begin::Item-->
													<div class="card">
														<!--begin::Header-->
														<div class="card-header" id="headingTwo7">
															<div class="card-title collapsed" data-toggle="collapse" data-target="#collapseTwo7" aria-expanded="true" role="button">
																<span class="svg-icon svg-icon-primary">
																	<!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Angle-double-right.svg-->
																	<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																		<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																			<polygon points="0 0 24 0 24 24 0 24" />
																			<path d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z" fill="#000000" fill-rule="nonzero" />
																			<path d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999)" />
																		</g>
																	</svg>
																	<!--end::Svg Icon-->
																</span>
																<div class="card-label text-dark pl-4">Order Statistics</div>
															</div>
														</div>
														<!--end::Header-->
														<!--begin::Body-->
														<div id="collapseTwo7" class="collapse" aria-labelledby="headingTwo7" data-parent="#accordionExample7">
															<div class="card-body text-dark-50 font-size-lg pl-12">Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident.</div>
														</div>
														<!--end::Body-->
													</div>
													<!--end::Item-->
													<!--begin::Item-->
													<div class="card">
														<!--begin::Header-->
														<div class="card-header" id="headingThree7">
															<div class="card-title collapsed" data-toggle="collapse" data-target="#collapseThree7" aria-expanded="true" role="button">
																<span class="svg-icon svg-icon-primary">
																	<!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Angle-double-right.svg-->
																	<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																		<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																			<polygon points="0 0 24 0 24 24 0 24" />
																			<path d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z" fill="#000000" fill-rule="nonzero" />
																			<path d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999)" />
																		</g>
																	</svg>
																	<!--end::Svg Icon-->
																</span>
																<div class="card-label text-dark pl-4">eCommerce Reports</div>
															</div>
														</div>
														<!--end::Header-->
														<!--begin::Body-->
														<div id="collapseThree7" class="collapse" aria-labelledby="headingThree7" data-parent="#accordionExample7">
															<div class="card-body text-dark-50 font-size-lg pl-12">Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident.</div>
														</div>
														<!--end::Body-->
													</div>
													<!--end::Item-->
												</div>
												<!--end::Accordion-->
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!--end::Section-->
						<!--begin::Section-->
						<div class="container mb-8 ">
							<div class="card card-custom p-6">
								<div class="card-body">
									<!--begin::Heading-->
									<h2 class="text-dark mb-8">News Feed Updates </h2>
									<!--end::Heading-->
									<!--begin::Content-->
									<h4 class="font-weight-bold text-dark mb-4">NSMS  Coming Soon Features</h4>
									<div class="text-dark-50 line-height-lg mb-8">
										<p>Windows 10 automatically downloads and installs updates to make sure your device is secure and up to date. This means you receive the latest fixes and security updates, helping your device run efficiently and stay protected. In most cases, restarting your device completes the update. Make sure your device is plugged in when you know updates will be installed.</p>
										<a class="font-weight-bold" href="#">Read More</a>
									</div>
									<!--end::Content-->
									<!--begin::Content-->
									<h4 class="font-weight-bold text-dark mb-4">Newest Big Event Nabati</h4>
									<div class="text-dark-50 line-height-lg">
										<p>Windows 10 automatically downloads and installs updates to make sure your device is secure and up to date. This means you receive the latest fixes and security updates, helping your device run efficiently and stay protected. In most cases, restarting your device completes the update. Make sure your device is plugged in when you know updates will be installed.</p>
										<a class="font-weight-bold" href="#">Read More</a>
									</div>
									<!--end::Content-->
								</div>
							</div>
						</div>
						<!--end::Section-->
						<!--begin::Section-->
						<div class="container ">
							<div class="row">
								<div class="col-lg-6">
									<!--begin::Callout-->
									<div class="card card-custom p-6 mb-8 mb-lg-0">
										<div class="card-body">
											<div class="row">
												<!--begin::Content-->
												<div class="col-sm-7">
													<h2 class="text-dark mb-4">Get in Touch</h2>
													<p class="text-dark-50 line-height-lg">You can contact us via Email messaging. We will reply in active hours.</p>
												</div>
												<!--end::Content-->
												<!--begin::Button-->
												<div class="col-sm-5 d-flex align-items-center justify-content-sm-end">
													<a href="custom/apps/support-center/feedback.html" class="btn font-weight-bolder text-uppercase font-size-lg btn-primary py-3 px-6">Contact Us</a>
												</div>
												<!--end::Button-->
											</div>
										</div>
									</div>
									<!--end::Callout-->
								</div>
								<div class="col-lg-6">
									<!--begin::Callout-->
									<div class="card card-custom p-6">
										<div class="card-body">
											<div class="row">
												<!--begin::Content-->
												<div class="col-sm-7">
													<h2 class="text-dark mb-4">Live Chat</h2>
													<p class="text-dark-50 line-height-lg">Need help from Support Agent via Live Chat, you can Start Chat Now</p>
												</div>
												<!--end::Content-->
												<!--begin::Button-->
												<div class="col-sm-5 d-flex align-items-center justify-content-sm-end">
													<a href="#" data-toggle="modal" data-target="#kt_chat_modal" class="btn font-weight-bolder text-uppercase font-size-lg btn-success py-3 px-6">Start Chat</a>
												</div>
												<!--end::Button-->
											</div>
										</div>
									</div>
									<!--end::Callout-->
								</div>
							</div>
						</div>
						<!--end::Section-->
						@endif
						
						<!--end::Entry-->
					</div>
					<!--end::Content-->

@endsection
