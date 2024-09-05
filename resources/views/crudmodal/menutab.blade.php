<style>
.nav-tabs2.nav-tabs-line .nav-link {
	background:white;
}
.nav-tabs2 {
	margin-bottom: 25px;
	margin-top: -15px;
}
</style>
<nav>
  <div class="nav nav-tabs2 nav-tabs nav-tabs-line" id="nav-tab" role="tablist">
    <button class="nav-item nav-home nav-link active" id="nav-home-tab" data-toggle="tab" data-bs-toggle="tab" href="#nav-home" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Properties</button>
    
  @foreach($menu_relation as $m)
	
	@if($type == 'team' && $m == 'person')
		<button class="nav-other nav-link" id="nav-{{$m}}-tab" 				data-toggle="tab" data-bs-toggle="tab"  href="#nav-{{$m}}" data-bs-target="#nav-{{$m}}" type="button" role="tab" aria-controls="nav-{{$m}}" aria-selected="false">{{t('Member')}}</button>
	@else
		<button class="nav-other nav-link" id="nav-{{$m}}-tab" 				data-toggle="tab" data-bs-toggle="tab"  href="#nav-{{$m}}" data-bs-target="#nav-{{$m}}" type="button" role="tab" aria-controls="nav-{{$m}}" aria-selected="false">{{t($m)}}</button>
	@endif
  @endforeach
    
  </div>
</nav>
<div class="tab-content mt-3 tab-content-other" id="nav-tabContent">
  <div class="tab-pane fade" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab"></div>
  
  
