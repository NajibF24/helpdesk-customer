@extends('layouts.app')

@section('content')
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
@if(false)
<div class="row">

<div class="col-lg-8 pl-7 pt-0">
	<!--begin::Advance Table Widget 4-->
	<div class="card  card-stretch gutter-b">
		<!--begin::Header-->
		<div class="card-header border-0 py-5">
			<h3 class="card-title align-items-start flex-column mb-0 mt-2">
				<span class="card-label font-weight-bolder text-dark">List Of Approval</span>
				<span class="text-muted mt-3 font-weight-bold font-size-sm" style="display:block">This is the list than need to Approve by You</span>
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
											->whereIn('ticket.status',['Submit for Approval','Waiting for User'])
											//->where('next_approval_id', Auth::user()->person)
											
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
									<a href="{{URL('/')}}/edit/{{$ticket->id}}/{{$ticket->finalclass}}" class="btn btn-light-success font-weight-bolder font-size-sm">Detail</a>
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
											->join('service', 'service.id', '=', 'ticket.service_id')
											//->whereIn('ticket.status',['Submit for Approval','Waiting for User'])
											//->where('next_approval_id', Auth::user()->person)
											->orderBy('created_at','desc')
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
									<a href="{{URL('/')}}/edit/{{$ticket->id}}/{{$ticket->finalclass}}" class="btn btn-light-success font-weight-bolder font-size-sm">Detail</a>
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


<div class="col-lg-4 pl-7 pt-0">
	
					
						<!--begin::Card-->
						<div class="card card-custom gutter-b">
							<div class="card-body" >
									<h3 class="dash-title mb-5">Current Ticket Status</h3>
									<!--start::Chart-->
									<figure class="highcharts-figure">
										<div id="container-chart4"></div>
									</figure>
									<!--end::Chart-->
							</div>
						</div>
						<!--end::Card-->

						<?php 
						$list_status = ['Closed', 'Resolved', 'On Progress', 'Open', 'Submit for Review'];
						$list_type = ['Service'=>'service_request','Incident'=>'incident_management','Problem'=>'problem'];
						$arr_data = [];
						foreach($list_type as $type_label => $type_class) {
							$data = [];
							foreach($list_status as $s) {
							
								$count = DB::table('ticket')
															->whereIn('ticket.status',[$s])
															->where('finalclass',$type_class)
															->count();
								$data[] = $count;
								//if($count) {
									//$arr_data[] = ['name'=>$p->priority,'data'=>$count
															////,'drilldown'=>$p->priority
														//];
								//}							
							}
							$arr_data[] = ['name'=>$type_label,'data'=>$data];
						}
						$json_data = json_encode($arr_data);
						//var_dump($json_data);
						?>
						<script>
						var data_json7 = <?=$json_data?>;
						Highcharts.chart('container-chart4', {
							chart: {
								type: 'bar'
							},
							title: {
								text: ''
							},
							//subtitle: {
								//text: 'Source: <a href="https://en.wikipedia.org/wiki/World_population">Wikipedia.org</a>'
							//},
							xAxis: {
								categories: ['Closed', 'Resolved', 'On Progress', 'Open', 'Submit for Review'],
								title: {
									text: null
								}
							},
							yAxis: {
								min: 0,
								//title: {
									//text: 'Population (millions)',
									//align: 'high'
								//},
								labels: {
									overflow: 'justify'
								}
							},
							tooltip: {
								valueSuffix: ' '
							},
							plotOptions: {
								bar: {
									dataLabels: {
										enabled: true
									}
								}
							},
							legend: {
								layout: 'vertical',
								align: 'right',
								verticalAlign: 'top',
								x: -0,
								y: 120,
								floating: false,
								borderWidth: 1,
								backgroundColor:
									Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
								shadow: true
							},
							credits: {
								enabled: false
							},
							series: data_json7
							//[{
								//name: 'Year 1800',
								//data: [107, 31, 635, 203, 2]
							//}, {
								//name: 'Year 1900',
								//data: [133, 156, 947, 408, 6]
							//}, {
								//name: 'Year 2000',
								//data: [814, 841, 3714, 727, 31]
							//}, {
								//name: 'Year 2016',
								//data: [1216, 1001, 4436, 738, 40]
							//}]
						});
						</script>
					
	<!--begin::Advance Table Widget 4-->
	<div class="card  card-stretch gutter-b">
		<!--begin::Header-->
		<div class="card-header border-0 py-5">
			<h3 class="card-title align-items-start flex-column mb-0 mt-2">
				<span class="card-label font-weight-bolder text-dark">List of Agents</span>
				<span class="text-muted mt-3 font-weight-bold font-size-sm" style="display:block">This is active agents</span>
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
									<span class="text-dark-75">Agent / Join Date</span>
								</th>
								<th style="min-width: 100px"><span class="text-dark-75">team  / company</span></th>
								
							</tr>
						</thead>
						<tbody>
							
							<?php 
							$color = ['info','success','warning','primary','danger'];
							$list_agents = DB::table('contact')
											->select('contact.*','team.name AS team_name','company.name AS company_name')
											//->join('service', 'service.id', '=', 'ticket.service_id')
											->join('lnkemployeetoteam', 'contact.id', '=', 'lnkemployeetoteam.employee_id')
											->leftJoin('contact AS team', 'team.id', '=', 'lnkemployeetoteam.team_id')
											->leftJoin('company', 'company.id', '=', 'contact.company')
											->whereIn('contact.status',['Active','Inactive'])
											->where('contact.type', 'Employee')
											->orderBy('contact.created_at','desc')
											->limit(5)
											->get();
							$n = 0;
							?>
							@foreach($list_agents as $agent)
							<tr>
								
								<td class="pl-0 py-8">
									<div class="d-flex align-items-center">
										<?php 
										$acronym = substr($agent->name,0,1);
										//$words = explode(" ", $name);
										//$acronym = "";

										//foreach ($words as $w) {
										  //$acronym .= $w[0];
										//}
										?>
										<div class="mr-4 symbol symbol-40 symbol-light-{{$color[(($n++)%4)]}} flex-shrink-0">
											<span class="symbol-label font-size-h4 font-weight-bold">{{$acronym}}</span>
										</div>
										<div>
											<a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">{{ $agent->name }}</a>
											<span class="text-muted font-weight-bold d-block">{{ date('d M Y', strtotime($agent->created_at)) }}</span>
										</div>
									</div>
								</td>
								<td>
									<span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{$agent->team_name}}</span>
									<span class="text-muted font-weight-bold">{{$agent->company_name}}</span>
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


<div class="col-lg-9 pl-7 pt-0">
</div>



</div>	<!--Close Row-->
@endif
</div>
@endsection
