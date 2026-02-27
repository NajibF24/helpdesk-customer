@extends('layouts.app')

@section('content')
<style>
.select2-container {
	width:100% !important;
}
</style>
<div class="container">
		<div class="row">
			<div class="col-md-9">
				<form id="form_submit" action="{{URL('/')}}/store/incident" method="POST">
					@csrf
				<!--begin::Card-->
				<div class="card card-custom gutter-b example example-compact">
					<div class="card-header">
						<h3 class="card-title">Base Controls</h3>
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
							//$RM = DB::table('request_management')->where('id',4)->first();
							//var_dump($request_management);
							//die;
							//GET FORMBUILDER
							$form_builder = DB::table('form_builder')->where('id',$request_management->form_builder)->first();
							$option = DB::table('service_category')
										->pluck('name', 'id')
										//->where('type','Incident')
										->toArray();
							$option = [""=>"-Select Service Category-"]+ $option;
							?>
							<div class="form-group row">
								<label>
									Originator
									<span class="text-danger">*</span>
								</label>
								<div class="col-lg-12 col-md-9 col-sm-12">
									<select class="form-control selectpicker" data-live-search="true" name="agent" required>
										@foreach ($originator as $key => $origin)
											@if (isset($origin->name))
												<option value="{{ $origin->id }}">{{ $origin->name }}</option>
											@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group row">
                            	<label>
                                	Requester<span class="text-danger">*</span>
								</label>
								<div class="col-lg-12 col-md-9 col-sm-12">
									<div class="radio-inline">
										<label class="radio radio-success">
										<input type="radio" name="request_for" value="myself" />
										<span></span>My Self</label>
										<label class="radio radio-success">
										<input type="radio" name="request_for" value="other" />
										<span></span>Other</label>
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-form-label t col-lg-3 col-sm-12">
									Subject
									<span class="text-danger">*</span>
								</label>
								<div class="col-lg-12 col-md-9 col-sm-12">
									<input class="form-control" type="text" />
								</div>
							</div>
							<div class="form-group row">
								<label class="col-form-label  col-lg-3 col-sm-12">
									Description
									<span class="text-danger">*</span>
								</label>
								<div class="col-lg-12 col-md-9 col-sm-12">
									<textarea name="content" class="form-control" data-provide="markdown" rows="10"></textarea>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-form-label  col-lg-3 col-sm-12">
									Upload
									<span class="text-danger">*</span>
								</label>
								<div class="col-lg-12 col-md-9 col-sm-12">
									<input type="file" class="custom-file-input" id="customFile" />
									<label class="custom-file-label" for="customFile">Choose file</label>
								</div>
							</div>
							
							<input type="hidden" name="request_management" value="{{$request_management->id}}"/>
<!--
							<div class="form-group">
								{!! Form::label('category', 'Incident Category') !!}
								
							</div>
							<div class="form-group">
								{!! Form::label('request', 'Incident Request') !!}
								
							</div>
-->
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
								
								<div id="fb-render" ></div>
							
						</div>
						<div class="card-footer">
							<button type="button" id="btn-submit" class="btn btn-primary mr-2 btn-submit">Submit</button>
							<button type="reset" class="btn btn-secondary">Cancel</button>
						</div>
				</div>
				<!--end::Card-->
				</form>
			</div>
		</div>
	</div>



<!--
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script> 
-->
<script src="http://formbuilder.online/assets/js/form-builder.min.js"></script> 
<script src="https://formbuilder.online/assets/js/form-render.min.js"></script> 
<script> 
	const getUserDataBtn = document.getElementById("get-user-data"); 
	const fbRender = document.getElementById("fb-render");
</script>
  <script>
	  var formBuilder;
	  let fields = [];
		<?php 
			$fb_objects = DB::table('form_builder_object')->get();
			foreach($fb_objects as $f) {
				?>  
				fields.push({
				  label: '{{$f->name}}',
				  attrs: {
					type: '{{strtolower($f->type!="Dropdown"?$f->type:$f->data_source)}}'
				  },
				  icon: '<i  class="fas fa-cube" style="    color: #5f636f;"></i>'
				});
				<?php 
			}
		?>
		<?php $list_data_source = DB::table('form_builder_object')->whereIn('type',['Dropdown'])->pluck('data_source','data_source')->toArray(); 
		$list_data_source = array_unique($list_data_source);

		?>
		setTimeout(function() {
			// Your code here
			$('.form-actions').hide();
			
			@foreach($list_data_source as $data_source)
				<?php $data_source = strtolower($data_source);?>
				  console.log('{{URL("/")}}/select2list/{{$data_source}}');
					$.ajax({
						type: "GET",
						url: '{{URL("/")}}/select2list/{{$data_source}}',
						//data: data_input,
						//dataType: 'json',
						success: function(data){
							console.log(data);
							var obj = JSON.parse(data);
							$('.target-{{$data_source}}').select2({
							  data: obj
							})
						},
						error: function(){console.log("error");}
					});
			@endforeach
		}, 500);
		
		let list_data_source_js = [];
		
		@foreach($list_data_source as $data_source)
			list_data_source_js.push('{{strtolower($data_source)}}');
		@endforeach
		
		
		let templates = {
			@foreach($list_data_source as $data_source)
				  <?php $data_source = strtolower($data_source);?>
				  {{$data_source}}: function(fieldData) {
					return {
					  field: '<span id="'+fieldData.name+'">',
					  onRender: function() {
						  console.log(fieldData);
						  <?php //get object dari type yang dipanggil ?>
							let content = '<div class="form-group ">\
												<select style="width:100%" class="select2formbuilder form-control '+fieldData.label+' target-'+fieldData.type+' " id="'+fieldData.name+'" name="'+fieldData.name+"_"+fieldData.type+"_"+fieldData.label+'">\
													<option value="" selected="selected">-Select '+fieldData.label+'-</option>\
												</select>\
											</div>';
						  $(document.getElementById(fieldData.name)).html(content);
					  }
					};
				  },
			@endforeach
		};
		
	
  jQuery(function($) {
		setTimeout(function() {
			// Your code here
			$('.form-actions').hide();
			
		}, 2000);

		const formData = '<?=htmlspecialchars_decode($form_builder->json)?>';
		formBuilder = $(document.getElementById('fb-render')).formRender({dataType: 'json',formData: formData,fields, templates,controlOrder: list_data_source_js});
		
		//document.getElementById('btn-submit').addEventListener('click', function() {
			//alert('check console');
			//console.log(formBuilder.actions.getData());
		  //});
		
		$('.btn-submit').click(function() {
			// your code here
			//alert(formBuilder.actions.getData('json'));
			console.log(JSON.stringify(formBuilder.userData));
			var datastring = $("#form_submit").serialize();
			var datasubmit = datastring+"&form_data_json="+JSON.stringify(formBuilder.userData);
			console.log(datasubmit);
			$.ajax({
				type: "POST",
				url: $("#form_submit").attr("action"),
				data: datasubmit,
				//dataType: "json",
				success: function(data) {
					console.log(data);
					//var obj=data;
					var obj = JSON.parse(data); 
					window.location = obj.redirect;
					if(obj.success) {
						window.location = obj.redirect;
						//console.log("s2");
					} else {
						window.location = obj.redirect;
					}
				},
				error: function() {
					alert('error handling here');
				}
			});

		});
  });
  </script>
@endsection
