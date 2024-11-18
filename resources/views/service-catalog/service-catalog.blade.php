@extends('layouts.app')

@section('content')
<style>
.nav .nav-link .nav-text {
    color: #50525f;
}
#kt_wrapper {
	background: #f0f7fd;
}
</style>

<script>
jQuery.fn.rotate = function(degrees) {
    $(this).css({'-webkit-transform' : 'rotate('+ degrees +'deg)',
                 '-moz-transform' : 'rotate('+ degrees +'deg)',
                 '-ms-transform' : 'rotate('+ degrees +'deg)',
                 'transform' : 'rotate('+ degrees +'deg)'});
    return $(this);
};
$(document).ready(function() {
	if (localStorage.getItem('is_back_service') == '1') {
		var target = localStorage.getItem('target');

		$('a[href="#'+target+'"]').trigger('click');
	}

	$('.service-title').html($('.click-category').data('title-click'));

	$('.click-category').on('click', function() {
		var href = $(this).attr('href');
		$('.tab-pane').hide();
		$(href).show();
		$('.service-title').html($(this).data('title-click'));
	})

	if ($('.no-category').data('count-category') == '0') {
		$('.card-category-left').hide();
	}

	@if(Session::has('warning'))
		Swal.fire({title: "Resolved Ticket Need Closed",text: "It's look like you have not close your Resolved Ticket. Please close all your resolved ticket, so you can create a new ticket ",icon: "warning",showCancelButton: true,confirmButtonText: "Check Resolved Ticket",  cancelButtonText: "Close",
		}).then(function(result) {
			if (result.value) {
				window.location = "{{URL('/')}}/list/ticket?status_ticket=Resolved";
			}
		});
	@endif

	var screen_height = screen.height;
	var kt_header_height = $("#kt_header").height();
	var kt_subheader_height = $("#kt_subheader").height();

	$("#sticky-nav").css("height", screen_height-kt_header_height-kt_subheader_height-250);
	//$(".sticky-main").css("height", screen_height-kt_header_height-kt_subheader_height-230);

	localStorage.setItem('is_back_service', '1');

	$(document).on('click', '.nav-link', function(){
		$(".nav-link").removeClass("active");
		$(this).addClass("active");
	});

	var card10 = new KTCard('kt_card_10');
	var rotation = 0;
	$('.toggle-category').click(function() {

		if($('.col-left .card-body-top').is(":visible")) {
			rotation -= 90;
			$('.toggle-category').rotate(rotation);
		} else {
			rotation += 90;
			$('.toggle-category').rotate(rotation);
		}
		$('.col-left .card-body-top').slideToggle('slow');
		//card10.toggle();

	})
	if (window.matchMedia('(max-width: 767px)').matches) {
		$('.category-item-left').on('click', function() {
			if($('.col-left .card-body-top').is(":visible")) {
				rotation -= 90;
				$('.toggle-category').rotate(rotation);
			} else {
				rotation += 90;
				$('.toggle-category').rotate(rotation);
			}
			$('.col-left .card-body-top').slideUp('slow');
			//card10.collapse();
		})
	} else {

	}
});

</script>
<style>
.col-left .card-body-top {

}
.card-header {
    border-bottom: 0px solid #fff;
}
.card-header {
    padding: 2rem 2.25rem 1rem 2.25rem;
}

@media (min-width: 768px){
	.col-left .card-body-top {
		min-height: 500px;
	}
	.toggle-category {
		display:none;
	}
}
</style>
<div class="container-fluid">

    <div class="row">
        @if (count($service_category) > 0)
        <div class="col-left col-md-4 card-category-left mb-4" >
			<div class="card  pt-9 pr-1 pb-3 card-left" id="kt_card_10">
				<div class="card-header pt-1">
					<div class="card-title mb-0" style="float: left;display: block;">
						<h3 class="card-label">{{ucfirst($type)}} Category</h3>
					</div>
					<div class="card-toolbar pull-right" style="width: 20%;float: right;text-align: right;">
						<a href="javascript:;" class="mr-1 toggle-category" >
							<i class=" ki ki-arrow-down icon-nm"></i>
						</a>
					</div>
				</div>

				
				<div class="card-body card-body-top" id="sticky-nav"  style="padding-top:0; overflow:auto;">
					<div class="form-item mb-5">
						<label for="select-division">Division</label>
						<select class="form-control select2" id="select-division" name="division">
							<option selected disabled>Select Division</option>
							@foreach($divisions->sortByDesc('id') as $division)
								<option 
									value="{{$division->id}}" 
									{{ request()->has('division') && request()->query('division') == $division->id ? "selected" : "" }}
								>{{$division->name}}</option>
							@endforeach
						</select>
					</div> 