@foreach($menu_relation as $m)
	<div class="tab-pane fade" id="nav-{{$m}}" role="tabpanel" aria-labelledby="nav-{{$m}}-tab">
					<p> All the {{t($m)}} for this {{t($type)}}</p>
	
					<?php
					//var_dump($m);
						$column = App\Http\Controllers\CrudModalController::getArrayColumNameFromTableForCreateAndEdit($m);
						if($m == "customer_contract") {
							$column2 = App\Http\Controllers\CrudModalController::getArrayColumNameFromTableForCreateAndEdit('contract');
							$column = $column2;
						}
						
						if ($type == 'person' && $m == 'team') {
							$column = ["name"=>"","organization_name"=>"","email"=>"","status"=>""];
						}
						if ($type == 'team' && $m == 'person') {
							$column = ["name"=>"","organization_name"=>"","email"=>"","status"=>""];
						}
						if ($type == 'employee' && $m == 'team') {
							$column = ["name"=>"","organization_name"=>"","email"=>"","status"=>""];
						}
						if ($type == 'team' && $m == 'employee') {
							$column = ["name"=>"","organization_name"=>"","email"=>"","status"=>""];
						}
						if ($m == 'location') {
							$column = ["name"=>"","address"=>"","postal_code"=>"","city"=>"","country"=>""];
						}

						if ($m == 'contact') {
							$column = ["name"=>"","status"=>"","organization"=>"","email"=>"","phone"=>"","function"=>""];
						}

						if ($m == 'document') {
							$column = ["name"=>"","organization"=>"","status"=>"","document_type"=>"","description"=>""];
						}

						if ($m == 'provider_contract') {
							$column = ["name"=>"","status"=>"","organization"=>"","description"=>"","start_date"=>"","end_date"=>"","provider"=>"","sla"=>"","service_hour"=>""];
						}

						// asset management CMDB
						if (in_array($type, DB::table('asset_management')->pluck('code')->toArray())) {
							if ($m == 'application_solution' || $m == 'business_process') {
								$column = ["name"=>"","organization"=>"","business_criticity"=>"","move2production"=>""];
							}

							if ($m == 'middleware_instance' || $m == 'database_schema') {
								$column = ["name"=>"","organization"=>"","business_criticity"=>"","move2production"=>"","description"=>""];
							}
						}

						if (in_array($type, ['incident_management'])) {
							if ($m == 'child_incident') {
								$column = ["referensi"=>"","title"=>"","organization"=>"","caller"=>"","start_date"=>"","status"=>"","agent"=>""];
							}

							if ($m == 'asset') {
								$column = ["name"=>"","organization"=>"","business_criticity"=>"","move_to_production"=>""];
							}
						}

						//$sm = \DB::getDoctrineSchemaManager();
						//$list_column = $sm->listTableColumns($m);
						//$column = array();
						
						//$column_list = Schema::getColumnListing($type);
						////var_dump($column_list);
						//foreach ($list_column as $c) {
							////echo $column->getName() . ': ' . $column->getType() . "\n";
							//$key = $c->getName();
							//if(!in_array($key,array('json_limit','kabupaten_kota','kecamatan','provinsi','telah_diapprove','id','desa_id','penduduk','approve_1','approve_2','approve_3','approve_4','approve_5','approve_6','tanggal_approve_1','tanggal_approve_2','tanggal_approve_3','tanggal_approve_4','tanggal_approve_5','tanggal_approve_6','created_at','updated_at','deleted_at','penandatanganan','tandatangan_camat','tanggal_approve'))) {
								
								//$column[$key] = array("field_name"=>$key,"type_data"=>ucfirst($c->getType()->getName()));
								
							//}
						//}
						

						$table = $type;
						
						$result = [];
						
						
						
						
						if(isset($berkas->id)) {
							//GET DATA FROM TABEL RELASI
							if ($type == 'person' && $m == 'team') {
								$result = DB::table('lnkpersontoteam')
											->select('contact.*','organization.name AS organization_name')
											->join('contact', 'contact.id', '=', 'lnkpersontoteam.team_id')
											->leftJoin('organization', 'organization.id', '=', 'contact.organization')
											->where('lnkpersontoteam.person_id',$berkas->id)->get();
								//var_dump($result);
							} else if ($type == 'employee' && $m == 'team') {
								$result = DB::table('lnkemployeetoteam')
											->select('contact.*','organization.name AS organization_name')
											->join('contact', 'contact.id', '=', 'lnkemployeetoteam.team_id')
											->leftJoin('organization', 'organization.id', '=', 'contact.organization')
											->where('lnkemployeetoteam.employee_id',$berkas->id)->get();

							} else if ($type == 'team' && $m == 'person') {
								$result = DB::table('lnkpersontoteam')
											->select('contact.*','organization.name AS organization_name')
											->join('contact', 'contact.id', '=', 'lnkpersontoteam.person_id')
											->leftJoin('organization', 'organization.id', '=', 'contact.organization')
											->where('lnkpersontoteam.team_id',$berkas->id)->get();
							} else if ($type == 'team' && $m == 'employee') {
								$result = DB::table('lnkemployeetoteam')
											->select('contact.*','organization.name AS organization_name')
											->join('contact', 'contact.id', '=', 'lnkemployeetoteam.employee_id')
											->leftJoin('organization', 'organization.id', '=', 'contact.organization')
											->where('lnkemployeetoteam.team_id',$berkas->id)->get();
							} else if ($type == 'team' && $m == 'ticket') {
								$result = DB::table('lnkcontacttoticket')
											->join('ticket', 'ticket.id', '=', 'lnkcontacttoticket.ticket_id')
											->where('lnkcontacttoticket.contact_id',$berkas->id)->get();	
							} else if ($type == 'person' && $m == 'ticket') {
								$result = DB::table('lnkcontacttoticket')
											->join('ticket', 'ticket.id', '=', 'lnkcontacttoticket.ticket_id')
											->where('lnkcontacttoticket.contact_id',$berkas->id)->get();	
							} else if ($type == 'employee' && $m == 'ticket') {
								$result = DB::table('lnkcontacttoticket')
											->join('ticket', 'ticket.id', '=', 'lnkcontacttoticket.ticket_id')
											->where('lnkcontacttoticket.contact_id',$berkas->id)->get();	
							} else if ($type == 'person' && $m == 'functional_ci') {
								$result = DB::table('lnkcontacttofunctional_ci')
											->join('functional_ci', 'functional_ci.id', '=', 'lnkcontacttofunctional_ci.functional_ci_id')
											->where('lnkcontacttofunctional_ci.contact_id',$berkas->id)->get();	
							} else if ($type == 'employee' && $m == 'functional_ci') {
								$result = DB::table('lnkcontacttofunctional_ci')
											->join('functional_ci', 'functional_ci.id', '=', 'lnkcontacttofunctional_ci.functional_ci_id')
											->where('lnkcontacttofunctional_ci.contact_id',$berkas->id)->get();	
							} else if ($type == 'team' && $m == 'functional_ci') {
								$result = DB::table('lnkcontacttofunctional_ci')
											->join('functional_ci', 'functional_ci.id', '=', 'lnkcontacttofunctional_ci.functional_ci_id')
											->where('lnkcontacttofunctional_ci.contact_id',$berkas->id)->get();	
							} else if ($type == 'team' && $m == 'location') {
								$result = DB::table('lnkcontacttolocation')
											->join('location', 'location.id', '=', 'lnkcontacttolocation.location_id')
											->where('lnkcontacttolocation.contact_id',$berkas->id)->get();	
								
							} else {
								//DETEKSI TABEL RELASI
								//NAMA TABEL RELASI SEPERTI lnkslatoslt
								//INI bisa kebalikan namanya harus deteksi nama tabel relasi							
								if(Schema::hasTable("lnk".$table."to".$m)) {//check table exist
									$table_relation = "lnk".$table."to".$m;
								} else if(Schema::hasTable("lnk".$m."to".$table)) {//check table exist
									$table_relation = "lnk".$m."to".$table;
								} else {
									
									if (in_array($table, DB::table('asset_management')->pluck('code')->toArray())) {
										
										$table_relation = "lnk".$m."toasset";

									} else if (in_array($table, ['incident_management'])) {
										
										$table_relation = "lnk".$m."toticket";

									} else {
										echo "table relation not found";
										echo "lnk".$m."to".$table;
										die;
									}
								}
								$result = [];
								if (str_contains($table_relation, 'customer_contract')) {
								//if($table_relation == "lnkcustomer_contracttosla") {
									//var_dump("masu");
									$result = DB::table($table_relation)
												->join($m, $m.'.id', '=', $table_relation.'.'.$m.'_id')
												->join('contract', 'contract.id', '=', 'customer_contract.id')
												->where($table."_id",$berkas->id)->get();								
								} else if (in_array($table, DB::table('asset_management')->pluck('code')->toArray())) {
									//
									if(in_array($m, DB::table('asset_management')->pluck('code')->toArray())) {
										//kondisional relasi ke internal modul asset (contoh application_solution ke contact)
										//echo "masuk2";die;
										$result = DB::table($table_relation)
													->join($m, $m.'.id', '=', $table_relation.'.'.$m.'_id')
													->join('asset', 'asset.id', '=', $m.'.'.'id')
													->join('organization AS o', 'o.id', '=', 'asset.'.'org_id')
													->select($m.'.*','o.name as organization','asset.*')
													->where($table."_id",$berkas->id)->get();
										
									} else {
										//echo "masuk1";die;
										
										//kondisional relasi ke eksternal modul asset (contoh application solution to business process
										$result = DB::table($table_relation)
													->join($m, $m.'.id', '=', $table_relation.'.'.$m.'_id')
													->where("asset_id",$berkas->id)->get();
									}
															
								} else if(in_array($table, ['incident_management'])) {
									if ($m == "child_incident") {
										$result = DB::table('ticket')
										->select(
											DB::raw('ticket.id, ticket.ref as referensi, ticket.title, organization.name AS organization, contact.name as caller, ticket.start_date, ticket.operational_status as status, c.name as agent')
										)->leftJoin('organization', 'organization.id', '=', 'ticket.org_id')
										->leftJoin('contact', 'contact.id', '=', 'ticket.caller_id')
										->leftJoin('contact as c', 'c.id', '=', 'ticket.agent_id')
										->where("ticket.parent_id",$berkas->id)->get();
									} else if ($m == "asset") {
										$result = DB::table('lnktickettoasset')
													->join($m, $m.'.id', '=', 'lnktickettoasset.'.$m.'_id')
													->where("asset_id",$berkas->id)->get();
									} else {
										$result = DB::table($table_relation)
												->join('ticket', 'ticket.id', '=', $table_relation.'.ticket_id')
												->join($m, $m.'.id', '=', $table_relation.'.'.$m.'_id')
												->join('organization AS o', 'o.id', '=', 'ticket.'.'org_id')
												->select($m.'.*','o.name as organization','ticket.*')
												->where($table_relation.".ticket_id",$berkas->id)->get();
									}
								}  else {
									$result = DB::table($table_relation)
												->join($m, $m.'.id', '=', $table_relation.'.'.$m.'_id')
												->where($table."_id",$berkas->id)->get();								
								}
							}
							//var_dump($m. $m.'.id'. '='. $table_relation.'.'.$m.'_id');
							//var_dump($table."_id".$berkas->id);
							//var_dump($result);
						}
						
					?>
					<?php
						$identifier_table =  rand(pow(10, 12-1), pow(10, 12)-1);
						$id_table = "table-".$identifier_table; 
					?>
					<div  style="overflow-x: scroll;">
						<table class="table table-bordered" >
							<thead>
								<th>
									<input type="checkbox" class="ml-2 list-checkAll" id="check-all" name="check-all" value="check-all">
								</th> 
								<th>
									ID
								</th>
								@foreach($column as $key=> $val)
								<th>
									{{t($key)}}
								</th>
								@endforeach
								
							</thead>
							<tbody class="tbody-{{$m}}" id="{{$id_table}}">
								<?php $n = 1;?>
								@foreach($result as $row)
									<tr>
										<td>
											<input type="hidden" name="lnk-{{$m}}[]" value="{{$row->id}}" />
											<input class="list-checkbox ml-2 list-checkbox-{{$m}}" type="checkbox" name="check_item[]" value="{{$row->id}}">
										</td>
										<td>
											{{$row->id}}
										</td>
										@foreach($column as $key=> $val)
											<td>
												{{$row->{$key} }}
											</td>
										@endforeach
									</tr>
								@endforeach

							</tbody>
						</table>
					</div>
				<br/>
				<!-- Button trigger modal -->
				<button type="button" class="btn btn-default btn-remove-item" > Remove Selected Objects</button>
				<button type="button" class="btn btn-primary modal-add-from-list" data-target-object="{{$m}}" data-target-table="{{$id_table}}">
				  Add {{t($m)}} objects
				</button>

				<!-- Modal -->
