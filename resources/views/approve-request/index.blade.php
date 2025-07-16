@extends('layouts.app')

@section('content')
<style>
.table-responsive tr {
	cursor: pointer;
}
#table_ticket {
	margin-top: 0 !important;
}
#kt_wrapper {
	background: #f0f7fd;
}
.label.label-inline.label-lg {
    padding: 0.4rem 0.75rem;
    height: auto;
}
.subheader {
	display:block !important;
	margin-left: -6px;
}
tbody tr td:last-child {
  display:none !important;
}
</style>

<style>
.content {
	padding-top:0 !important;
}
table.dataTable thead th, table.dataTable thead td {
    border-bottom: none !important;
}
th,td {
	vertical-align: middle !important;
}
.table th {
	line-height: 2 !important;
}
.table-responsive {
    min-height: .01%;
    overflow-x: scroll;
}
.table {
	background:#fff;
	
}
.table-striped tbody tr:nth-of-type(odd) {
    background-color: #ffffff;
}
thead {
	background:#f5f7f9;
}
tbody tr:first-child td {
	border-top:1px solid #cfd7df !important;
}
tr th {
	border-top:1px solid #cfd7df !important;
}
tbody tr td {
	border-right:none !important
}
tr {
	border-right:1px solid #cfd7df !important;
	border-left:1px solid #cfd7df !important;
}
tr td:first-child,tr th:first-child {
	border-left:1px solid #cfd7df !important;
}
tr th {
	border-right:1px solid #cfd7df !important;
	white-space:nowrap;
}
/*
.dataTables_wrapper .row:first-child {
	display:none;
}
*/
.custom-select {
    border-radius: 0.3rem;
}
div.dataTables_wrapper div.dataTables_filter input {
    max-width: 190px;
}
.dataTables_wrapper .dataTable {
    width: unset !important;
}
tr td {
	white-space:nowrap;
}
</style>
<div class="card card-custom gutter-b" style="min-width: 100%;box-shadow: none;margin-top: 0px;">
	<div class="card-body" style="background: #f3f6f9;padding: 0.2rem 0.7rem;">    
		@include('flash::message')
			
		<section id="ticket">
			<div class="p-3">
				<h3>Ticket</h3>
			</div>
			<div class="table-responsive pl-3 pr-3" style="overflow-x:scroll">
				<style>
				th {
					 font-weight:600 !important;
				}
				</style>
				<!--begin: Datatable-->
				<table class="table table-striped table-bordered" id="table_ticket"  >
					<thead>
						<tr>
							<th>#</th>
							<th>Ticket Number</th>
							<th>Request Type</th>
							<th>Request Name</th>
							<th>Title</th>
							<th>Service</th>
							<th>Service Category</th>
							<th>Status</th>
							<th>Related Ticket</th>
							<th>Request Date</th>
							<th>Request By</th>
							<th style="display:none">Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php $no = 1; 
						
						?>
						@foreach ($tickets as $ticket)
						<tr>
							<td>{{ $no }}</td>
							<td>{{ $ticket->ref }}</td>
							<td><?php if ($ticket->finalclass == "service_request") {
										echo "Service";
									} elseif ($ticket->finalclass == "problem_request") {
										echo "Problem";
									} else {
										echo "Incident";
									}
									?></td>
							<td><?php
									$service = DB::table('service')->where('id', $ticket->service_id)->first();
								?>{{ $service->name  ?? '-'}}
							</td>
							<td>{{ $ticket->title }}</td>
							<td><?php
									$service = DB::table('service')->where('id', $ticket->service_id)->first();
								?>{{ $service->name  ?? '-'}}</td>
							<td><?php
									$category = DB::table('service_category')->where('id', $ticket->servicesubcategory_id)->first();
								?>{{ isset($category->name) ? $category->name : '-' }}</td>
							<td><?= statusHtml($ticket->status) ?></td>
							<td>{{ $ticket->related_ticket ?? '-' }}</td>
							<td>{{ date('d M Y', strtotime($ticket->created_at)) }}</td>
							<td><?php
									$name = DB::table('users')->where('id', $ticket->created_by)->first();
								?>{{ $name->name }}</td>
							<td style="display:none">{{URL('/')}}/approve-request/{{ $ticket->token }}</td>
						</tr>
						<?php $no++;?>
						@endforeach
					</tbody>
				</table>
				<!--end: Datatable-->
			</div>
		</section>

		<section id="goods-issue" class="mt-5">
			<div class="p-3">
				<h3>Goods Issue</h3>
			</div>
			@include('approve-request.goods_issue_list')
		</section>
	</div>
</div>
<script>
   $(document).ready( function () {
		setTimeout(function() {
			console.log("ok");
			$(".table-responsive").on('click', 'tbody tr', function(){
				//alert($(this).children("td").last().children('.edit-button').attr('href'));
				window.open($(this).children("td").last().html(), '_blank');
				//window.location.replace($(this).children("td").last().children('a').attr('href'));
			});
		}, 1500);
	});
</script>
<script>
$(document).ready( function () {
$('#table_ticket').DataTable({
});  

	$( "td" ).each(function( index ) {
		console.log($(this).html());
		if(!$(this).html().includes("actioncolumncell")) {
			if(!$(this).html().includes("http://")) {
				if(!$(this).html().includes("https://")) {
					if(!$(this).html().includes("<img")) {
						if(!$(this).html().includes("element-for-status")) {
							if($(this).html().length > 30) {
								$(this).html(''+'<div data-placement="bottom" data-trigger="hover" data-toggle="popover" title="" data-html="true" data-content="'+$(this).html()+'" >'+$(this).html().substring(0, 30)+"...")+'</div>';
							}

							$(this).hover(
							  function() {

								//$( this ).addClass( "hover" );
							  }, function() {
								//$( this ).removeClass( "hover" );
							  }
							);
						}
					}
				}
			}
		}
		//$(function () {
		  $('[data-toggle="popover"]').popover()
		//})
	  //console.log( index + ": " + $( this ).text() );
	});
});
</script>
@endsection
