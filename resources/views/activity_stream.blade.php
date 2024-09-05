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
								
								$ticket_log = DB::table('ticket_log')
													->where('created_by',Auth::user()->id)
													->orderBy('created_at','desc')
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
@endsection