<!--
				<div class="modal fade" id="exampleModal-{{$m}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
				  <div class="modal-dialog modal-lg">
					<div class="modal-content modal-add-ajax">

					</div>
				  </div>
				</div>
-->
	</div> <!-- close tab-pane -->
@endforeach

  
  
</div>



<script>

$( document ).ready(function() {
	$(".tab-content-other").hide();
	$(".list-checkAll").click(function(){
		$('.list-checkbox').not(this).prop('checked', this.checked);
	});	
	$( ".nav-home" ).click(function() {
		$(".content-tab-home").show();
		$(".tab-content-other").hide();
	});
	$( ".nav-other" ).click(function() {
		$(".content-tab-home").hide();
		$(".tab-content-other").show();
	});
	@foreach($menu_relation as $m)
		//$( ".btn-add-item" ).click(function() {
			//console.log("click");
			//$.ajax({
				//type: "GET",
				//url: "{{URL('/').'/addItemAjax/'.$m}}",
				////data: "spec="+$( "#spec-select" ).val()+"&product="+$( "#product-select3" ).val()+"&step="+$( "#step-select" ).val()+"&_token={{ csrf_token() }}",
				////dataType: 'json',
				//success: function(data){
					//console.log(data);
					//$(".modal-add-ajax").html(data);
				//},
				//error: function(){console.log("error");}
			//});
		//});
		$( ".btn-remove-item" ).click(function() {
			console.log("click");
			$('input:checkbox.list-checkbox-{{$m}}').each(function () {
				   var sThisVal = (this.checked ? $(this).val() : "");
				   if(sThisVal) {
					   console.log("dicek");
					   $(this).parent().parent().remove();
				   } else {
					   console.log("tidak dicek");
				   }
			});
		});

	
	@endforeach

});
</script>
@section('js')
@endsection

