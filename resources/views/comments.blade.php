<style>
.text-muted {
    color: #57575a !important;
}
.messages p {
	margin-bottom:0;
}
.file-attach {
	border: 1px solid #e0e0e0;
    border-radius: 5px;
    padding: 3px;
    background: #f1f1f1;
    color: #757579;
    display: block;
}
</style>
<?php 
$comments = DB::table('comment')->orderBy('id','desc')->where('ticket_id',$ticket->id)->get();

?>
@foreach($comments as $c) 
	<?php 
	$contact = DB::table('contact')->where('id',$c->contact_id)->first();
	$job_name = "";
	if(!empty($contact->job_title)) {
		$job_name = DB::table('job_title')->where('id',$contact->job_title)->value('job_name');
	}
	?>
	@if($c->user_id == Auth::user()->id) 

		<div class="card card-custom gutter-b "		>
			<div class="card-body">
				<!--begin::Top-->
				<div class="d-flex">
					<!--begin::Pic-->
					<div class="flex-shrink-0 mr-7">
						<div class="symbol symbol-50 symbol-md-70 symbol-light-success">
							<span class="font-size-h3 symbol-label font-weight-boldest">{{acronym($contact->name ?? "")}}</span>
						</div>
					</div>
					<!--end::Pic-->
					<!--begin: Info-->
					<div class="flex-grow-1">
						<!--begin::Title-->
						<div class="d-flex align-items-center justify-content-between flex-wrap mt-2">
							<!--begin::User-->
							<div class="mr-3">
								@if(!empty($c->mode))
								<h4 class="text-dark">{{$c->mode}}</h4>
								@endif
								<!--begin::Name-->
								<span class="d-flex align-items-center text-dark text-hover-primary font-size-h5 font-weight-bold mr-3">{{$contact->name ?? ""}}
								<i class="flaticon2-correct text-success icon-md ml-2"></i></span>
								<!--end::Name-->
								<!--begin::Contacts-->
								<div class="d-flex flex-wrap my-2">
									<span class="text-muted text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2">
									<span class="flaticon2-chronometer icon-md icon-gray-500 mr-1"></span>
									{{date('d M Y H:i', strtotime($c->created_at))}}</span>
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
							@if(false)
							<!--begin::Actions-->
							<div class="my-lg-0 my-1" hidden>
								<a href="#" class="btn btn-sm btn-white-line3"><span class="far fa-star"></span></a>
								<a href="#" class="btn btn-sm btn-white-line3 ml-1 mr-2"><span class="far fa-trash-alt"></span></a>
							</div>
							<!--end::Actions-->
							@endif
						</div>
						<!--end::Title-->
						<!--begin::Content-->
						<div class="d-flex align-items-center flex-wrap justify-content-between">
							<!--begin::Description-->
							<div class="flex-grow-1 font-weight-bold text-dark-50 py-2 py-lg-2 mr-5">
								<?=$c->message?>
							</div>
							<!--end::Description-->
						</div>
						<!--end::Content-->
					</div>
					<!--end::Info-->
				</div>
				<!--end::Top-->
				
				
				
				@if(!empty($c->files))
					<!--begin::Separator-->
					<div class="separator separator-solid my-7"></div>
					<!--end::Separator-->
					<div class="d-flex flex-column mb-5 align-items-start">Attachments</div>
					<div class="d-flex flex-column mb-5 align-items-start">
						<div>
						
							<?php $files = explode(",",$c->files_url);
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
										<div class="symbol symbol-50 symbol-lg-50 symbol-light-primary ml-1 p-1" style="border:1px solid #d4d4d4">
											<img src="{{$f}}">
										</div>
										</a>
									@else
									@endif
								@endforeach
						</div>
					</div>
				@endif
			</div>
		</div>

		<div class="d-none">
		<!--begin::Message Out-->
		<div class="d-flex flex-column mb-5 align-items-end">
			<div class="d-flex align-items-center">
				<div>
					
					<a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6" style="display:block">You</a>
					<span class="text-muted font-size-sm">{{date('d M Y H:i', strtotime($c->created_at))}}</span>
				</div>
				<div class="symbol symbol-circle symbol-35 ml-3">
					<img alt="Pic" src="{{URL('/')}}/assets/media/users/default.jpg">
				</div>
			</div>
			<div class="mt-2 rounded p-3 bg-light-primary text-dark-50 font-weight-bold font-size-lg text-right max-w-400px"><?=$c->message?>


				
			</div>

			
		</div>
		@if(!empty($c->files))
			<div class="d-flex flex-column mb-5 align-items-end">Attachments</div>
			<div class="d-flex flex-column mb-5 align-items-end">
				<div>
				
					<?php $files = explode(",",$c->files_url);
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
								<div class="symbol symbol-50 symbol-lg-50 symbol-light-primary ml-1 p-1" style="border:1px solid #d4d4d4;float:right">
									<img src="{{$f}}">
								</div>
								</a>
							@else
							@endif
						@endforeach
				</div>
			</div>
		@endif
		<!--end::Message Out-->
		</div>
	@else

		<div class="card card-custom gutter-b ">
			<div class="card-body">
				<!--begin::Top-->
				<div class="d-flex">
					<!--begin::Pic-->
					<div class="flex-shrink-0 mr-7">
						<div class="symbol symbol-50 symbol-md-70 symbol-light-primary">
							<span class="font-size-h3 symbol-label font-weight-boldest">{{acronym($contact->name ?? "")}}</span>
						</div>
					</div>
					<!--end::Pic-->
					<!--begin: Info-->
					<div class="flex-grow-1">
						<!--begin::Title-->
						<div class="d-flex align-items-center justify-content-between flex-wrap mt-2">
							<!--begin::User-->
							<div class="mr-3">
								@if(!empty($c->mode))
								<h4 class="text-dark">{{$c->mode}}</h4>
								@endif
								<!--begin::Name-->
								<a href="#" class="d-flex align-items-center text-dark text-hover-primary font-size-h5 font-weight-bold mr-3">{{$contact->name ?? ""}}
								<i class="flaticon2-correct text-success icon-md ml-2"></i></a>
								<!--end::Name-->
								<!--begin::Contacts-->
								<div class="d-flex flex-wrap my-2">
									<a href="#" class="text-muted text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2">
									<span class="flaticon2-chronometer icon-md icon-gray-500 mr-1"></span>
									{{date('d M Y H:i', strtotime($c->created_at))}}</a>
									<a href="#" class="text-muted text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2">
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
									</span>{{$job_name}}</a>
									<a href="#" class="text-muted text-hover-primary font-weight-bold">
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
									</span>{{$contact->email ?? ""}}</a>
								</div>
								<!--end::Contacts-->
							</div>
							<!--begin::User-->
							@if(false)
							<!--begin::Actions-->
							<div class="my-lg-0 my-1" hidden>
								<a href="#" class="btn btn-sm btn-white-line3"><span class="far fa-star"></span></a>
								<a href="#" class="btn btn-sm btn-white-line3 ml-1 mr-2"><span class="far fa-trash-alt"></span></a>
							</div>
							<!--end::Actions-->
							@endif
						</div>
						<!--end::Title-->
						<!--begin::Content-->
						<div class="d-flex align-items-center flex-wrap justify-content-between">
							<!--begin::Description-->
							<div class="flex-grow-1 font-weight-bold text-dark-50 py-2 py-lg-2 mr-5">
								<?=$c->message?>
							</div>
							<!--end::Description-->
						</div>
						<!--end::Content-->
					</div>
					<!--end::Info-->
				</div>
				<!--end::Top-->

				@if(!empty($c->files))
					<!--begin::Separator-->
					<div class="separator separator-solid my-7"></div>
					<!--end::Separator-->
					<div class="d-flex flex-column mb-5 align-items-start">Attachments</div>
					<div class="d-flex flex-column mb-5 align-items-start">
						<div>
						
							<?php $files = explode(",",$c->files_url);
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
										<div class="symbol symbol-50 symbol-lg-50 symbol-light-primary ml-1 p-1" style="border:1px solid #d4d4d4">
											<img src="{{$f}}">
										</div>
										</a>
									@else
									@endif
								@endforeach
						</div>
					</div>
				@endif
			</div>
		</div>
		
		<div class="d-none">
		<!--begin::Message In-->
		<div class="d-flex flex-column mb-5 align-items-start">
			<div class="d-flex align-items-center">
				<div class="symbol symbol-circle symbol-35 mr-3">
					<img alt="Pic" src="{{URL('/')}}/assets/media/users/default.jpg">
				</div>
				<div>
					<?php 
					$contact = DB::table('contact')->where('id',$c->contact_id)->first();
					?>
					<a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6"  style="display:block">{{$contact->name ?? ""}}</a>
					<span class="text-muted font-size-sm">{{date('d M Y H:i', strtotime($c->created_at))}}</span>
				</div>
			</div>
			<div class="mt-2 rounded p-3 bg-light-success text-dark-50 font-weight-bold font-size-lg text-left max-w-400px"><?=$c->message?>
			</div>
		</div>
		<!--end::Message In-->
		@if(!empty($c->files))
			<div class="d-flex flex-column mb-5 align-items-start">Attachments</div>
			<div class="d-flex flex-column mb-5 align-items-start">
				<div>
				
					<?php $files = explode(",",$c->files_url);
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
								<div class="symbol symbol-50 symbol-lg-50 symbol-light-primary ml-1 p-1" style="border:1px solid #d4d4d4">
									<img src="{{$f}}">
								</div>
								</a>
							@else
							@endif
						@endforeach
				</div>
			</div>
		@endif
		</div>
	@endif
@endforeach
