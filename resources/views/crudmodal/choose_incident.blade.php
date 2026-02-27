@extends('layouts.app')

@section('content')
<style>
.select2-container {
	width:100% !important;
}
</style>
<div class="container">
		<div class="row">
			<div class="col-md-6">
				@include('flash::message')
				<form action="{{URL('/')}}/create/incident" method="GET">
				<!--begin::Card-->
			
				<div class="card card-custom bg-light gutter-b example example-compact">
					<div class="card-header">
						<h3 class="card-title">Choose Incident Category</h3>
						<div class="card-toolbar">
							<div class="example-tools justify-content-center">
								<span class="example-toggle" data-toggle="tooltip" title="" data-original-title="View code"></span>
								<span class="example-copy" data-toggle="tooltip" title="" data-original-title="Copy code"></span>
							</div>
						</div>
					</div>
						<div class="card-body">
							<div id="get-user-data"></div>
							

							<?php 
							$option = DB::table('service_category')
										->pluck('name', 'id')
										//->where('type','Incident')
										->toArray();
							$option = [""=>"-Select Service Category-"]+ $option;
							?>
							<div class="form-group">
								{!! Form::label('category', 'Incident Category') !!}
								{!! Form::select('category', $option, null, ['class' => 'select2 form-control','id'=>'select-category']) !!}
							</div>
							<div class="form-group">
								{!! Form::label('request', 'Incident Request') !!}
								{!! Form::select('request', [""=>"-Select Incident-"], null, ['class' => 'select2 form-control','id'=>'service_name']) !!}
							</div>
							<script>
								$('#select-category').on("change", function(e) {
									$.ajax({
										type: "GET",
										url: '{{URL("/")}}/select2list/service?service_category='+$(this).val(),
										//data: data_input,
										//dataType: 'json',
										success: function(data){
											console.log(data);
											var obj = JSON.parse(data);
											$('#service_name').select2('data', null);
											 $('#service_name').select2('data', {id: null, text: null});
											 $('#service_name').off('select2:select');
											 $('#service_name').select2('destroy');
											 $('#service_name').html("");
											$('#service_name').select2({
											  data: obj
											})
											//var newOption = new Option(data.text, data.id, false, false);
											//$('#mySelect2').append(newOption).trigger('change');
										},
										error: function(){console.log("error");}
									});
								   //console.log("select");
								   //console.log($('#select-request_type').val());
								});
							</script>
								
							
						</div>
						<div class="card-footer">
							<button type="submit" class="btn btn-primary mr-2">Submit</button>
							<button type="reset" class="btn btn-secondary">Cancel</button>
						</div>
						
				</div>
				<!--end::Card-->
				</form>
			</div>
		</div>
	</div>

@endsection
