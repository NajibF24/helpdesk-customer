@extends('layouts.app')
@section('title',$title)
@section('content')

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
.dataTables_wrapper .dataTable th, .dataTables_wrapper .dataTable td {

  padding: 0.6rem 0.8rem;
}
.dataTables_wrapper .row:first-child {
	display:none;
}
</style>
<!--begin::Card-->
<div class="card card-custom gutter-b" style="box-shadow: none;">
	<div class="card-body" style="background: #ebeff3;padding: 0.2rem 0.7rem;">
		@include('flash::message')
		<?php
		$organization = DB::table('organization_level')->pluck('name','name')->toArray();
		if(in_array($table,$organization)){
		?>
			<ul class="nav nav-pills nav-justified" style="margin-bottom: 10px;">
			  @foreach($organization as $org_level)
				  <li class="nav-item">
					<a class="nav-link {{$org_level==$table?'active':''}}" href="{{URL('/').'/list/'.$org_level}}">{{$org_level}}</a>
				  </li>
			  @endforeach
			</ul>
		<?php } ?>
		@if(!in_array($table,['product']))
		<div class="row">
			<div class="col-md-12">
				<a  href="{!! route('create',$table) !!}" class="btn btn btn-sm btn-outline-dark btn-white-line btn-sm  mr-1 " style="float:right;width: 100px;margin-left:10px;">
					 Create Item
				</a>
			</div>
		</div>
		@endif



			<div class="table-responsive" >
				<?php
					$identifier =  rand(pow(10, 12-1), pow(10, 12)-1);
					$id_table = "table-".$identifier;
				?>
				<table class="table table-striped table-bordered" id="{{$id_table}}" style="width:100% !important">
					<thead>
						<tr>
							<th>ID</th>

							@foreach($column as $key => $c)
								<?php //disini opsional jika user input key string maka dipilih keynya sebagai header?>
								@if($c != "id")
									@if(is_numeric($key))
										<th>{{ ucwords(str_replace("_"," ",$c)) }}</th>
									@else
										<th>{{ $key }}</th>
									@endif
								@endif
							@endforeach
							<th>Action</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><input type="text" class="column-search" id="id"/></th>

							@foreach($column as $c)
								@if($c != "id")
								<th>
									<input type="text" class="column-search" id="{{$c}}"/>
								</th>
								@endif
							@endforeach
							<th></th>
						</tr>
					</tfoot>

				</table>
			</div>
	</div>
</div>
<!--end::Card-->

@endsection


@section('js')
<script>

	var table_index ;
   $(document).ready( function () {
		$(".box-arrow-left,.box-arrow-left2").click(function () {
		  var leftPos = $('.table-responsive').scrollLeft();
		  $(".table-responsive").animate({scrollLeft: leftPos - 200}, 300);
		});

		$(".box-arrow-right,.box-arrow-right2").click(function () {
		  var leftPos = $('.table-responsive').scrollLeft();
		  $(".table-responsive").animate({scrollLeft: leftPos + 200}, 300);
		});
		table_index = $("#{{$id_table}}").DataTable({
				order: [[ 0, "desc" ]],
				responsive: true,
				//pageLength: 60,
				processing: true,
				serverSide: true,
				ajax: {
					url: "{{ url('listServer').'/'.$table .'/'.$disposal }}",
					type: 'GET',
					data: function (d) {
						@if(isset($filter))
							// read start date from the element
							d.from = $('#start_date').val();
							// read end date from the element
							d.to = $('#end_date').val();
						@endif
						d.id = $('#id').val();
						@foreach($column as $c)
							@if($c != "id")
								d.{{$c}} = $('#{{$c}}').val();
							@endif
						@endforeach

					}
				},
				columns: [
						{ data: 'id', name: 'id' },
						@foreach($column as $c)
							@if($c != "id")
								{ data: '{{$c}}', name: '{{$c}}' },
							@endif
						@endforeach

						{ data: 'action', name: 'action' },
						//{ data: 'start_date', name: 'start_date' },
						//{ data: 'end_date', name: 'end_date' }
					 ]
        });
		//$('#search-form').on('submit', function(e) {
			//console.log("call");
			//table_index.draw();
			//e.preventDefault();
		//});
		$('.column-search').on('change', function(e) {
			console.log("call");
			table_index.draw();
			e.preventDefault();
		});
		//$('.reset-btn').on('click', function(e) {
			//$('#start_date').val("");
			//$('#end_date').val("");
			//console.log("call");
			//table_index.draw();
			//e.preventDefault();
		//});
     });
     function reloadTable() {
		 table_index.ajax.reload();
	 }



  </script>
<style>
tfoot input {
	width:100%;
	border-radius: 2px;
	border: 1px solid #e2e7f1;
	box-shadow: none;

}
tfoot input:focus {

  border: 1px solid #e2e7f1;
}
</style>
  @endsection