<!--
					<h3 class=" font-weight-bold text-dark mt-0">{{ucfirst($type)}} Category</h3>
-->
					<ul class="nav flex-column nav-pills">
						<?php
							$contact = DB::table('contact')->where('id', Auth::user()->person)->first();
							//var_dump($contact);
							$departments = DB::table('organization')
								->whereIn('type', ['Department','DEPARTEMENT'])
								->when(request()->query('division'), function($q) {
									$q->where('parent', request()->query('division'));
								})
								->get();

							$n=0;
							$i=0;
						?>
						@foreach ($departments as $department)

							<?php
							//echo ucfirst($type).($type == "service"?" Request":"");
								$service_category2 = DB::table('service_category')->where('type', $type)->where('department', $department->id)->get();
								$count_active_service_in_department = DB::table('lnkservicetoservice_category')
									->select('request_management_location.*')
									->join('service', 'service.id', '=', 'lnkservicetoservice_category.service_id')
									->join('request_management', 'request_management.request_name', '=', DB::raw('CAST(service.id AS VARCHAR)'))
									->join('request_management_location', 'request_management_location.request_management', '=', 'request_management.id')
									->where('service.department', $department->id)
									->where('service.request_type',ucfirst($type).($type == "service"?" Request":""))
									->whereRaw('(request_management_location.type =\'All Location\' OR
												(request_management_location.type =\'Company\' AND request_management_location.company=?) OR
												(request_management_location.type =\'Location Company\' AND request_management_location.company=?
													AND request_management_location.location=?))
									',[($contact->company ?? -5),($contact->company ?? -5),($contact->location ?? -5),])
									->count();
								//var_dump($count_active_service_in_department

							?>

							@if($count_active_service_in_department)
								<li class="nav-item mb-2">
									<div class="accordion accordion-light {{ ($service_category2) ? 'accordion-toggle-arrow' : '' }}" id="accordionExample-{{ $i }}" style="border-style:none;">
										<div class="card">
											<div class="card-header" id="headingOne5">
												@if ($service_category2)
												<?php $target = $service_category2 ? '#targetdep-parent-'.$department->id.'' : '#targetdep-'.$department->id.''; ?>
												<div class="card-title {{ ($n==0)?'':'collapsed' }} " data-toggle="collapse" data-target="{{ $target }}" aria-expanded="{{ ($n==0)?'true':'false' }}">
												{{ $department->name }}</div>
												@else
												<a class="card-title {{ ($i==0)?'':'collapsed' }} " data-toggle="tab" href="#targetdep-{{ $department->id }}" aria-expanded="{{ ($i==0)?'true':'false' }}">
													{{ $department->name }}
												</a>
												@endif
											</div>
											<div id="targetdep-parent-{{ $department->id }}" class="collapse {{ ($n==0)?'show':'' }}" data-parent="#accordionExample-{{ $i }}">
												<div class="card-body">
													@if ($service_category2)
													<ul class="nav flex-column nav-pills">
														@foreach ($service_category2 as $category)
															<?php //count service yang aktif
																$count_cat = DB::table('lnkservicetoservice_category')
																	->select('request_management_location.*')
																	->join('service', 'service.id', '=', 'lnkservicetoservice_category.service_id')
																	->join('request_management', 'request_management.request_name', '=', DB::raw('CAST(service.id AS VARCHAR)'))
																	->join('request_management_location', 'request_management_location.request_management', '=', 'request_management.id')
																	->where('lnkservicetoservice_category.service_category_id', $category->id)
																	->whereRaw('lnkservicetoservice_category.service_category_id=? AND (request_management_location.type =\'All Location\' OR
																				(request_management_location.type =\'Company\' AND request_management_location.company=?) OR
																				(request_management_location.type =\'Location Company\' AND request_management_location.company=?
																					AND request_management_location.location=?))
																	',[($category->id ?? -5),($contact->company ?? -5),($contact->company ?? -5),($contact->location ?? -5),])
																	->count();
															?>
															@if($count_cat)
																<li class="nav-item mb-2">
																	<a class="category-item-left nav-link link-{{$i}}" data-toggle="tab" href="#target-{{ $category->id }}">
																		{{ $category->name }}
																	</a>
																</li>
															@endif
															<?php $n++;?>
														@endforeach
													</ul>
													@endif
												</div>
											</div>
										</div>
									</div>
								</li>
							@endif
							@php $i++; @endphp
						@endforeach



					</ul>









				</div>
			</div>
        </div>
        @if (true || $count_cat)
		<div class="col-right col-md-8">
			<div class="card pt-6 pl-8">
				<div class="sticky-main">
					<h2 class="d-flex align-items-center text-dark font-weight-bold my-1 mr-3 service-title"></h2> <br><br>

					<div class="tab-content" id="myTabContent5">
						<?php $i=0;?>
						@foreach ($service_category as $category)

						<?php
							$services = DB::table('lnkservicetoservice_category')
								// ->select('service.*')
								->select('service.*', 'request_management.request_name', 'request_management.inventory_url')
								->leftJoin('service', 'service.id', '=', DB::raw('lnkservicetoservice_category.service_id'))
								->leftJoin('request_management', 'request_management.request_name', '=', DB::raw('CAST(service.id AS VARCHAR)'))
								->where('service_category_id', $category->id)
								->where('service.request_type', $request_type)
								// ->groupBy('service.id')
								->get();
						?>

						<div class="tab-pane fade show {{$i==0?'active':''}}" id="target-{{$category->id}}" role="tabpanel">

							@if (count($services) > 0)

							<div class="row pr-8 pb-8">
								@foreach ($services as $service)

								<?php //count service yang aktif
								$count_service = DB::table('lnkservicetoservice_category')
                                ->select('request_management_location.*')
                                ->join('service', 'service.id', '=', 'lnkservicetoservice_category.service_id')
                                ->join('request_management', 'request_management.request_name', '=', DB::raw('CAST(service.id AS VARCHAR)'))
                                ->join('request_management_location', 'request_management_location.request_management', '=', 'request_management.id')
                                ->whereRaw('CAST(service.id AS VARCHAR) = ? AND (request_management_location.type = \'All Location\' OR
                                            (request_management_location.type = \'Company\' AND request_management_location.company = ?) OR
                                            (request_management_location.type = \'Location Company\' AND request_management_location.company = ?
                                                AND request_management_location.location = ?))',
                                            [($service->id ?? '-5'), ($contact->company ?? '-5'), ($contact->company ?? '-5'), ($contact->location ?? '-5')])
                                ->count();

								//var_dump($count_cat);
								?>

								@if($count_service)
								<!--begin::Product-->
								<div class="col-md-4 col-xxl-4 col-lg-4 show-services d-none">
									<!--begin::Card-->
									<div class="card card-custom card-shadowless">
										@php
											$serviceUrl = URL('/')."/create/$type?category=$category->id&request=$service->id&target=target-".str_replace(' ','_',$category->name);
											$uri = $service->inventory_url ?? $serviceUrl;
										@endphp
										<a href="{{$uri}}">
										<div class="card-body p-0">
											<!--begin::Image-->
											<div class="overlay">

													@if(!empty($service->img_svg))
															<?php
															if($service->img_svg_color == "Red") {
																$class_color = "danger";
															} else if($service->img_svg_color == "Yellow") {
																$class_color = "warning";
															} else if($service->img_svg_color == "Green") {
																$class_color = "success";
															} else if($service->img_svg_color == "Cyan") {
																$class_color = "primary";
															} else if($service->img_svg_color == "Purple") {
																$class_color = "info";
															} else if($service->img_svg_color == "Gray") {
																$class_color = "dark-50";
															} else {
																$class_color = "danger";
															}
															?>
															<div class="overlay-wrapper rounded bg-light text-center pt-14 pb-14" style="height: 170px;">
																<span class="svg-icon svg-icon-6x mr-5  svg-icon-{{$class_color}}">
																	<?=$service->img_svg?>
																</span>
															</div>
													@elseif(!empty($service->image))
															<div class="d-flex overlay-wrapper rounded bg-light text-center align-items-center justify-content-center" style="height: 170px;">
																<img style="height:90px;border-radius:5px" class="" src="{{env('GRP_HELPDESK_URL')}}/uploads/{{$service->image}}">
															</div>
													@else
															<div class="overlay-wrapper rounded bg-light text-center pt-14 pb-14" style="height: 170px;">
																<span class="svg-icon svg-icon-6x mr-5  svg-icon-danger">
																	<!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\General\Thunder-move.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																		<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																			<rect x="0" y="0" width="24" height="24"/>
																			<path d="M16.3740377,19.9389434 L22.2226499,11.1660251 C22.4524142,10.8213786 22.3592838,10.3557266 22.0146373,10.1259623 C21.8914367,10.0438285 21.7466809,10 21.5986122,10 L17,10 L17,4.47708173 C17,4.06286817 16.6642136,3.72708173 16.25,3.72708173 C15.9992351,3.72708173 15.7650616,3.85240758 15.6259623,4.06105658 L9.7773501,12.8339749 C9.54758575,13.1786214 9.64071616,13.6442734 9.98536267,13.8740377 C10.1085633,13.9561715 10.2533191,14 10.4013878,14 L15,14 L15,19.5229183 C15,19.9371318 15.3357864,20.2729183 15.75,20.2729183 C16.0007649,20.2729183 16.2349384,20.1475924 16.3740377,19.9389434 Z" fill="#000000"/>
																			<path d="M4.5,5 L9.5,5 C10.3284271,5 11,5.67157288 11,6.5 C11,7.32842712 10.3284271,8 9.5,8 L4.5,8 C3.67157288,8 3,7.32842712 3,6.5 C3,5.67157288 3.67157288,5 4.5,5 Z M4.5,17 L9.5,17 C10.3284271,17 11,17.6715729 11,18.5 C11,19.3284271 10.3284271,20 9.5,20 L4.5,20 C3.67157288,20 3,19.3284271 3,18.5 C3,17.6715729 3.67157288,17 4.5,17 Z M2.5,11 L6.5,11 C7.32842712,11 8,11.6715729 8,12.5 C8,13.3284271 7.32842712,14 6.5,14 L2.5,14 C1.67157288,14 1,13.3284271 1,12.5 C1,11.6715729 1.67157288,11 2.5,11 Z" fill="#000000" opacity="0.3"/>
																		</g>
																	</svg><!--end::Svg Icon-->
																</span>
															</div>
													@endif

											</div>
											<!--end::Image-->
											<!--begin::Details-->
											<div class="text-center mt-5 mb-md-0 mb-lg-5 mb-md-0 mb-lg-5 mb-lg-0 mb-5 d-flex flex-column">
												@php
													$serviceUrl = URL('/')."/create/$type?category=$category->id&request=$service->id&target=target-".str_replace(' ','_',$category->name);
													$uri = $service->inventory_url ?? $serviceUrl;
												@endphp
												<a href="{{$uri}}" class="font-size-h5 font-weight-bolder text-dark-75 text-hover-primary mb-1">{{ $service->name }}</a>
												<span class="font-size-lg">{{ $service->description }}</span>
											</div>
											<!--end::Details-->
										</div>
										</a>
									</div>
									<!--end::Card-->
								</div>
								<!--end::Product-->
								@endif
								@endforeach
							</div>

							<ul class="d-none dashboard-tabs nav nav-pills nav-danger row row-paddingless m-0 p-0 flex-column flex-sm-row" role="tablist">
								@foreach ($services as $service)
								<!--begin::Item-->
								<li class="nav-item d-flex col flex-grow-1 flex-shrink-0 mr-3 mb-3 mb-lg-0">
									<a class="nav-link border-nabati py-10 d-flex flex-grow-1 rounded flex-column align-items-center" href="{{URL('/')}}/create/service?category={{ $category->id }}&request={{ $service->id }}&target=target-{{str_replace(' ','_',$category->name)}}">
										<span class="nav-icon py-2 w-auto">
											<span class="svg-icon svg-icon-3x">
												<!--begin::Svg Icon | path:{{URL('/')}}/assets/media/svg/icons/Home/Library.svg-->
												<svg width="48px" height="48px" viewBox="0 0 48 48" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
													<!-- Generator: Sketch 50.2 (55047) - http://www.bohemiancoding.com/sketch -->
													<title>Stockholm-icons / Home / Lamp1</title>
													<desc>Created with Sketch.</desc>
													<defs></defs>
													<g id="Stockholm-icons-/-Home-/-Lamp2" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
														<rect id="bound" x="0" y="0" width="48" height="48"></rect>
														<path d="M12,2 C12.5522847,2 13,2.44771525 13,3 L13,11 C13,11.5522847 12.5522847,12 12,12 C11.4477153,12 11,11.5522847 11,11 L11,3 C11,2.44771525 11.4477153,2 12,2 Z" id="Combined-Shape" fill="#000000" opacity="0.3"></path>
														<path d="M8.12601749,19 L15.8739825,19 C15.4299397,20.7252272 13.8638394,22 12,22 C10.1361606,22 8.57006028,20.7252272 8.12601749,19 Z" id="Combined-Shape" fill="#000000" opacity="0.3"></path>
														<path d="M12,8 L12,8 C16.9705627,8 21,12.0294373 21,17 L3,17 L3,17 C3,12.0294373 7.02943725,8 12,8 Z" id="Rectangle-98" fill="#000000"></path>
													</g>
												</svg>
												<!--end::Svg Icon-->
											</span>
										</span>
										<span class="nav-text font-size-lg py-2 font-weight-bolder text-center">{{ $service->name }}</span>
										<span class="nav-text font-size-lg py-2 text-center">{{ $service->description }}</span>
									</a>
								</li>
								<!--end::Item-->
								@endforeach
							</ul>
							@else


							<div class="row pr-8">
								<!--begin::Product-->
								<div class="col-md-4 col-xxl-4 col-lg-4">
									<!--begin::Card-->
									<div class="card card-custom card-shadowless">
										<div class="card-body p-0">
											<!--begin::Image-->
											<div class="overlay">
												<div class="overlay-wrapper rounded text-center pt-0 pb-0">
															<img style="width:100%;border-radius:5px" class="" src="http://ideas.or.id/wp-content/themes/consultix/images/no-image-found-360x250.png">
												</div>

											</div>
											<!--end::Image-->
											<!--begin::Details-->
											<div class="text-center mt-5 mb-md-0 mb-lg-5 mb-md-0 mb-lg-5 mb-lg-0 mb-5 d-flex flex-column">
												<a href="#" class="font-size-h5 font-weight-bolder text-dark-75 text-hover-primary mb-1"></a>
												<span class="font-size-lg">Service Not Available For This Category</span>
											</div>
											<!--end::Details-->
										</div>
									</div>
									<!--end::Card-->
								</div>
								<!--end::Product-->
							</div>


							@endif
						</div>
						<?php $i++;?>
						@endforeach
					</div>
				</div>
			</div>
        </div>
		@else
		<span class="no-category ml-4" data-count-category="{{$count_cat}}">Tidak Ada Category yang tersedia</span>
		@endif
        @else
        <span class="ml-4">Tidak Ada Category yang tersedia</span>
        @endif
    </div>
</div>
<style>
.ps__rail-y,.ps__rail-x {
	display:none !important;
}

.card-left ::-webkit-scrollbar {
  width: 19px;
}

.card-left ::-webkit-scrollbar-track {
  background-color: transparent;
}

.card-left ::-webkit-scrollbar-thumb {
  background-color: #f7f7f7;
  border-radius: 20px;
  border: 6px solid transparent;
  background-clip: content-box;
}

.card-left ::-webkit-scrollbar-thumb:hover {
  background-color: #eaeaea;
}
</style>
<script>
    $(document).ready(function () {
        $('.category-item-left').click(function (e) {
            $('.show-services').removeClass('d-none');
        });
    });

	$('#select-division').change(function() {
		const val = $(this).val()
		window.location.href = '?division='+val
	})


</script>
@endsection
