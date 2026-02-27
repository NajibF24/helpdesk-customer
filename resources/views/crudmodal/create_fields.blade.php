
					@csrf
					@foreach($column as $key => $c)
						<?php $c['field_name'] = $key; //set key as field name ?>
						@if ($c['type_data'] == 'String')
							<div class="form-group col-sm-6">
							{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name']))) !!}
							{!! Form::text($c['field_name'], null, ['class' => 'form-control  validate-hidden','maxlength' => 255,'maxlength' => 255,
																				'data-rule-required' => '',
																				'id' => $c['field_name'],
																				"placeholder" => ucwords(str_replace("_"," ",$c['field_name'])),
																				'data-msg-required' => 'Required Fields',
							]) !!}
							</div>
						@elseif ($c['type_data'] == 'Text')
							<div class="form-group col-sm-12">
							{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name']))) !!}					
							{!! Form::textarea($c['field_name'], null, ['class' => 'form-control  validate-hidden',
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
								{!! Form::number($c['field_name'], null, ['class' => 'form-control  validate-hidden',
																					'data-rule-required' => '',
																					'id' => $c['field_name'],
																					"placeholder" => ucwords(str_replace("_"," ",$c['field_name'])),
																					'data-msg-required' => 'Required Fields',
																		] +(empty($c['attribute'])?[]:$c['attribute'])
																				) !!}
							</div>
						@elseif ($c['type_data'] == 'timepicker')
							<div class="form-group">
								<label for="{{$c['field_name']}}" class=" col-md-3">{{ucwords(str_replace("_"," ",$c['field_name']))}}</label>
								<div class="col-md-9">
								{!! Form::text($c['field_name'], null, ['class' => 'timepicker form-control  validate-hidden',
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
							{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name']))) !!}
							{!! Form::text($c['field_name'], null, ['class' => 'form-control  validate-hidden',
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
							<div class="form-group ">
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
							<div class="form-group col-sm-6">
								{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name'])).':') !!}
								{!! Form::select($c['field_name'], $new_option, null, ['class' => 'select2 form-control','style'=>'width:100%']) !!}
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
								{!! Form::select($c['field_name'], $new_option, null, ['class' => 'select2 form-control','id'=>$id_select,'style'=>'width:100%']) !!}
								
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
								{!! Form::select('request_name', [""=>"-Select Request Name-"], null, ['class' => 'select2 form-control','id'=>'request_name']) !!}
							</div>
							<script>
								
								$('#select-request_type').on("change", function(e) {
									//var data = {
										//id: 1,
										//text: 'Barn owl'
									//};
									
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
											//var newOption = new Option(data.text, data.id, false, false);
											//$('#mySelect2').append(newOption).trigger('change');
										},
										error: function(){console.log("error");}
									});
								   console.log("select");
								   //console.log($('#select-request_type').val());
								});
							</script>
						@elseif ($c['type_data'] == 'company_location')
							<?php 
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
								{!! Form::select('location', $option, null, ['class' => 'select2 form-control','id'=>'select-company_location']) !!}
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
								
								?>
									<?php 
									$identifier =  rand(pow(10, 12-1), pow(10, 12)-1);
									$id_select = "select-".$identifier;
									?>
									<div class="col-md-6 form-group mb-3" style="width:100%">
										<div class="row">
											<div class="col-md-11">
											{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name'])).' :',['class'=>'label-'.$c['field_name']]) !!}
											{!! Form::select($c['field_name']."[]", $new_option, null, ['class' => 'select2 form-control','id'=>$id_select]) !!}
											</div>
											<div class="col-md-1 pl-0">
												<button type="button" title="Delete" data-field_title="{{ucwords(str_replace("_"," ",$c['field_name']))}}" class="mb-0 btn-delete-approval btn btn-sm btn-outline-dark btn-white-line4" style="padding-left: 12px;text-align:center;margin-top: 25px;font-weight: 500;color: #000000;width: 40px;margin-left:2px;border-radius:5px"><i class="flaticon-delete icon-sm text-dark-75" style="font-size: 1rem !important;"></i></button>
											</div>
										</div>
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
									$("body").on('click', '.btn-delete-approval', function(){
										$(this).parent().parent().parent().remove();
										var counting = 1;
										$('.label-{{$c["field_name"]}}').each(function(i, obj) {
											console.log($(this).html());
											$(this).html("{{ucwords(str_replace("_"," ",$c['field_name']))}} "+counting+" :");
											//test
											counting++;
										});
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
								?>
								<div class="row ml-1 mr-1">
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
								<div class="col-md-1 form-group mb-1 pl-0">
									<button type="button" title="Delete" class="mb-0 btn-delete-tier btn btn-sm btn-outline-dark btn-white-line4" style="padding-left: 12px;text-align:center;margin-top: 25px;font-weight: 500;color: #000000;width: 40px;margin-left:2px;border-radius:5px"><i class="flaticon-delete icon-sm text-dark-75" style="font-size: 1rem !important;"></i></button>
								</div>
								</div>
								
								<div class="tmp-tier" style="display:none">
									<div class="row ml-1 mr-1">
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
									<div class="col-md-1 form-group mb-1 pl-0">
										<button type="button" title="Delete" class="mb-0 btn-delete-tier btn btn-sm btn-outline-dark btn-white-line4" style="padding-left: 12px;text-align:center;margin-top: 25px;font-weight: 500;color: #000000;width: 40px;margin-left:2px;border-radius:5px"><i class="flaticon-delete icon-sm text-dark-75" style="font-size: 1rem !important;"></i></button>
									</div>
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
									$("body").on('click', '.btn-delete-tier', function(){
										$(this).parent().parent().remove();
										var counting = 1;
										$('.label-assignment_tier').each(function(i, obj) {
											console.log($(this).html());
											$(this).html("Assignment Tier "+counting+" :");
											//test
											counting++;
										});
									});
								});
								</script>
						@elseif ($c['type_data'] == 'title_separator')
								<div class="col-md-12 separator separator-dashed my-10"></div>
								<h3 class="col-md-12 text-dark font-weight-bold mb-10">{{$c['title']}}</h3>
						@elseif ($c['type_data'] == 'title')
								<br/><br/>
								<h3 class="col-md-12 text-dark font-weight-bold mb-10">{{$c['title']}}</h3>
						@elseif ($c['type_data'] == 'Pilih Schedule')
							<div class="form-group">
								<label for="{{$c['field_name']}}" class=" col-md-3">{{ucwords(str_replace("_"," ",$c['field_name']))}}</label>
								<div class="col-md-9">
									{!! Form::select($c['field_name'], $c['option'], null, ['class' => 'form-control  validate-hidden',
																							'data-rule-required' => '1',
																							'id' => $c['field_name'],
																							"placeholder" => ucwords(str_replace("_"," ",$c['field_name'])),
																							'data-msg-required' => 'Required Fields',]) !!}
								</div>
							</div>
								
			<!--
							<div class="form-group col-sm-4" style="padding-left: 0;">
								<input type="hidden" class="nilai-anggota" name="daftar_user" value="{{$c['daftar_user']}}" />
								<input type="hidden" class="current_id"  value=""/>
								<input type="hidden" class="current_nik" value=""/>
								<input type="hidden" class="current_name"  value=""/>
								{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name'])).':') !!}
								
								<select class="carianggota form-control" style="" name="">
								</select>

							</div>
			-->
							<div class="form-group col-sm-4" style="padding-left: 0;">
								<br/>
								<button type="button" class="tambah-anggota btn btn-info">Tambah</button>
							</div>
							
						@elseif ($c['type_data'] == 'combotree')
							<div class="form-group col-sm-6">
							{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name']))) !!}
							{!! Form::text($c['field_name'], null, ['class' => 'form-control validate-hidden easyui-combotree','maxlength' => 255,'maxlength' => 255,
																				'data-rule-required' => '',
																				'id' => $c['field_name'],
																				"placeholder" => ucwords(str_replace("_"," ",$c['field_name'])),
																				'data-msg-required' => 'Required Fields',
																				'data-options' => "url:'".URL('/')."/get_combo_tree_list?table=".$c['table']."',method:'get'",
																				'style' => "width:100%"
							]) !!}
							</div>
						@elseif ($c['type_data'] == 'StringReadOnly')
							<div class="form-group col-sm-6">
							{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name']))) !!}
							{!! Form::text($c['field_name'], null, ['class' => 'form-control  validate-hidden','maxlength' => 255,'maxlength' => 255,
																				'data-rule-required' => '',
																				'id' => $c['field_name'],
																				"placeholder" => ucwords(str_replace("_"," ",$c['field_name'])),
																				'data-msg-required' => 'Required Fields',
																				'readonly' => 'true'
							]) !!}
							</div>
						@else
						
						@endif

					@endforeach
					<!-- Submit Field -->
