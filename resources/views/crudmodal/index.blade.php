@extends('layouts.app')
@section('title',$title)
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
.dataTables_wrapper .dataTable {
    width: unset !important;
}
tr td {
	white-space:nowrap;
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
<!--begin::Card-->
<div class="card card-custom gutter-b ml-3 mr-3" style="box-shadow: none;">
	<div class="card-body" style="background: #f3f6f9;padding: 0.2rem 0.7rem;">
		@include('flash::message')
		@if(!in_array($table,['product','problem_request']))
		@elseif($table == "problem_request")
		@endif

			<div id="monitoring" class="table-responsive" style="overflow-x:scroll">

				<?php
					$identifier =  rand(pow(10, 12-1), pow(10, 12)-1);
					$id_table = "table-".$identifier;
				?>
				<style>
				th {
					 font-weight:600 !important;
				}
				</style>
				<table class="table table-striped table-bordered" id="{{$id_table}}"  >
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
							<th style="display:none" class="d-none">Action</th>


						</tr>
					</thead>
					<!-- <tfoot>
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
					</tfoot> -->

				</table>
			</div>
	</div>
</div>
<!--end::Card-->
<input type="hidden" value='{{date("Y-m-d", strtotime("first day of this month"))}}' id="start_date">
<input type="hidden" value='{{date("Y-m-d", strtotime("last day of this month"))}}' id="end_date">
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

		$("body").on('click', '.btn-delete-id', function() {
			var id = $(this).data('id');
			var nama_table = $(this).data('table');
			Swal.fire({title: "Confirmation",text: "Are you sure want to delete this record ?", icon: "question", showCancelButton: true,confirmButtonText: "Yes!"}).then(function(result) {
				if (result.value) {
					window.location = "/delete/"+id+"/"+nama_table;
				}
			});
		});

		table_index = $("#{{$id_table}}").DataTable({
				 "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
					 //console.log(aData);
					 //console.log(iDisplayIndexFull);
					  // Bold the grade for all 'A' grade browsers
					  //if ( aData[4] == "A" )
					  //{
						//$('td:eq(4)', nRow).html( '<b>A</b>' );
					  //}
					},
				order: [[ 0, "desc" ]],
				responsive: true,
				//pageLength: 60,
				processing: true,
				serverSide: true,
				ajax: {
					url: "{{ url('listServer').'/'.$table .'/'.$disposal }}",
					type: 'GET',
					data: function (d) {
						@if($status_ticket)
							d.status_ticket = "{{$status_ticket}}";

							//if ($('#start_date').val()) {
								//d.start_date = $('#start_date').val();
								//d.end_date = $('#end_date').val();
							//}
						@endif

						@if($start_date)
							d.start_date = "{{$start_date}}";
						@endif
						@if($end_date)
							d.end_date = "{{$end_date}}";
						@endif
						@if($ticket_type)
							d.ticket_type = "{{$ticket_type}}";
						@endif

                        @if($state)
                            d.state = "{{$state}}";
                        @endif

						@if($requester)
                            d.requester = "{{$requester}}";
                        @endif

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

						setTimeout(function() {

							$( 'tbody tr' ).each(function( index ) {
								$(this).children("td").last().hide();
							});




						}, 200);
					}
				},
				"fnDrawCallback": function( oSettings ) {
					//popover load
					//$('[data-toggle="popover"]').popover()
				},

				columns: [

						{ data: 'id', name: 'id' },
						@foreach($column as $c)
							@if($c != "id")
								{ data: '{{$c}}', name: '{{$c}}',
									"render": function( data, type, row, meta) {
										//popover modif content
										var val_cell = row.{{$c}};
										if (val_cell != null) {
											val_cell = val_cell + "";
											if(!val_cell.includes("actioncolumncell")) {
												if(!val_cell.includes("http://")) {
													if(!val_cell.includes("https://")) {
														if(!val_cell.includes("<img")) {
															if(!val_cell.includes("element-for-status")) {
																if(val_cell.length > 50) {
																	val_cell = (''+'<div data-placement="bottom" data-trigger="hover" data-toggle="popover" title="" data-html="true" data-content="'+val_cell+'" >'+val_cell.substring(0, 30)+"...")+'</div>';
																}
															}
														}
													}
												}
											}

										}
										return val_cell;
                                    }

								},
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
		@if (session('status') == 200)
			Swal.fire(
				"Created",
				'Data has been successfully created',
				"success"
			)
		@elseif (session('status') == 202)
			Swal.fire(
				"Deleted",
				"Data has been successfully deleted",
				"success"
			)
		@elseif (session('status') == 204)
			Swal.fire(
				"Updated",
				"Data has been successfully updated",
				"success"
			)
		@elseif (session('status') == 400)
			Swal.fire(
				"Failed",
				"Data does not updated",
				"error"
			)
		@elseif (session('status') == 403)
			Swal.fire(
				"Failed",
				"Permission denied",
				"error"
			)
		@elseif (session('status') == 404)
			Swal.fire(
				"Failed",
				"Berkas tidak ditemukan",
				"error"
			)
		@endif
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
<style>
.checkbox2 {
  --background: #fff;
  --border: #D1D6EE;
  --border-hover: #BBC1E1;
  --border-active: #7d8388;
  --tick: #fff;
  position: relative;
  margin-bottom:2px;
  vertical-align: middle;
}
.checkbox2 input,
.checkbox2 svg {
  width: 17px;
  height: 17px;
  display: block;
}
.checkbox2 input {
  -webkit-appearance: none;
  -moz-appearance: none;
  position: relative;
  outline: none;
  background: var(--background);
  border: none;
  margin: 0;
  padding: 0;
  cursor: pointer;
  border-radius: 4px;
  transition: box-shadow 0.3s;
  box-shadow: inset 0 0 0 var(--s, 1px) var(--b, var(--border));
}
.checkbox2 input:hover {
  --s: 2px;
  --b: var(--border-hover);
}
.checkbox2 input:checked {
  --b: var(--border-active);
}
.checkbox2 svg {
  pointer-events: none;
  fill: none;
  stroke-width: 2px;
  stroke-linecap: round;
  stroke-linejoin: round;
  stroke: var(--stroke, var(--border-active));
  position: absolute;
  top: 0;
  left: 0;
  width: 17px;
  height: 17px;
  transform: scale(var(--scale, 1)) translateZ(0);
}
.checkbox2.path input:checked {
  --s: 2px;
  transition-delay: 0.4s;
}
.checkbox2.path input:checked + svg {
  --a: 16.1 86.12;
  --o: 102.22;
}
.checkbox2.path svg {
  stroke-dasharray: var(--a, 86.12);
  stroke-dashoffset: var(--o, 86.12);
  transition: stroke-dasharray 0.6s, stroke-dashoffset 0.6s;
}
.checkbox2.bounce {
  --stroke: var(--tick);
}
.checkbox2.bounce input:checked {
  --s: 11px;
}
.checkbox2.bounce input:checked + svg {
  -webkit-animation: bounce 0.4s linear forwards 0.2s;
          animation: bounce 0.4s linear forwards 0.2s;
}
.checkbox2.bounce svg {
  --scale: 0;
}

@-webkit-keyframes bounce {
  50% {
    transform: scale(1.2);
  }
  75% {
    transform: scale(0.9);
  }
  100% {
    transform: scale(1);
  }
}

@keyframes bounce {
  50% {
    transform: scale(1.2);
  }
  75% {
    transform: scale(0.9);
  }
  100% {
    transform: scale(1);
  }
}

</style>

<script>
   $(document).ready( function () {
		setTimeout(function() {
			console.log("ok");
			$(".table-responsive").on('click', 'tbody tr', function(){
				//alert($(this).children("td").last().children('.edit-button').attr('href'));
				window.open($(this).children("td").last().html(), '_blank');
				//window.location.replace($(this).children("td").last().children('a').attr('href'));
			});

			$( 'tbody tr' ).each(function( index ) {
				$(this).children("td").last().hide();
			});
		}, 500);

	});
</script>
  @endsection
