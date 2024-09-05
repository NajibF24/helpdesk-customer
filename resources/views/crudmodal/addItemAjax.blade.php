<style>
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
</style>
<?php 
$identifier =  rand(pow(10, 12-1), pow(10, 12)-1);
?>

<div class="modal fade" id="modal-{{$identifier}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
	<div class="modal-content modal-add-ajax">

			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Add {{$title}} Objects</h5>
				<button type="button" class="btn-close close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body ">

					@include('flash::message')



									<div class="table-responsive" >
										<form id="form-{{$identifier}}">
											<input type="hidden" name="type" value="{{$table}}" />
											<?php
												$identifier_table =  rand(pow(10, 12-1), pow(10, 12)-1);
												$id_table = "table-".$identifier_table; 
											?>
											<table class="table table-striped table-bordered" id="{{$id_table}}" style="width:100% !important">
												<thead>
													<tr>
														<th><input type="checkbox" class="checkAll" id="check-all" name="check-all" value="check-all"></th> 
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
													</tr>
												</thead>
												<tfoot>
													<tr>
														
														<th></th>
														<th><input type="text" class="column-search form-control" id="id$identifier_table"  style="width:unset"/></th> 
														
														@foreach($column as $c)
															@if($c != "id")
															<th>
																<input type="text" class="column-search form-control" id="{{$c.$identifier_table}}" style="width:unset"/>
															</th> 
															@endif
														@endforeach
													</tr>
												</tfoot>

											</table>
										</form>
									</div>
						<div class="row mb-2 mt-2">

							<div class="col-sm-12">

								<button type="button" class="addItemAction btn btn-primary float-right" data-target-form="form-{{$identifier}}" data-target-modal="modal-{{$identifier}}" data-target-table="{{$target_table}}">
									Add
								</button>
								<script>

								</script>
								<button type="button" class="btn btn-secondary float-right" data-dismiss="modal" data-bs-dismiss="modal" style="margin-right:10px;">Cancel</button>
							</div>
						</div>

			</div>
			<div class="modal-footer">

			</div>

	</div>
  </div>
</div>

<script>
$( document ).ready(function() {
    $('#modal-{{$identifier}}').modal('show')
});
</script>

<script>
	$(".checkAll").click(function(){
		$('.result-checkbox').not(this).prop('checked', this.checked);
	});	
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
		console.log("{{ url('addItemAjaxGetListAjax').'/'.$table .'/'.$disposal }}");
		table_index = $("#{{$id_table}}").DataTable({
				columnDefs: [
				  { targets: 'checkbox', orderable: false }
				],
				order: [[ 0, "desc" ]],
				responsive: true,
				//pageLength: 60,
				processing: true,
				serverSide: true,
				ajax: {
					url: "{{ url('addItemAjaxGetListAjax').'/'.$table .'/'.$disposal }}",
					type: 'GET',
					data: function (d) {
						
						@if(isset($filter))	
							// read start date from the element
							d.from = $('#start_date').val();
							// read end date from the element
							d.to = $('#end_date').val();
						@endif
						d.id = $('#id{{$identifier_table}}').val();
						@foreach($column as $c)
							@if($c != "id")
								d.{{$c}} = $('#{{$c.$identifier_table}}').val();
							@endif
						@endforeach

					}
				},
				columns: [
						{ data: 'checkbox', name: 'checkbox' , sortable: false},
						{ data: 'id', name: 'id' },
						@foreach($column as $c)
							@if($c != "id")
								{ data: '{{$c}}', name: '{{$c}}' },
							@endif
						@endforeach
						
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
			//$('#skmds-table_filter > input').val('');
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

