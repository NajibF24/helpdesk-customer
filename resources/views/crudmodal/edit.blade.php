
@extends('layouts.app')

@section('content')
<style>
#kt_subheader {
	display:none;
}
#kt_content {
	padding-top: 0;
	margin-top: -45px;
    margin-left: 5px;
}
.ui-timepicker-standard {
	z-index: 100000 !important;
}
</style>
{!! Form::model(($berkas ?? ''), ['route' => ['update', $berkas->id, $type], 'method' => 'post', 'files' => true]) !!}
<div class="card "  style="">
  <div class="card-header" style="padding:0.7rem 1.7rem;background:#f5f7f9;">
		<span style="font-weight: 500;font-size: 15px;line-height: 2;">Edit Item</span>
		<button type="submit" class="btn btn-dark btn-sm blue-black" style="width: 70px;float:right;margin-left:10px">Create</button>
		<a href="{!! route('list', [$type]) !!}" class="btn btn-sm btn-outline-dark btn-white-line"  style="width: 70px;float:right;margin-left:10px">Cancel</a>
  </div>
  <div class="card-body">



			@include('flash::message')

			@include('adminlte-templates::common.errors')
			<?php
			$table = $type;
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

			@include('crudmodal.menutab')
			<div class="row content-tab-home mt-4">
    @csrf

						@foreach($column as $key => $c)
							<?php $c['field_name'] = $key; //set key as field name ?>
							@if ($c['type_data'] == 'String')
								<div class="form-group col-sm-6">
								{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name']))) !!}
								{!! Form::text($c['field_name'], (!empty($row->{$c['field_name']}) ? $row->{$c['field_name']} : old($c['field_name'])),
																				['class' => 'form-control  validate-hidden','maxlength' => 255,'maxlength' => 255,
																				'data-rule-required' => '',
																				'id' => $c['field_name'],
																				"placeholder" => ucwords(str_replace("_"," ",$c['field_name'])),
																				'data-msg-required' => 'Required Fields',
								]) !!}
								</div>
							@elseif ($c['type_data'] == 'Text')
								<div class="form-group col-sm-12">
								{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name']))) !!}
								{!! Form::textarea($c['field_name'], (!empty($row->{$c['field_name']}) ? $row->{$c['field_name']} : old($c['field_name'])), ['class' => 'form-control  validate-hidden',
																					'data-rule-required' => '',
																					'id' => $c['field_name'],
																					"placeholder" => ucwords(str_replace("_"," ",$c['field_name'])),
																					'data-msg-required' => 'Required Fields',
																					'rows'=>3,
									]) !!}

								</div>
							@elseif ($c['type_data'] == 'Integer')
								<div class="form-group col-sm-6">
									{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name']))) !!}
									{!! Form::number($c['field_name'], (!empty($row->{$c['field_name']}) ? $row->{$c['field_name']} : old($c['field_name'])),
																						['class' => 'form-control  validate-hidden',
																						'data-rule-required' => '',
																						'id' => $c['field_name'],
																						"placeholder" => ucwords(str_replace("_"," ",$c['field_name'])),
																						'data-msg-required' => 'Required Fields',]
																						+(empty($c['attribute'])?[]:$c['attribute'])
																						) !!}
								</div>

							@elseif ($c['type_data'] == 'timepicker')

								<div class="form-group">
									<label for="{{$c['field_name']}}" class=" col-md-3">{{ucwords(str_replace("_"," ",$c['field_name']))}}</label>
									<div class="col-md-9">
									{!! Form::text($c['field_name'], (!empty($row->{$c['field_name']}) ? $row->{$c['field_name']} : old($c['field_name'])), ['class' => 'timepicker form-control  validate-hidden',
																						'data-rule-required' => '',
																						'id' => $c['field_name'],
																						"placeholder" => ucwords(str_replace("_"," ",$c['field_name'])),
																						'data-msg-required' => 'Required Fields',
										]) !!}
									</div>
								</div>

							@elseif ($c['type_data'] == 'Editor')
								<div class="form-group col-sm-12">
									{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name'])).':') !!}
									<textarea id="editor" class="form-control" name="{{$c['field_name']}}" rows="10" cols="50"></textarea>
								</div>

							@elseif ($c['type_data'] == 'Date')
								<div class="form-group col-sm-6">
								<?php
								$value = (!empty($row->{$c['field_name']}) ? $row->{$c['field_name']} : old($c['field_name']));
								$new_value = date_format_garing($value);
								?>
								{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name']))) !!}
								{!! Form::text($c['field_name'], $new_value, ['class' => 'form-control  validate-hidden',
																					'data-rule-required' => '',
																					'id' => $c['field_name'],
																					"placeholder" => ucwords(str_replace("_"," ",$c['field_name'])),
																					'data-msg-required' => 'Required Fields',
									]) !!}
								</div>
								<script>
								$(document).ready( function () {
									$('#{{$c['field_name']}}').datepicker({
										//dateFormat: 'yy-mm-dd'			// show date format mm/dd/yy.
										dateFormat: 'dd/mm/yy'
									});
								});
								</script>
							@elseif ($c['type_data'] == 'datetime')
								<?php

									//$value = (!empty($row->{$c['field_name']}) ? $row->{$c['field_name']} : old($c['field_name']));
									$value = (!empty($c['value']) ? $c['value'] : "");
									//echo $value;

									$date_value = "";
									$time_value = "";
									if(!empty($value)) {
										$v = explode(" ",trim($value));
										 $date_value = date_format_garing($v[0]);
										 $time_value = $v[1];
									}
								?>
								<div class="form-group">
									<label class=" col-md-3">{{ucwords(str_replace("_"," ",$c['field_name']))}}</label>
									<div class="col-md-9">
										{!! Form::text($c['field_name']."_datepart", $date_value, ['class' => 'form-control  validate-hidden',
																							'data-rule-required' => '',
																							'id' => $c['field_name']."_datepart",
																							"placeholder" => "Date",
																							'data-msg-required' => 'Required Fields',
											]) !!}
										<input name="{{$c['field_name'].'_timepart'}}" type="text" value="{{$time_value}}" class="time_select timepicker form-control  validate-hidden" placeholder="Time" style="margin-top:10px"/>
									</div>
								</div>
								<script>
								$(document).ready( function () {
									$('#{{$c['field_name']."_datepart"}}').datepicker({
										//dateFormat: 'yy-mm-dd'			// show date format mm/dd/yy.
										dateFormat: 'dd/mm/yy'
									});
								});
								$('.timepicker').timepicker({
									timeFormat: 'HH:mm',
									interval: 60,
									//minTime: '10',
									//maxTime: '6:00',
									//defaultTime: '11',
									//startTime: '10:00',
									dynamic: false,
									dropdown: true,
									scrollbar: true,
								});
								</script>

							@elseif ($c['type_data'] == 'Autocomplete User')
								<div class="form-group col-sm-6">
									{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name'])).':') !!}
									<select class="cari form-control" style="" name="{{$c['field_name']}}">
										@php
										{{
											$member_id = (!empty($row->{$c['field_name']}) ? $row->{$c['field_name']} : old($c['field_name']));
											$user = DB::table('users')->where('id',$member_id)->first();
											if($user) {
												$title_name = $user->name.' - '.$user->email;
												echo '<option value="'.(!empty($row->{$c['field_name']}) ? $row->{$c['field_name']} : old($c['field_name'])).'" checked>'.$title_name.'</option>';
											} else {

											}
										}}
										@endphp
									</select>
								</div>
							@elseif ($c['type_data'] == 'upload file')
								<div class="form-group col-sm-6">
									{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name']))) !!}
									@if(!empty($row->{$c['field_name']}))
										@php
										{{
											$f = $row->{$c['field_name']};
											if(is_image($f)){
												echo '<p><a target="_blank" href="'.url('/uploads/'.$f).'"><img src="'.url('/uploads/'.$f).'" style="width:200px"/></a></p>';
											} else {
												echo '<p><a target="_blank" href="'.url('/uploads/'.$f).'">'.$f.'</a></p>';
											}
										}}
										@endphp
										Upload Baru
									@endif
									<input type="file" name="{{$c['field_name']}}" >

								</div>
							@elseif ($c['type_data'] == 'upload multiple file')
								<div class="form-group">
									<label for="{{$c['field_name']}}" class=" col-md-3">{{ucwords(str_replace("_"," ",$c['field_name']))}}</label>
									<div class="col-md-9">
									@if(!empty($row->{$c['field_name']}))
										@php
										{{
											$list_file = explode(", ",$row->{$c['field_name']});
											foreach($list_file as $f){
												if(is_image($f)){
													echo '<p><a target="_blank" href="'.url('/uploads/'.$f).'"><img src="'.url('/uploads/'.$f).'" style="width:200px"/></a></p>';
												} else {
													echo '<p><a target="_blank" href="'.url('/uploads/'.$f).'">'.$f.'</a></p>';
												}
											}
										}}
										@endphp

										<p><b><i>Jika Anda mengupload file yang baru. maka file yang terupload sebelumnya akan hilang !</i></b></p>
										Upload Baru
									@endif
									<input type="file" name="{{$c['field_name']}}[]" multiple>
									Anda bisa memilih lebih dari satu file sekaligus
									</div>
								</div>

						@elseif ($c['type_data'] == 'select')
								<?php
								$select_field = [""=>"-Select ".ucwords(str_replace("_"," ",$c['field_name']))."-"];
								if(is_array($c['option'])) {
									//array
									$new_option = $select_field + $c['option'];
								} else {
									//collection
									$new_option = $select_field + $c['option']->toArray();
								}
								?>
								<div class="col-md-6 form-group mb-3">
									{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name'])).':') !!}
									{!! Form::select($c['field_name'], $new_option, null, ['class' => 'select2 form-control']) !!}
								</div>

						@elseif ($c['type_data'] == 'select_and_add')
								<?php
								$identifier =  rand(pow(10, 12-1), pow(10, 12)-1);
								$id_select = "select-".$identifier;

								$select_field = [""=>"-Select ".ucwords(str_replace("_"," ",$c['field_name']))."-"];
								if(is_array($c['option'])) {
									//array
									$new_option = $select_field + $c['option'];
								} else {
									//collection
									$new_option = $select_field + $c['option']->toArray();
								}
								?>
								<div class="col-md-5 form-group mb-3">
									{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name'])).':') !!}
									{!! Form::select($c['field_name'], $new_option, null, ['class' => 'select2 form-control','id'=>$id_select]) !!}

								</div>
								<div class="col-md-1 form-group mb-3" >
									<button data-select-target="{{$id_select}}" data-target="{{isset($c['data-target'])?$c['data-target']:$c['field_name']}}" type="button" class="modal-add btn btn-white-line btn-dark-outline"
								style="height: 37px;
										margin-top: 25px;
										padding-top: 10px;
										text-align: center;
										width: 43px;
										color: #6b6b94;
										font-weight: 500;
										font-size: 19px;
										line-height: 0.5;
										border: 1px solid #bbbdc7 !important;">+</button>
								</div>
						@elseif ($c['type_data'] == 'request_type_name')
							<?php
							$option = DB::table('request_type')->pluck('request_type_name', 'request_type_name')->toArray();
							$option = [""=>"-Select Request Type-"]+ $option;
							?>
							<div class="form-group col-sm-6">
								{!! Form::label('request_type', 'Request Type') !!}
								{!! Form::select('request_type', $option, null, ['class' => 'select2 form-control','id'=>'select-request_type']) !!}
							</div>
							<div class="form-group col-sm-6">
								{!! Form::label('request_name', 'Request Name') !!}
								{!! Form::select('request_name', [""=>"-Select Request Name-"], $berkas->request_name, ['class' => 'select2 form-control','id'=>'request_name']) !!}
							</div>
							<script>
								$.ajax({
									type: "GET",
									url: '{{URL("/")}}/select2list/service?request_type='+$('#select-request_type').val(),
									//data: data_input,
									//dataType: 'json',
									success: function(data){
										console.log(data);
										var obj = JSON.parse(data);
										$('#request_name').select2('data', null);
										 $('#request_name').select2('data', {id: null, text: null});
										 $('#request_name').off('select2:select');
										 $('#request_name').select2('destroy');
										 $('#request_name').html("");
										$('#request_name').select2({
										  data: obj
										});
										$('#request_name').val({{$berkas->request_name}}).trigger('change');
									},
									error: function(){console.log("error");}
								});
								$('#select-request_type').on("change", function(e) {
									$.ajax({
										type: "GET",
										url: '{{URL("/")}}/select2list/service?request_type='+$(this).val(),
										//data: data_input,
										//dataType: 'json',
										success: function(data){
											console.log(data);
											var obj = JSON.parse(data);
											$('#request_name').select2('data', null);
											 $('#request_name').select2('data', {id: null, text: null});
											 $('#request_name').off('select2:select');
											 $('#request_name').select2('destroy');
											 $('#request_name').html("");
											$('#request_name').select2({
											  data: obj
											})
										},
										error: function(){console.log("error");}
									});
								   console.log("select");
								   //console.log($('#select-request_type').val());
								});
								//$('#select-request_type').change();
							</script>
						@elseif ($c['type_data'] == 'company_location')
							<?php
							$value = $berkas->location.' - '.$berkas->company;
							$option = DB::table('company')
										->join('location', 'location.company', '=', 'company.id')
										->selectRaw('CONCAT(location.name," - ",company.name) AS company_location
													,CONCAT(location.id," - ",company.id) AS ids')
										->pluck( 'company_location','ids')
										->toArray();

							$option = [""=>"-Select Location-"]+ $option;
							?>
							<div class="form-group col-sm-6">
								{!! Form::label('location', 'Location') !!}
								{!! Form::select('location', $option, $value, ['class' => 'select2 form-control','id'=>'select-company_location']) !!}
							</div>
						@elseif ($c['type_data'] == 'pick_title_name')
								<?php

								//PICK TITLE
								$c['data-target'] = 'job_title';
								$c['option'] = DB::table('job_title')->pluck('job_name', 'id');

								$identifier =  rand(pow(10, 12-1), pow(10, 12)-1);
								$id_select = "select-".$identifier;

								$select_field = [""=>"-Select ".ucwords(str_replace("_"," ",$c['field_name']))."-"];
								if(is_array($c['option'])) {
									//array
									$new_option = $select_field + $c['option'];
								} else {
									//collection
									$new_option = $select_field + $c['option']->toArray();
								}
								?>
								<div class="col-md-6 form-group mb-3">
									{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name'])).':') !!}
									{!! Form::select($c['field_name'], $new_option, null, ['class' => 'select2 form-control','id'=>$id_select]) !!}
								</div>
								<div class="detect-name-{{$identifier}}"></div>
<!--
								<div class="col-md-1 form-group mb-3" >
									<button data-select-target="{{$id_select}}" data-target="{{isset($c['data-target'])?$c['data-target']:$c['field_name']}}" type="button" class="modal-add btn btn-primary" style="margin-top: 21px;padding-top: 10px;"><i class="fas fa-plus"></i></button>
								</div>
-->
						@elseif ($c['type_data'] == 'pick_title_plus')

								<?php

								//PICK TITLE
								//$c['data-target'] = 'job_title';
								//$c['option'] = DB::table('job_title')->pluck('job_name', 'id');

								$select_field = [""=>"-Select ".ucwords(str_replace("_"," ",$c['field_name']))."-"];
								if(is_array($c['option'])) {
									//array
									$new_option = $select_field + $c['option'];
								} else {
									//collection
									$new_option = $select_field + $c['option']->toArray();
								}

								if(!empty($berkas->{$c['field_name']})){
									$list_ids = explode(",",$berkas->{$c['field_name']});
									?>
										@foreach($list_ids as $v)
											<?php
											//DB::table('job_title')->where('id',$v)->first();
											//echo $v;
											$identifier =  rand(pow(10, 12-1), pow(10, 12)-1);
											$id_select = "select-".$identifier;
											?>
											<div class="col-md-6 form-group mb-3" style="width:100%">
												{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name'])).':',['class'=>'label-'.$c['field_name']]) !!}
												{!! Form::select($c['field_name']."[]", $new_option, $v, ['class' => 'select2 form-control','id'=>$id_select]) !!}
											</div>
										@endforeach
										<?php
								}
									$identifier =  rand(pow(10, 12-1), pow(10, 12)-1);
									$id_select = "select-".$identifier;
									?>
									<div class="col-md-6 form-group mb-3" style="width:100%">
										{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name'])).' :',['class'=>'label-'.$c['field_name']]) !!}
										{!! Form::select($c['field_name']."[]", $new_option, null, ['class' => 'select2 form-control','id'=>$id_select]) !!}
									</div>

								<div class="col-md-1 form-group mb-3" >
									<button data-target="{{$c['data-target']}}" data-field_title="{{ucwords(str_replace("_"," ",$c['field_name']))}}" data-field_name="{{$c['field_name']}}" type="button" class="add-more btn btn-success" style="margin-top: 21px;padding-top: 10px;"><i class="fas fa-plus"></i></button>
								</div>

								<script>
								$( document ).ready(function() {
									var counting = 1;
									$('.label-{{$c["field_name"]}}').each(function(i, obj) {
										console.log($(this).html());
										$(this).html("{{ucwords(str_replace("_"," ",$c['field_name']))}} "+counting+" :");
										//test
										counting++;
									});
								});
								</script>
						@elseif ($c['type_data'] == 'assignment_tier')
								<?php
								$c['option'] = DB::table('contact')->where('type','Team')->pluck('name','id')->toArray();

								$identifier =  rand(pow(10, 12-1), pow(10, 12)-1);
								$id_select = "select-".$identifier;

								$select_field = [""=>"-Select Team-"];
								if(is_array($c['option'])) {
									//array
									$new_option = $select_field + $c['option'];
								} else {
									//collection
									$new_option = $select_field + $c['option']->toArray();
								}

								$c['option'] = DB::table('assignment_type')->pluck('name','id')->toArray();

								$select_field = [""=>"-Select Assignment Type-"];
								if(is_array($c['option'])) {
									//array
									$new_option2 = $select_field + $c['option'];
								} else {
									//collection
									$new_option2 = $select_field + $c['option']->toArray();
								}

								if(!empty($berkas->assignment_tier)) {
									$assignment_tier = explode(",",$berkas->assignment_tier);
									$assignment_type = explode(",",$berkas->assignment_type);
									$escalation_time = explode(",",$berkas->escalation_time);
									$escalation_unit = explode(",",$berkas->escalation_unit);
									for($i=0;$i<count($assignment_tier);$i++) {
										?>

										<div class="col-md-3 form-group mb-3">
											{!! Form::label('assignment_tier', 'Assignment Tier :',['class'=>'label-assignment_tier']) !!}
											{!! Form::select('assignment_tier[]', $new_option, $assignment_tier[$i], ['class' => 'select2 form-control']) !!}
										</div>

										<div class="col-md-3 form-group mb-3">
											{!! Form::label('assignment_type', 'Assignment Type :') !!}
											{!! Form::select('assignment_type[]', $new_option2, $assignment_type[$i], ['class' => 'select2 form-control']) !!}
										</div>

										<div class="form-group col-sm-3">
											{!! Form::label('escalation_time', 'Escalation Time') !!}
											{!! Form::number('escalation_time[]', $escalation_time[$i],
																								['class' => 'form-control  validate-hidden',
																								'data-rule-required' => '',
																								'id' => $c['field_name'],
																								"placeholder" => 'Escalation Time',
																								'data-msg-required' => 'Required Fields',]
																								) !!}

										</div>
										<div class="col-md-3 form-group mb-3">
											{!! Form::label('escalation_unit', 'Escalation Unit:') !!}
											{!! Form::select('escalation_unit[]', ['Hours'=>'Hours','Days'=>'Days'], $escalation_unit[$i], ['class' => 'select2 form-control']) !!}
										</div>

									<?php }
								}
								?>
								<div class="col-md-3 form-group mb-3">
									{!! Form::label('assignment_tier', 'Assignment Tier :',['class'=>'label-assignment_tier']) !!}
									{!! Form::select('assignment_tier[]', $new_option, null, ['class' => 'select2 form-control']) !!}
								</div>
								<div class="col-md-3 form-group mb-3">
									{!! Form::label('assignment_type', 'Assignment Type :') !!}
									{!! Form::select('assignment_type[]', $new_option2, null, ['class' => 'select2 form-control']) !!}
								</div>
								<div class="form-group col-sm-3">
									{!! Form::label('escalation_time', 'Escalation Time') !!}
									{!! Form::number('escalation_time[]', '',
																						['class' => 'form-control  validate-hidden',
																						'data-rule-required' => '',
																						'id' => $c['field_name'],
																						"placeholder" => 'Escalation Time',
																						'data-msg-required' => 'Required Fields',]
																						) !!}

								</div>
								<div class="col-md-3 form-group mb-3">
									{!! Form::label('escalation_unit', 'Escalation Unit:') !!}
									{!! Form::select('escalation_unit[]', ['Hours'=>'Hours','Days'=>'Days'], null, ['class' => 'select2 form-control']) !!}
								</div>
								<div class="col-md-1 form-group mb-3" >
									<button type="button" class="add-tier btn btn-success" style="margin-top: 21px;padding-top: 10px;"><i class="fas fa-plus"></i></button>
								</div>

								<div class="tmp-tier" style="display:none">
									<div class="col-md-3 form-group mb-3">
										{!! Form::label('assignment_tier', 'Assignment Tier :',['class'=>'label-assignment_tier']) !!}
										{!! Form::select('assignment_tier[]', $new_option, null, ['class' => 'form-control','id'=>'select_tier_id']) !!}
									</div>
									<div class="col-md-3 form-group mb-3">
										{!! Form::label('assignment_type', 'Assignment Type :') !!}
										{!! Form::select('assignment_type[]', $new_option2, null, ['class' => 'form-control','id'=>'select_assignment_type_id']) !!}
									</div>
									<div class="form-group col-sm-3">
										{!! Form::label('escalation_time', 'Escalation Time') !!}
										{!! Form::number('escalation_time[]', '',
																							['class' => 'form-control  validate-hidden',
																							'data-rule-required' => '',
																							'id' => $c['field_name'],
																							"placeholder" => 'Escalation Time',
																							'data-msg-required' => 'Required Fields',]
																							) !!}

									</div>
									<div class="col-md-3 form-group mb-3">
										{!! Form::label('escalation_unit', 'Escalation Unit:') !!}
										{!! Form::select('escalation_unit[]', ['Hours'=>'Hours','Days'=>'Days'], null, ['class' => ' form-control','id'=>'select_escalation_id']) !!}
									</div>
								</div>

								<script>
								$( document ).ready(function() {
									var counting = 1;
									$('.label-assignment_tier').each(function(i, obj) {
										console.log($(this).html());
										$(this).html("Assignment Tier "+counting+" :");
										//test
										counting++;
									});
								});
								</script>
						@elseif ($c['type_data'] == 'title_separator')
								<div class="col-md-12 separator separator-dashed my-10"></div>
								<h3 class="col-md-12 text-dark font-weight-bold mb-10">{{$c['title']}}</h3>
						@elseif ($c['type_data'] == 'title')
								<br/><br/>
								<h3 class="col-md-12 text-dark font-weight-bold mb-10">{{$c['title']}}</h3>
						@elseif ($c['type_data'] == 'clear_space')
								<div style="width: 100%;"></div>


								@elseif ($c['type_data'] == 'combotree')
									<div class="form-group col-sm-6">
									{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name']))) !!}
									{!! Form::text($c['field_name'], null, ['class' => 'form-control validate-hidden easyui-combotree','maxlength' => 255,'maxlength' => 255,
																						'data-rule-required' => '',
																						'id' => $c['field_name'],
																						"placeholder" => ucwords(str_replace("_"," ",$c['field_name'])),
																						'data-msg-required' => 'Required Fields',
																						'data-options' => "url:'".URL('/')."/get_faq_category',method:'get'",
																						'style' => "width:100%"
									]) !!}
									</div>
								@else

							@endif

						@endforeach


			</div>

	</div>

	<div class="">

	</div>
</div>
{!! Form::close() !!}

@endsection
@section('js')
<script>
    $(document).ready(function () {
        @if($type == 'profile')
            $('input, select, :radio, textarea').prop('disabled', true);
            $('.submit-save]').hide();
        @endif
    });
</script>
@endsection
