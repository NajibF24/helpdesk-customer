@extends('layouts.app')

@section('content')
<div class="container-fluid" style="    margin-top: -30px;">
<div class="row home-content-table">
<div class="col-lg-12 pl-6 pt-0 pr-0">
	<!--begin::Advance Table Widget 4-->
	<div class="card  card-stretch gutter-b">
		<!--begin::Header-->
		<div class="card-header border-0 py-5">
			<h3 class="card-title align-items-start flex-column mb-0 mt-2">
				<span class="card-label font-weight-bolder text-dark">Notification</span>

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

								$notif_list = DB::table('notification_message')
											->where('type','!=','ticket_comment')
											->where('user_id',Auth::user()->id)
											->orderBy('created_at','desc')
											->get();

								$count_unread = DB::table('notification_message')->where('type','!=','ticket_comment')->where('user_id',Auth::user()->id)->whereNull('read_at')->count();

								// $notif_list = DB::table('notification_message')
								// 					->where('user_id',Auth::user()->id)
								// 					->orderBy('created_at','desc')
								// 					->get();
								$color3 = ['primary','danger','warning','info','default'];
								$i = 0;
								?>
								
								@if($notif_list->count() == 0)
									<span><i class="text-dark-75">There's currently no activity stream. </i></span>
								@else
									@if ($count_unread > 0)
									<div class="row">
										<div class="col-md-12">
											<div style="margin-left:20px;" class="float-left"> 
												<input type="checkbox" name="all" id="all" /> <label for='all' style="margin-left: 10px;">Select all mark as read</label>
											</div>	
											<a href="javascript:;" id="btn-read" class="btn btn btn-sm btn-outline-dark btn-white-line btn-sm  mr-1 " style="margin-left:10px; display:none;">
												Set notifications as read
											</a>
										</div>
									</div>
									@endif
									
									<div class="timeline timeline-6 mt-3">
										@foreach($notif_list as $t)
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
											<div class="font-weight-mormal font-size-lg timeline-content text-muted pl-3">
												{{-- <input type="checkbox" name="selected[]" id="notif_{{ $t->id }}" value="{{ $t->id }}" />
												<label><?= //$t->message ?></label> --}}
												<div class="form-check">
													@if($t->read_at==null)
													<input class="form-check-input" type="checkbox" name="selected[]" id="notif_{{ $t->id }}" value="{{ $t->id }}">
													@endif
													<?php
													if($t->read_at==null) {
														$set = "color: #000 !important;";
													} else {
														$set = "color: #606065 !important";
													}
													?>
													<label class="form-check-label" for="notif_{{ $t->id }}" style="margin-left: 10px;">
														<a href="{{URL('/')}}/view_notif/{{$t->token}}" class="{{ ($t->read_at==null)?'font-weight-bolder':'' }}" style="{{$set}}"><?= $t->message ?></a>
													</label>
												</div>
											</div>
											<!--end::Text-->
										</div>
										<!--end::Item-->
										@endforeach
									</div>
									<!--end::Timeline-->

								@endif

						</div>
					</div>
					<!--end::Example-->

		</div>
		<!--end::Body-->
	</div>
</div>
<style>
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

<script>
	$(document).ready(function(){
		$('input[name="all"]').bind('click', function(){
			var status = $(this).is(':checked');
			show_btn_save(status)
			$('.form-check-input').not(this).prop('checked', this.checked);
		});

		$('input[name="selected[]"]').bind('click', function(){
			var $checkboxes = $('input[name="selected[]"]');
			var countCheckedCheckboxes = $checkboxes.filter(':checked').length;
			if(countCheckedCheckboxes > 0 ){
				show_btn_save(true)
			} else {
				show_btn_save(false)
			}
		});

		$("#btn-read").click(function(){
			var read = []
			var checkboxes = document.querySelectorAll('input[name="selected[]"]:checked')
			for (var i = 0; i < checkboxes.length; i++) {
				read.push(checkboxes[i].value)
			}
			
			$.ajax({
				type: "POST",
				url: "{{ URL('/').'/set-read-notification' }}",
				data: {'read':read,  "_token": "{{ csrf_token() }}"},
				dataType: 'json',
				success: function(data){
					console.log(data);
					if(data.status){
						Swal.fire("Confirmation",data.message,"success")
                            setTimeout(function(){ 
                                window.location = "{{ URL('/').'/notification' }}";
                            }, 2000);
					} else {
						Swal.fire("Failed",data.message,"error")
					}
				},
				error: function(){
					console.log("error");
				}
			});
		});
	});

	function show_btn_save(status){
		if(status){
			document.getElementById("btn-read").style.display = "inline";
		} else {
			document.getElementById("btn-read").style.display = "none";
		}
	}
</script>
@endsection
